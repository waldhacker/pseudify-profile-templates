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

namespace Waldhacker\Pseudify\Profiles\Preset\Pseudonymize;

use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorContext;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorPreset;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;

/**
 * A generic preset to pseudonymize Wordpress installations.
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
            ->addTable(table: 'wp_users', columns: [
                Column::create(identifier: 'user_login')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'username', processingIdentifier: 'fake username')
                ),
                Column::create(identifier: 'user_pass')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'bcryptPassword', processingIdentifier: 'fake password')
                ),
                Column::create(identifier: 'user_nicename')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'name', processingIdentifier: 'fake name')
                ),
                Column::create(identifier: 'user_email')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'safeEmail', processingIdentifier: 'fake safeEmail')
                ),
                Column::create(identifier: 'user_url')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'url', processingIdentifier: 'fake url')
                ),
                Column::create(identifier: 'user_activation_key')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'bcryptPassword', processingIdentifier: 'fake password')
                ),
                Column::create(identifier: 'display_name')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'name', processingIdentifier: 'fake name')
                ),
            ])

            ->addTable(table: 'wp_comments', columns: [
                Column::create(identifier: 'comment_author')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'name', processingIdentifier: 'fake name')
                ),
                Column::create(identifier: 'comment_author_email')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'safeEmail', processingIdentifier: 'fake safeEmail')
                ),
                Column::create(identifier: 'comment_author_url')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'url', processingIdentifier: 'fake url')
                ),
                Column::create(identifier: 'comment_author_IP')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::ip(processingIdentifier: 'fake ip')
                ),
                Column::create(identifier: 'comment_agent')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'userAgent', processingIdentifier: 'fake userAgent')
                ),
            ])

            ->addTable(table: 'wp_options', columns: [
                Column::create(identifier: 'option_value')->addDataProcessing(
                    dataProcessing: new DataProcessing(identifier: 'find and fake options',
                        processor: function (DataManipulatorContext $context): void {
                            $value = $context->getProcessedData();

                            $row = $context->getDatebaseRow();
                            switch ($row['option_name']) {
                                case 'admin_email':
                                    $context->setProcessedData(processedData: $context->fake(source: $value)->safeEmail());
                                    break;
                                case 'mailserver_login':
                                    $context->setProcessedData(processedData: $context->fake(source: $value)->username());
                                    break;
                                case 'mailserver_pass':
                                    $context->setProcessedData(processedData: $context->fake(source: $value)->password());
                                    break;
                                default:
                                    return;
                            }
                        }
                    )
                ),
            ])

            ->addTable(table: 'wp_usermeta', columns: [
                Column::create(identifier: 'meta_value')->addDataProcessing(
                    dataProcessing: new DataProcessing(identifier: 'find and fake meta data',
                        processor: function (DataManipulatorContext $context): void {
                            $value = $context->getProcessedData();

                            $row = $context->getDatebaseRow();
                            switch ($row['meta_key']) {
                                case 'nickname':
                                    $context->setProcessedData(processedData: $context->fake(source: $value)->username());
                                    break;
                                case 'first_name':
                                    $context->setProcessedData(processedData: $context->fake(source: $value)->firstName());
                                    break;
                                case 'last_name':
                                    $context->setProcessedData(processedData: $context->fake(source: $value)->lastName());
                                    break;
                                default:
                                    return;
                            }
                        }
                    )
                ),
            ])

            ->addTable(table: 'wp_posts', columns: [
                Column::create(identifier: 'post_password')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'bcryptPassword', processingIdentifier: 'fake password')
                ),
            ])
        ;
    }
}
