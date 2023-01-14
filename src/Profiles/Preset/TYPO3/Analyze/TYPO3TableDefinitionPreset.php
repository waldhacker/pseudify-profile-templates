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

namespace Waldhacker\Pseudify\Profiles\Preset\TYPO3\Analyze;

use Waldhacker\Pseudify\Core\Processor\Processing\Analyze\TargetDataDecoderPreset;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TargetColumn;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TargetTable;

/**
 * A generic preset to analyze TYPO3 installations.
 *
 * This preset is certainly incomplete.
 * Please help to complete the preset:
 *
 * * Add tables and columns from older and newer TYPO3 versions
 * * Add definitions for frequently used extensions
 * * ...
 */
class TYPO3TableDefinitionPreset
{
    /**
     * @api
     */
    public static function create(string $identifier = 'TYPO3'): TableDefinition
    {
        return TableDefinition::create(identifier: $identifier)
            ->setTargetDataFrameCuttingLength(length: 0)

            // Add some data sources.
            // The data sources are used to find their contents in the rest of the database.

            // Looks like IPv4
            ->addSourceString(string: 'regex:(?:[0-9]{1,3}\.){3}[0-9]{1,3}')
            // Looks like IPv6
            // https://regex101.com/r/cT0hV4/5
            ->addSourceString(string: 'regex:(?:^|(?<=\s))(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))(?=\s|$)')

            ->addSourceTable(table: 'be_users', columns: [
                'username',
                'password',
                'email',
                'realName',
                'password_reset_token',
            ])
            ->addSourceTable(table: 'fe_users', columns: [
                'username',
                'password',
                'name',
                'first_name',
                'middle_name',
                'last_name',
                'address',
                'telephone',
                'fax',
                'email',
                'title',
                'zip',
                'city',
                'www',
                'company',
                'felogin_forgotHash',
            ])
            ->addSourceTable(table: 'sys_file_metadata', columns: [
                'latitude',
                'location_city',
                'longitude',
            ])
            ->addSourceTable(table: 'sys_lockedrecords', columns: [
                'username',
            ])
            ->addSourceTable(table: 'sys_log', columns: [
                'IP',
            ])

            // Exclude some columns and column data types in the database to speed up the analysis
            ->excludeTargetColumnTypes(columnTypes: TableDefinition::COMMON_EXCLUED_TARGET_COLUMN_TYPES)
            ->excludeTargetTables(tables: [
                'backend_layout',
                'be_dashboards',
                'cache_.*',
                'index_config',
                'index_debug',
                'index_grlist',
                'index_rel',
                'index_section',
                'pages',
                'sys_category_record_mm',
                'sys_collection_entries',
                'sys_language',
                'sys_preview',
                'sys_redirect',
                'tx_extensionmanager_domain_model_extension',
                'tx_extensionmanager_domain_model_repository',
                'tx_impexp_presets',
                'tx_scheduler_task_group',
            ])

            ->addTargetTable(table: 'be_groups', excludeColumns: [
                'allowed_languages',
                'availableWidgets',
                'category_perms',
                'custom_options',
                'db_mountpoints',
                'explicit_allowdeny',
                'file_mountpoints',
                'file_permissions',
                'groupMods',
                'mfa_providers',
                'non_exclude_fields',
                'pagetypes_select',
                'subgroup',
                'tables_modify',
                'tables_select',
            ])
            ->addTargetTable(table: 'be_sessions', excludeColumns: [
                'ses_id',
                'ses_iplock',
            ])
            ->addTargetTable(table: 'be_users', excludeColumns: [
                'allowed_languages',
                'category_perms',
                'db_mountpoints',
                'file_mountpoints',
                'file_permissions',
                'lang',
                'mfa',
                'userMods',
                'usergroup',
                'usergroup_cached_list',
            ])
            ->addTargetTable(table: 'fe_groups', excludeColumns: [
                'felogin_redirectPid',
                'subgroup',
                'tx_extbase_type',
            ])
            ->addTargetTable(table: 'fe_sessions', excludeColumns: [
                'ses_id',
                'ses_iplock',
            ])
            ->addTargetTable(table: 'fe_users', excludeColumns: [
                'felogin_redirectPid',
                'image',
                'mfa',
                'tx_extbase_type',
                'usergroup',
            ])
            ->addTargetTable(table: 'index_phash', excludeColumns: [
                'static_page_arguments',
                'data_page_mp',
                'gr_list',
                'item_type',
            ])
            ->addTargetTable(table: 'sys_be_shortcuts', excludeColumns: [
                'arguments',
                'module_name',
                'route',
                'url',
            ])
            ->addTargetTable(table: 'sys_category', excludeColumns: [
                'l10n_state',
                'l10n_diffsource',
            ])
            ->addTargetTable(table: 'sys_collection', excludeColumns: [
                'fe_group',
                'l10n_state',
                'l10n_diffsource',
                'type',
                'table_name',
            ])
            ->addTargetTable(table: 'sys_file', excludeColumns: [
                'extension',
                'folder_hash',
                'identifier_hash',
                'mime_type',
                'sha1',
                'type',
            ])
            ->addTargetTable(table: 'sys_file_collection', excludeColumns: [
                'l10n_state',
                'l10n_diffsource',
                'type',
            ])
            ->addTargetTable(table: 'sys_file_metadata', excludeColumns: [
                'color_space',
                'fe_groups',
                'language',
                'l10n_state',
                'l10n_diffsource',
                'status',
                'unit',
            ])
            ->addTargetTable(table: 'sys_file_processedfile', excludeColumns: [
                'checksum',
                'configurationsha1',
                'originalfilesha1',
                'task_type',
            ])
            ->addTargetTable(table: 'sys_file_reference', excludeColumns: [
                'crop',
                'fieldname',
                'l10n_diffsource',
                'l10n_state',
                'link',
                'table_local',
                'tablenames',
            ])
            ->addTargetTable(table: 'sys_file_storage', excludeColumns: [
                'driver',
                'processingfolder',
            ])
            ->addTargetTable(TargetTable::create(identifier: 'sys_history',
                excludeColumns: [
                    'correlation_id',
                    'tablename',
                    'usertype',
                ],
                columns: [
                    // Normalize JSON strings to make them searchable (decode UTF encodings)
                    TargetColumn::create('history_data')->addDataProcessing(dataProcessing: TargetDataDecoderPreset::normalizedJsonString()),
                ]
            ))
            ->addTargetTable(table: 'sys_lockedrecords', excludeColumns: [
                'record_table',
            ])
            ->addTargetTable(table: 'sys_log', excludeColumns: [
                'component',
                'NEWid',
                'request_id',
                'tablename',
            ])
            ->addTargetTable(table: 'sys_refindex', excludeColumns: [
                'field',
                'flexpointer',
                'hash',
                'ref_table',
                'softref_id',
                'tablename',
            ])
            ->addTargetTable(table: 'sys_registry', excludeColumns: [
                'entry_namespace',
            ])
            ->addTargetTable(table: 'sys_template', excludeColumns: [
                'basedOn',
                'include_static_file',
            ])
            ->addTargetTable(table: 'sys_workspace', excludeColumns: [
                'adminusers',
                'db_mountpoints',
                'edit_notification_defaults',
                'execute_notification_defaults',
                'file_mountpoints',
                'members',
                'publish_notification_defaults',
            ])
            ->addTargetTable(table: 'sys_workspace_stage', excludeColumns: [
                'notification_defaults',
                'parenttable',
                'responsible_persons',
                'title',
            ])
            ->addTargetTable(table: 'tt_content', excludeColumns: [
                'category_field',
                'CType',
                'header_position',
                'fe_group',
                'file_collections',
                'filelink_sorting',
                'filelink_sorting_direction',
                'frame_class',
                'header_layout',
                'header_link',
                'l10n_state',
                'l18n_diffsource',
                'list_type',
                'pages',
                'records',
                'selected_categories',
                'space_after_class',
                'space_before_class',
                'table_class',
                'target',
            ])
            ->addTargetTable(table: 'tx_linkvalidator_link', excludeColumns: [
                'element_type',
                'field',
                'link_type',
                'table_name',
            ])
            ->addTargetTable(table: 'tx_scheduler_task', excludeColumns: [
                'lastexecution_context',
            ])
        ;
    }
}
