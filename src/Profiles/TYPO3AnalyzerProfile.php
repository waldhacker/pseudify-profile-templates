<?php

declare(strict_types=1);

namespace Waldhacker\Pseudify\Profiles;

use Waldhacker\Pseudify\Core\Profile\Analyze\ProfileInterface;
use Waldhacker\Pseudify\Core\Profile\Model\Analyze\TableDefinition;
use Waldhacker\Pseudify\Profiles\Preset\TYPO3\Analyze\TYPO3TableDefinitionPreset;

class TYPO3AnalyzerProfile implements ProfileInterface
{
    public function getIdentifier(): string
    {
        return 'my-typo3';
    }

    public function getTableDefinition(): TableDefinition
    {
        $tableDefinition = TYPO3TableDefinitionPreset::create(identifier: $this->getIdentifier());

        // Change the table definition according to your needs
        // Add your extension tables and so on ...

        return $tableDefinition;
    }
}
