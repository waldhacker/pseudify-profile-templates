<?php

declare(strict_types=1);

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Profile\Model\Pseudonymize\TableDefinition;
use Waldhacker\Pseudify\Core\Profile\Pseudonymize\ProfileInterface;
use Waldhacker\Pseudify\Profiles\Preset\TYPO3\Pseudonymize\TYPO3TableDefinitionPreset;

class TYPO3PseudonymizeProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-typo3';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = TYPO3TableDefinitionPreset::create($this->getIdentifier());

        // Change the table definition according to your needs
        // Add your extension tables and so on ...

        return $tableDefinition;
    }
}
