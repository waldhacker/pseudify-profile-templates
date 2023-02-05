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

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Waldhacker\Pseudify\Core\Processor\Encoder\Serialized\Node\StringNode;
use Waldhacker\Pseudify\Core\Processor\Processing\DataProcessing;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorContext;
use Waldhacker\Pseudify\Core\Processor\Processing\Pseudonymize\DataManipulatorPreset;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\Column;
use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;

/**
 * A generic preset to pseudonymize TYPO3 installations.
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
            ->addTable(table: 'be_users', columns: [
                Column::create(identifier: 'username')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'username', processingIdentifier: 'fake username')
                ),
                Column::create(identifier: 'password')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'argon2iPassword', processingIdentifier: 'fake password')
                ),
                Column::create(identifier: 'email')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'safeEmail', processingIdentifier: 'fake safeEmail')
                ),
                Column::create(identifier: 'realName')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'name', processingIdentifier: 'fake name')
                ),
                Column::create(identifier: 'password_reset_token')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'argon2iPassword', processingIdentifier: 'fake argon2iPassword')
                ),
            ])

            ->addTable(table: 'fe_users', columns: [
                Column::create(identifier: 'username')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'username', processingIdentifier: 'fake username')
                ),
                Column::create(identifier: 'password')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'argon2iPassword', processingIdentifier: 'fake argon2iPassword')
                ),
                Column::create(identifier: 'name')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'name', processingIdentifier: 'fake name')
                ),
                Column::create(identifier: 'first_name')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'firstName', processingIdentifier: 'fake firstName')
                ),
                Column::create(identifier: 'middle_name')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'firstName', processingIdentifier: 'fake firstName')
                ),
                Column::create(identifier: 'last_name')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'lastName', processingIdentifier: 'fake lastName')
                ),
                Column::create(identifier: 'address')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'address', processingIdentifier: 'fake address')
                ),
                Column::create(identifier: 'telephone')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'phoneNumber', processingIdentifier: 'fake phoneNumber')
                ),
                Column::create(identifier: 'fax')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'phoneNumber', processingIdentifier: 'fake phoneNumber')
                ),
                Column::create(identifier: 'email')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'safeEmail', processingIdentifier: 'fake safeEmail')
                ),
                Column::create(identifier: 'title')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'title', processingIdentifier: 'fake title')
                ),
                Column::create(identifier: 'zip')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'postcode', processingIdentifier: 'fake postcode')
                ),
                Column::create(identifier: 'city')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'city', processingIdentifier: 'fake city')
                ),
                Column::create(identifier: 'www')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'url', processingIdentifier: 'fake url')
                ),
                Column::create(identifier: 'company')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'company', processingIdentifier: 'fake company')
                ),
                Column::create(identifier: 'felogin_forgotHash')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'argon2iPassword', processingIdentifier: 'fake argon2iPassword')
                ),
            ])

            ->addTable(table: 'sys_file_metadata', columns: [
                Column::create(identifier: 'latitude')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'latitude', processingIdentifier: 'fake latitude')
                ),
                Column::create(identifier: 'longitude')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'longitude', processingIdentifier: 'fake longitude')
                ),
                Column::create(identifier: 'location_city')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'city', processingIdentifier: 'fake city')
                ),
            ])

            ->addTable(table: 'sys_lockedrecords', columns: [
                Column::create(identifier: 'username')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::scalarData(fakerFormatter: 'username', processingIdentifier: 'fake username')
                ),
            ])

            ->addTable(table: 'sys_refindex', columns: [
                Column::create(identifier: 'ref_string')->addDataProcessing(
                    dataProcessing: new DataProcessing(identifier: 'find and fake emails',
                        processor: function (DataManipulatorContext $context): void {
                            $softRefValue = $context->getProcessedData();

                            if (
                                !is_string($softRefValue)
                                || trim($softRefValue) !== $softRefValue
                                || !str_contains($softRefValue, '@')
                                || !(new EmailValidator())->isValid($softRefValue, new RFCValidation())
                            ) {
                                return;
                            }

                            $context->setProcessedData(processedData: $context->fake(source: $softRefValue)->safeEmail());
                        }
                    )
                ),
            ])

            ->addTable(table: 'sys_history', columns: [
                Column::create(identifier: 'history_data', dataType: Column::DATA_TYPE_JSON)->addDataProcessing(
                    dataProcessing: new DataProcessing(identifier: 'find and fake username, password, email',
                        processor: function (DataManipulatorContext $context): void {
                            $historyData = $context->getProcessedData();
                            $row = $context->getDatebaseRow();
                            if (
                                'be_users' !== $row['tablename']
                                || !is_array($historyData)
                            ) {
                                return;
                            }

                            if (isset($historyData['username'])) {
                                $historyData['username'] = $context->fake(source: $historyData['username'])->username();
                            }
                            if (isset($historyData['oldRecord']['username'])) {
                                $historyData['oldRecord']['username'] = $context->fake(source: $historyData['oldRecord']['username'])->username();
                            }
                            if (isset($historyData['newRecord']['username'])) {
                                $historyData['newRecord']['username'] = $context->fake(source: $historyData['newRecord']['username'])->username();
                            }

                            if (isset($historyData['password'])) {
                                $historyData['password'] = $context->fake(source: $historyData['password'])->argon2iPassword();
                            }
                            if (isset($historyData['oldRecord']['password'])) {
                                $historyData['oldRecord']['password'] = $context->fake(source: $historyData['oldRecord']['password'])->argon2iPassword();
                            }
                            if (isset($historyData['newRecord']['password'])) {
                                $historyData['newRecord']['password'] = $context->fake(source: $historyData['newRecord']['password'])->argon2iPassword();
                            }

                            if (isset($historyData['email'])) {
                                $historyData['email'] = $context->fake(source: $historyData['email'])->safeEmail();
                            }
                            if (isset($historyData['oldRecord']['email'])) {
                                $historyData['oldRecord']['email'] = $context->fake(source: $historyData['oldRecord']['email'])->safeEmail();
                            }
                            if (isset($historyData['newRecord']['email'])) {
                                $historyData['newRecord']['email'] = $context->fake(source: $historyData['newRecord']['email'])->safeEmail();
                            }

                            $context->setProcessedData(processedData: $historyData);
                        }
                    )
                ),
            ])

            ->addTable(table: 'sys_log', columns: [
                Column::create(identifier: 'IP')->addDataProcessing(
                    dataProcessing: DataManipulatorPreset::ip(processingIdentifier: 'fake ip')
                ),
                Column::create(identifier: 'log_data', dataType: Column::DATA_TYPE_SERIALIZED)->addDataProcessing(
                    dataProcessing: new DataProcessing(identifier: 'fake IPv4 / IPv6 addresses',
                        processor: function (DataManipulatorContext $context): void {
                            $row = $context->getDatebaseRow();
                            $node = $context->getProcessedData();

                            if (
                                // \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication::checkAuthentication
                                (255 === (int) $row['type'] && 1 === (int) $row['action'] && 0 === (int) $row['error'])
                                // \TYPO3\CMS\Backend\Controller\LogoutController::processLogout
                                // \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication::checkAuthentication
                                || (255 === (int) $row['type'] && 2 === (int) $row['action'] && 0 === (int) $row['error'])
                                // \TYPO3\CMS\Core\Authentication\AuthenticationService::getUser
                                // \TYPO3\CMS\Core\Authentication\AuthenticationService::authUser
                                || (255 === (int) $row['type'] && 3 === (int) $row['action'] && 3 === (int) $row['error'])
                            ) {
                                $node->replaceProperty(
                                    identifier: 0,
                                    property: new StringNode(
                                        content: $context->fake(
                                            source: $node->getPropertyContent(identifier: 0)->getValue()
                                        )->username()
                                    )
                                );
                            } elseif (
                                // \TYPO3\CMS\Backend\Security\FailedLoginAttemptNotification::sendEmailOnLoginFailures
                                255 === (int) $row['type'] && 4 === (int) $row['action'] && 0 === (int) $row['error']
                            ) {
                                $node->replaceProperty(
                                    identifier: 2,
                                    property: new StringNode(
                                        content: $context->fake(
                                            source: $node->getPropertyContent(identifier: 2)->getValue()
                                        )->safeEmail()
                                    )
                                );
                            } elseif (
                                // \TYPO3\CMS\Backend\Authentication\PasswordReset::sendAmbiguousEmail
                                (255 === (int) $row['type'] && 5 === (int) $row['action'] && 4 === (int) $row['error'])
                                // \TYPO3\CMS\Backend\Authentication\PasswordReset::sendResetEmail
                                (255 === (int) $row['type'] && 5 === (int) $row['action'] && 3 === (int) $row['error'])
                                // \TYPO3\CMS\Backend\Authentication\PasswordReset::resetPassword
                                (255 === (int) $row['type'] && 6 === (int) $row['action'] && 3 === (int) $row['error'])
                            ) {
                                $node->replaceProperty(
                                    identifier: 'email',
                                    property: new StringNode(
                                        content: $context->fake(
                                            source: $node->getPropertyContent(identifier: 'email')->getValue()
                                        )->safeEmail()
                                    )
                                );
                            }

                            $context->setProcessedData(processedData: $node);
                        }
                    )
                ),
            ])
        ;
    }
}
