<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\PSR4\Rector\FileWithoutNamespace\NormalizeNamespaceByPSR4ComposerAutoloadRector;
use Rector\Renaming\Rector\Class_\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    // Mapper, hvor Rector skal arbejde
    $rectorConfig->paths([
        __DIR__ . '/Includes',
        __DIR__ . '/Modules',
        __DIR__ . '/Core',
    ]);

    // Ekskluder mapper som vendor og editor-specifikke mapper
    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/.vscode',
        __DIR__ . '/node_modules',
    ]);

    // Retter namespaces baseret på PSR-4 og composer.json
    $rectorConfig->rule(NormalizeNamespaceByPSR4ComposerAutoloadRector::class);

    // Dynamisk rettelse af klassenavne baseret på filnavn og referencer
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, []);

    // Tilføj logger for at spore ændringer
    $rectorConfig->withLogging();
};
