<?php

declare(strict_types=1);

/*
 * This file is part of the pseudify project
 * - (c) 2022 waldhacker UG (haftungsbeschränkt)
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Waldhacker\Pseudify\Profiles\Tests;

use Waldhacker\Pseudify\Core\Processor\Encoder\Base64Encoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\ChainedEncoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\GzEncodeEncoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\HexEncoder;
use Waldhacker\Pseudify\Core\Processor\Processing\Analyze\SourceDataCollectorContext;
use Waldhacker\Pseudify\Core\Processor\Processing\Analyze\TargetDataDecoderContext;
use Waldhacker\Pseudify\Core\Processor\Processing\Analyze\TargetDataDecoderPreset;
use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Profile\Analyze\ProfileInterface;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\SourceColumn;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TargetColumn;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TargetTable;

/*
 * This is an example profile for this dataset:
 * https://github.com/waldhacker/pseudify-profile-templates/blob/0.0.1/tests/mariadb/10.5/pseudify_utf8mb4.sql
 */
class TestAnalyzeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'test-profile';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition(identifier: $this->getIdentifier());

        $tableDefinition
            // Collect values from this database columns and search for it in each other database column
            ->addSourceTable(table: 'wh_user', columns: [
                'username',
                'password',
                'first_name',
                'last_name',
                'email',
                'city',
            ])

            // Collect values from this database columns and search for it in each other database column
            ->addSourceTable(table: 'wh_user_session', columns: [
                // Decode the data from `session_data_json` as JSON (`dataType: SourceColumn::DATA_TYPE_JSON`).
                // The JSON string within the database looks like this `{"data": {"last_ip":"107.66.23.195"}}`.
                SourceColumn::create(identifier: 'session_data_json', dataType: SourceColumn::DATA_TYPE_JSON)
                    // We need tho grab a special value (`last_ip`) from the JSON data so we need a custom `DataProcessing`.
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'extract ip address',
                        processor: function (SourceDataCollectorContext $context): void {
                            // `$data` holds the decoded data structure (an associative array representation of the JSON string).
                            // JSON: `{"data": {"last_ip":"107.66.23.195"}}`
                            // $data: `[
                            //     'data' => [
                            //         'last_ip' => '107.66.23.195',
                            //     ],
                            // ]`

                            // Fetch the decoded data
                            $data = $context->getDecodedData();
                            // Extract the value you want to search for in the database
                            $ip = $data['data']['last_ip'];
                            // Add the search value to the "search data collection"
                            $context->addCollectedData(data: $ip);
                        }
                    )),
            ])

            // Search for this value with each database column
            ->addSourceString(string: 'example.com')
            // Search for this regex with each database column
            ->addSourceString(string: 'regex:(?:[0-9]{1,3}\.){3}[0-9]{1,3}')

            // Exclude database column data types from the search.
            // As a rule, database columns with non string data types do not need to be searched
            ->excludeTargetColumnTypes(columnTypes: TableDefinition::COMMON_EXCLUED_TARGET_COLUMN_TYPES)

            // Add some special configuration for this table
            ->addTargetTable(table: TargetTable::create(identifier: 'wh_log',
                columns: [
                    // Add some spacial configuration for this column.
                    //
                    // Decoding the data from `log_data` from a hexadecimal to a decimal representation (`dataType: TargetColumn::DATA_TYPE_HEX`).
                    TargetColumn::create(identifier: 'log_data', dataType: TargetColumn::DATA_TYPE_HEX)
                        // Under certain conditions the `log_data` are further encoded so we need a custom `DataProcessing`.
                        ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'decode conditional log data',
                            processor: function (TargetDataDecoderContext $context): void {
                                $row = $context->getDatebaseRow();
                                // If the database column "log_type" is not "foo",
                                // we know that the data is already in plaintext,
                                // so we do not need any further decoding.
                                if ('foo' !== $row['log_type']) {
                                    return;
                                }

                                // Fetch the decoded data
                                $data = $context->getDecodedData();

                                // Decode the data one more time with base64 so we get the plaintext
                                $encoder = new Base64Encoder();
                                $logData = $encoder->decode(data: $data);

                                // Add the plaintext value to the "search data collection"
                                $context->setDecodedData(decodedData: $logData);
                            }
                        )),

                    // Add some spacial configuration for this column.
                    //
                    // `log_message` contains a JSON string.
                    // However, UTF characters are encoded in JSON strings
                    // and therefore are not meaningfully searchable
                    // (e.g. `Ö` is masked as `\u00d6`).
                    // Use the `TargetDataDecoderPreset::normalizedJsonString()` `DataProcessing`
                    // to make JSON strings searchable.
                    TargetColumn::create(identifier: 'log_message')->addDataProcessing(dataProcessing: TargetDataDecoderPreset::normalizedJsonString()),
                ]
            ))

            // Add some special configuration for this table
            ->addTargetTable(table: TargetTable::create(identifier: 'wh_meta_data',
                columns: [
                    // Add some spacial configuration for this column.
                    //
                    // Decoding the data from `meta_data` in multiple steps.
                    // First, the data is converted from a hexadecimal to a decimal representation (`new HexEncoder()`).
                    // After that the data will be decompressed using gzip.
                    // Now pseudify can search the plaintext obtained by multiple decodings.
                    TargetColumn::create(identifier: 'meta_data')->setEncoder(encoder: new ChainedEncoder(encoders: [
                        new HexEncoder(),
                        new GzEncodeEncoder(defaultContext: [
                            GzEncodeEncoder::ENCODE_LEVEL => 5,
                            GzEncodeEncoder::ENCODE_ENCODING => ZLIB_ENCODING_GZIP,
                        ]),
                    ])),
                ]
            ))
        ;

        return $tableDefinition;
    }
}
