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

namespace Waldhacker\Pseudify\Profiles\Preset\Analyze;

use Waldhacker\Pseudify\Core\Processor\Processing\Analyze\SourceDataCollectorContext;
use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\SourceColumn;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TableDefinition;

/**
 * A generic preset to analyze Wordpress installations.
 *
 * This preset is certainly incomplete.
 * Please help to complete the preset:
 *
 * * Add tables and columns from older and newer Wordpress versions
 * * Add definitions for frequently used extensions
 * * ...
 */
class WordpressTableDefinitionPreset
{
    /**
     * @api
     */
    public static function create(string $identifier = 'Wordpress'): TableDefinition
    {
        return TableDefinition::create(identifier: $identifier)
            // Add some data sources.
            // The data sources are used to find their contents in the rest of the database.

            // Looks like IPv4
            ->addSourceString(string: 'regex:(?:[0-9]{1,3}\.){3}[0-9]{1,3}')
            // Looks like IPv6
            // https://regex101.com/r/cT0hV4/5
            ->addSourceString(string: 'regex:(?:^|(?<=\s))(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))(?=\s|$)')

            ->addSourceTable(table: 'wp_comments', columns: [
                'comment_author',
                'comment_author_email',
                'comment_author_url',
                'comment_author_IP',
                'comment_agent',
            ])
            ->addSourceTable(table: 'wp_options', columns: [
                SourceColumn::create(identifier: 'option_value')
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'extract option values',
                        processor: function (SourceDataCollectorContext $context): void {
                            $optionNamesToCollect = [
                                'admin_email',
                                'mailserver_login',
                                'mailserver_pass',
                            ];

                            $row = $context->getDatebaseRow();
                            if (!in_array($row['option_name'], $optionNamesToCollect)) {
                                return;
                            }

                            $context->addCollectedData(data: $context->getDecodedData());
                        }
                    )),
            ])
            ->addSourceTable(table: 'wp_usermeta', columns: [
                SourceColumn::create(identifier: 'meta_value')
                    ->addDataProcessing(dataProcessing: new DataProcessing(identifier: 'extract meta values',
                        processor: function (SourceDataCollectorContext $context): void {
                            $metaKeysToCollect = [
                                'nickname',
                                'first_name',
                                'last_name',
                            ];

                            $row = $context->getDatebaseRow();
                            if (!in_array($row['meta_key'], $metaKeysToCollect)) {
                                return;
                            }

                            $context->addCollectedData(data: $context->getDecodedData());
                        }
                    )),
            ])
            ->addSourceTable(table: 'wp_users', columns: [
                'user_login',
                'user_pass',
                'user_nicename',
                'user_email',
                'user_url',
                'user_activation_key',
                'display_name',
            ])
            ->addSourceTable(table: 'wp_posts', columns: [
                'post_password',
            ])

            // Exclude some columns and column data types in the database to speed up the analysis
            ->excludeTargetColumnTypes(columnTypes: TableDefinition::COMMON_EXCLUED_TARGET_COLUMN_TYPES)
            ->excludeTargetTables(tables: [
                'wp_terms',
                'wp_termmeta',
                'wp_term_taxonomy',
                'wp_links',
            ])

            ->addTargetTable(table: 'wp_options', excludeColumns: [
                'option_name',
                'autoload',
            ])
            ->addTargetTable(table: 'wp_usermeta', excludeColumns: [
                'meta_key',
            ])
            ->addTargetTable(table: 'wp_posts', excludeColumns: [
                'guid',
                'post_content',
                'post_status',
                'comment_status',
                'ping_status',
                'to_ping',
                'pinged',
                'post_type',
                'post_mime_type',
                'post_title',
                'post_excerpt',
                'post_name',
                'post_content_filtered',
            ])
            ->addTargetTable(table: 'wp_commentmeta', excludeColumns: [
                'meta_key',
            ])
            ->addTargetTable(table: 'wp_comments', excludeColumns: [
                'comment_approved',
                'comment_type',
            ])
            ->addTargetTable(table: 'wp_postmeta', excludeColumns: [
                'meta_key',
            ])
        ;
    }
}
