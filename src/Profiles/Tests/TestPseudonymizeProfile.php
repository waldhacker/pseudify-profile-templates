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

declare(strict_types=1);

namespace Waldhacker\Pseudify\Profiles\Tests;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Faker\Provider\Person;
use Waldhacker\Pseudify\Core\Processor\Encoder\Base64Encoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\ChainedEncoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\GzEncodeEncoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\HexEncoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\JsonEncoder;
use Waldhacker\Pseudify\Core\Processor\Encoder\Serialized\Node\StringNode;
use Waldhacker\Pseudify\Core\Processor\Encoder\SerializedEncoder;
use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorContext;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorPreset;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Table;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;

/*
 * This is an example profile for this dataset:
 * https://github.com/waldhacker/pseudify-profile-templates/blob/0.0.1/tests/mariadb/10.5/pseudify_utf8mb4.sql
 */
class TestPseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'test-profile';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = new TableDefinition(identifier: $this->getIdentifier());

        $tableDefinition
            ->addTable(table: 'non_existing_table', columns: [
                Column::create(identifier: 'non_existing_column'),
            ])

            // Pseudonymize data within this table
            ->addTable(table: 'wh_user', columns: [
                // Pseudonymize data within this column
                Column::create(identifier: 'username')
                    // Replace the value within this column with fake data generated by FakerPHP/Faker formatter "userName"
                    ->addDataProcessing(dataProcessing: DataManipulatorPreset::scalarData(
                        fakerFormatter: 'userName',
                        processingIdentifier: 'fake user names'
                    )),

                // Pseudonymize data within this column
                Column::create(identifier: 'password')
                    // Replace the value within this column with fake data generated by FakerPHP/Faker formatter "argon2iPassword"
                    ->addDataProcessing(dataProcessing: DataManipulatorPreset::scalarData(
                        fakerFormatter: 'argon2iPassword',
                        processingIdentifier: 'fake argon2i passwords'
                    )),

                // Pseudonymize data within this column
                Column::create(identifier: 'first_name')
                    // Replace the value within this column with fake data generated by FakerPHP/Faker formatter "firstName"
                    ->addDataProcessing(dataProcessing: DataManipulatorPreset::scalarData(
                        fakerFormatter: 'firstName',
                        fakerArguments: [Person::GENDER_FEMALE],
                        processingIdentifier: 'fake female first names'
                    )),

                // Pseudonymize data within this column
                Column::create(identifier: 'last_name')
                    // Replace the value within this column with fake data generated by FakerPHP/Faker formatter "lastName"
                    ->addDataProcessing(dataProcessing: DataManipulatorPreset::scalarData(
                        fakerFormatter: 'lastName',
                        processingIdentifier: 'fake last names'
                    )),

                // Pseudonymize data within this column
                Column::create(identifier: 'email')
                    // Replace the value within this column with fake data generated by FakerPHP/Faker formatter "safeEmail"
                    ->addDataProcessing(dataProcessing: DataManipulatorPreset::scalarData(
                        fakerFormatter: 'safeEmail',
                        processingIdentifier: 'fake safe email addresses'
                    )),

                // Pseudonymize data within this column
                Column::create(identifier: 'city')
                    // Replace the value within this column with fake data generated by FakerPHP/Faker formatter "city"
                    ->addDataProcessing(dataProcessing: DataManipulatorPreset::scalarData(
                        fakerFormatter: 'city',
                        processingIdentifier: 'fake city names'
                    )),
            ])

            // Pseudonymize data within this table
            ->addTable(table: 'wh_log', columns: [
                // Pseudonymize data within this column
                Column::create(identifier: 'ip')
                    // Replace the value within this column with fake data generated by FakerPHP/Faker formatter "ipv4" / "ipv6"
                    ->addDataProcessing(dataProcessing: DataManipulatorPreset::ip(processingIdentifier: 'fake ip')),

                // Pseudonymize data within this column
                //
                // Interpret the data as JSON and pass the decoded data as an associative array to the `DataProcessing`
                Column::create(identifier: 'log_message', dataType: Column::DATA_TYPE_JSON)
                    // Add some advanced pseudonymization logic
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'process log messages',
                        processor: function (DataManipulatorContext $context): void {
                            // The decoded JSON data
                            $logMessage = $context->getProcessedData();

                            // The $logMessage looks like
                            // {"message":"foo text \"ronaldo15\", another \"mcclure.ofelia@example.com\""}
                            // or
                            // {"message":"bar text \"Block\", another \"georgiana59\""}
                            //
                            // Grab some values from the continuous text
                            preg_match('/^.*(".*").*(".*")$/', $logMessage['message'], $matches);
                            array_shift($matches);

                            // Process the data in different ways depending
                            // on the content of `$row['log_type']`
                            $row = $context->getDatebaseRow();
                            if ('foo' === $row['log_type']) {
                                $userName = trim($matches[0], '"');
                                $mail = trim($matches[1], '"');

                                // Pseudonymize the `$logMessage` items
                                $logMessage['message'] = strtr($logMessage['message'], [
                                    $matches[0] => sprintf('"%s"', $context->fake(source: $userName)->userName()),
                                    $matches[1] => sprintf('"%s"', $context->fake(source: $mail)->safeEmail()),
                                ]);
                            } else {
                                $lastName = trim($matches[0], '"');
                                $userName = trim($matches[1], '"');

                                // Pseudonymize the `$logMessage` items
                                $logMessage['message'] = strtr($logMessage['message'], [
                                    $matches[0] => sprintf('"%s"', $context->fake(source: $lastName)->lastName()),
                                    $matches[1] => sprintf('"%s"', $context->fake(source: $userName)->userName()),
                                ]);
                            }

                            // Replace the value in this column with the pseudonymized data
                            $context->setProcessedData(processedData: $logMessage);
                        }
                    ))
                    ->onBeforeUpdateData(onBeforeUpdateData: function (QueryBuilder $queryBuilder, Table $table, Column $column, DoctrineColumn $columnInfo, mixed $originalData, mixed $processedData, array $databaseRow): void {
                        // If you need to do crazy stuff before the update of the data, do it here
                        // ...
                    }),

                // Pseudonymize data within this column
                //
                // Interpret the data as HEX encoded. Pass the decoded data to the `DataProcessing`
                Column::create(identifier: 'log_data', dataType: Column::DATA_TYPE_HEX)
                    // Add some advanced pseudonymization logic
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'process logs',
                        processor: function (DataManipulatorContext $context): void {
                            $row = $context->getDatebaseRow();

                            // We need more decoding depending
                            // on the content of `$row['log_type']`
                            if ('foo' === $row['log_type']) {
                                $encoder = new ChainedEncoder(encoders: [new Base64Encoder(), new JsonEncoder()]);
                            } elseif ('bar' === $row['log_type']) {
                                $encoder = new SerializedEncoder();
                            } else {
                                return;
                            }

                            // Get the decoded data (from hex to binary)
                            $decodedData = $context->getDecodedData();
                            // Further decoding of the data
                            $logData = $encoder->decode(data: $decodedData);

                            if ('foo' === $row['log_type']) {
                                // The `$decodedData` looks like this
                                // {"userName":"ronaldo15","email":"mcclure.ofelia@example.com","lastName":"Keeling","ip":"1321:57fc:460b:d4d0:d83f:c200:4b:f1c8"}

                                // Collect the original data, which must be pseudonymized
                                $userName = $logData['userName'];
                                $email = $logData['email'];
                                $lastName = $logData['lastName'];
                                $ip = $logData['ip'];

                                // looks like an IPv6 address?
                                if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                                    // Generate a fake IPv6 address
                                    $fakeIp = $context->fake(source: $ip)->ipv6();
                                } elseif (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                                    // Generate a fake IPv4 address
                                    $fakeIp = $context->fake(source: $ip)->ipv4();
                                }

                                // Pseudonymize the `$logData` items
                                $logData['userName'] = $context->fake(source: $userName)->userName();
                                $logData['email'] = $context->fake(source: $email)->safeEmail();
                                $logData['lastName'] = $context->fake(source: $lastName)->lastName();
                                $logData['ip'] = $fakeIp;
                            } else {
                                // The `$decodedData` looks like this
                                // a:2:{i:0;s:14:"243.202.241.67";s:4:"user";O:8:"stdClass":5:{s:8:"userName";s:11:"georgiana59";s:8:"lastName";s:5:"Block";s:5:"email";s:19:"nolan11@example.net";s:2:"id";i:2;s:4:"user";R:3;}}

                                // Collect the original data, which must be pseudonymized
                                $ip = $logData->getPropertyContent(identifier: 0)->getValue();
                                $userNode = $logData->getPropertyContent(identifier: 'user');
                                $userName = $userNode->getPropertyContent(identifier: 'userName')->getValue();
                                $lastName = $userNode->getPropertyContent(identifier: 'lastName')->getValue();
                                $email = $userNode->getPropertyContent(identifier: 'email')->getValue();

                                // looks like an IPv6 address?
                                if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                                    // Generate a fake IPv6 address
                                    $fakeIp = $context->fake(source: $ip)->ipv6();
                                } elseif (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                                    // Generate a fake IPv4 address
                                    $fakeIp = $context->fake(source: $ip)->ipv4();
                                }

                                // Pseudonymize the `$logData` items
                                $logData->replaceProperty(
                                    identifier: 0,
                                    property: new StringNode(content: $fakeIp)
                                );

                                $userNode
                                    ->replaceProperty(
                                        identifier: 'userName',
                                        property: new StringNode(content: $context->fake(source: $userName)->userName())
                                    )
                                    ->replaceProperty(
                                        identifier: 'lastName',
                                        property: new StringNode(content: $context->fake(source: $lastName)->lastName())
                                    )
                                    ->replaceProperty(
                                        identifier: 'email',
                                        property: new StringNode(content: $context->fake(source: $email)->safeEmail())
                                    )
                                ;
                            }

                            // We need to re-encode the data
                            $logData = $encoder->encode(data: $logData);

                            // Replace the value in this column with the pseudonymized data
                            $context->setProcessedData(processedData: $logData);
                        }
                    )),
            ])

            // Pseudonymize data within this table
            ->addTable(table: 'wh_user_session', columns: [
                // Pseudonymize data within this column
                //
                // Interpret the data as PHP serialized values. Pass the decoded data as an AST to the `DataProcessing`.
                // @see https://github.com/waldhacker/pseudify-core/blob/0.0.1/src/src/Processor/Encoder/Serialized/Parser.php#L56
                Column::create(identifier: 'session_data', dataType: Column::DATA_TYPE_SERIALIZED)
                    // Add some advanced pseudonymization logic
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'fake IPv4',
                        processor: function (DataManipulatorContext $context): void {
                            // Get the decoded data
                            $sessionDataNode = $context->getDecodedData();

                            // Collect the original data, which must be pseudonymized
                            $ip = $sessionDataNode->getPropertyContent(identifier: 'last_ip')->getValue();

                            // looks like an IPv6 address?
                            if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                                // Generate a fake IPv6 address
                                $fakeIp = $context->fake(source: $ip)->ipv6();
                            } elseif (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                                // Generate a fake IPv4 address
                                $fakeIp = $context->fake(source: $ip)->ipv4();
                            }

                            // Pseudonymize the data
                            $sessionDataNode->replaceProperty(
                                identifier: 'last_ip',
                                property: new StringNode(content: $fakeIp)
                            );

                            // Replace the value within this column with the pseudonymized data
                            $context->setProcessedData(processedData: $sessionDataNode);
                        }
                    )),

                // Pseudonymize data within this column
                //
                // Interpret the data as JSON and pass the decoded data as an associative array to the `DataProcessing`
                Column::create(identifier: 'session_data_json', dataType: Column::DATA_TYPE_JSON)
                    // Add some advanced pseudonymization logic
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'fake IPv4',
                        processor: function (DataManipulatorContext $context): void {
                            // The decoded JSON data
                            $data = $context->getDecodedData();

                            // Collect the original data, which must be pseudonymized
                            $ip = $data['data']['last_ip'];

                            // looks like an IPv6 address?
                            if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                                // Generate a fake IPv6 address
                                $fakeIp = $context->fake(source: $ip)->ipv6();
                            } elseif (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                                // Generate a fake IPv4 address
                                $fakeIp = $context->fake(source: $ip)->ipv4();
                            }

                            // Pseudonymize the data
                            $data['data']['last_ip'] = $fakeIp;

                            // Replace the value in this column with the pseudonymized data
                            $context->setProcessedData(processedData: $data);
                        }
                    )),
            ])

            // Pseudonymize data within this table
            ->addTable(table: 'wh_meta_data', columns: [
                // Pseudonymize data within this column
                Column::create(identifier: 'meta_data')
                    // Add multiple decodings / encodings.
                    // Hex -> binary -> decopress using ZLIB -> transform into an PHP serialized AST
                    // (@see https://github.com/waldhacker/pseudify-core/blob/0.0.1/src/src/Processor/Encoder/Serialized/Parser.php#L56)
                    ->setEncoder(encoder: new ChainedEncoder(encoders: [
                        new HexEncoder(),
                        new GzEncodeEncoder(defaultContext: [
                            GzEncodeEncoder::ENCODE_LEVEL => 5,
                            GzEncodeEncoder::ENCODE_ENCODING => ZLIB_ENCODING_GZIP,
                        ]),
                        new SerializedEncoder(),
                    ]))
                    // Add some advanced pseudonymization logic
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'fake meta data',
                        processor: function (DataManipulatorContext $context): void {
                            // The plain data looks like this:
                            // a:3:{s:4:"key1";a:9:{s:2:"id";i:3;s:8:"username";s:6:"hpagac";s:8:"password";s:92:"$argon2i$v=19$m=8,t=1,p=1$QXNXbTRMZWxmenBRUzdwZQ$i6hntUDLa3ZFqmCG4FM0iPrpMp6d4D8XfrNBtyDmV9U";s:18:"password_hash_type";s:7:"argon2i";s:18:"password_plaintext";s:8:"[dvGd#gI";s:10:"first_name";s:6:"Donato";s:9:"last_name";s:7:"Keeling";s:5:"email";s:26:"mcclure.ofelia@example.com";s:4:"city";s:16:"North Elenamouth";}s:4:"key2";a:2:{s:2:"id";i:3;s:12:"session_data";s:41:"a:1:{s:7:"last_ip";s:13:"244.166.32.78";}";}s:4:"key3";a:1:{s:4:"key4";s:12:"239.27.57.12";}}

                            // Get the decoded data
                            $metaDataNode = $context->getDecodedData();

                            // Collect the original data, which must be pseudonymized
                            $key1Node = $metaDataNode->getPropertyContent(identifier: 'key1');

                            $userName = $key1Node->getPropertyContent(identifier: 'username')->getValue();
                            $password = $key1Node->getPropertyContent(identifier: 'password')->getValue();
                            $firstName = $key1Node->getPropertyContent(identifier: 'first_name')->getValue();
                            $lastName = $key1Node->getPropertyContent(identifier: 'last_name')->getValue();
                            $email = $key1Node->getPropertyContent(identifier: 'email')->getValue();
                            $city = $key1Node->getPropertyContent(identifier: 'city')->getValue();

                            // Pseudonymize the data
                            $key1Node
                                ->replaceProperty(
                                    identifier: 'username',
                                    property: new StringNode(content: $context->fake(source: $userName)->userName())
                                )
                                ->replaceProperty(
                                    identifier: 'password',
                                    property: new StringNode(content: $context->fake(source: $password)->password())
                                )
                                ->replaceProperty(
                                    identifier: 'first_name',
                                    property: new StringNode(content: $context->fake(source: $firstName)->firstName(gender: Person::GENDER_FEMALE))
                                )
                                ->replaceProperty(
                                    identifier: 'last_name',
                                    property: new StringNode(content: $context->fake(source: $lastName)->lastName())
                                )
                                ->replaceProperty(
                                    identifier: 'email',
                                    property: new StringNode(content: $context->fake(source: $email)->safeEmail())
                                )
                                ->replaceProperty(
                                    identifier: 'city',
                                    property: new StringNode(content: $context->fake(source: $city)->city())
                                )
                            ;

                            // Collect the original data, which must be pseudonymized
                            $key2Node = $metaDataNode->getPropertyContent(identifier: 'key2');

                            // `session_data` contains another PHP serialized data string
                            // so we need to decode it too.
                            // The plain data looks like this:
                            // a:1:{s:7:"last_ip";s:13:"244.166.32.78";}
                            $rawSessionData = $key2Node->getPropertyContent(identifier: 'session_data')->getValue();
                            $encoder = new SerializedEncoder();
                            $sessionDataNode = $encoder->decode(data: $rawSessionData);

                            // Collect the original data, which must be pseudonymized
                            $ip = $sessionDataNode->getPropertyContent(identifier: 'last_ip')->getValue();

                            // looks like an IPv6 address?
                            if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                                // Generate a fake IPv6 address
                                $fakeIp = $context->fake(source: $ip)->ipv6();
                            } elseif (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                                // Generate a fake IPv4 address
                                $fakeIp = $context->fake(source: $ip)->ipv4();
                            }

                            // Pseudonymize the data
                            $sessionDataNode->replaceProperty(
                                identifier: 'last_ip',
                                property: new StringNode(content: $fakeIp)
                            );

                            // Re-encode the data
                            $rawSessionData = $encoder->encode(data: $sessionDataNode);

                            // Pseudonymize the data
                            $key2Node->replaceProperty(
                                identifier: 'session_data',
                                property: new StringNode(content: $rawSessionData)
                            );

                            // Collect the original data, which must be pseudonymized
                            $key3Node = $metaDataNode->getPropertyContent(identifier: 'key3');
                            $ip = $key3Node->getPropertyContent(identifier: 'key4')->getValue();

                            // looks like an IPv6 address?
                            if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                                // Generate a fake IPv6 address
                                $fakeIp = $context->fake(source: $ip)->ipv6();
                            } elseif (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                                // Generate a fake IPv4 address
                                $fakeIp = $context->fake(source: $ip)->ipv4();
                            }

                            // Pseudonymize the data
                            $key3Node->replaceProperty(
                                identifier: 'key4',
                                property: new StringNode(content: $fakeIp)
                            );

                            // Replace the value in this column with the pseudonymized data
                            $context->setProcessedData(processedData: $metaDataNode);
                        }
                    )),
            ])
        ;

        return $tableDefinition;
    }
}
