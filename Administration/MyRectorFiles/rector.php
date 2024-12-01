<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Rector\Config\RectorConfig;
use Administration\MyRectorFiles\MyRectorTailorMadeFuncs\MyRefactoringFirstPass;
use Administration\MyRectorFiles\MyRectorTailorMadeFuncs\MyRefactoringSecondPass;
use Administration\MyRectorFiles\MyRectorTailorMadeFuncs\MyRectorBackupsHandler;
use Administration\MyRectorFiles\MyRectorTailorMadeFuncs\MyRectorLoggingHandler;
//use Administration\MyRectorFiles\MyRectorTailorMadeFuncs\MyRollbackHandler;

/**
 * Returnerer projektstier, der skal analyseres og refaktoreres.
 *
 * @return array<string>
 */
function getProjectPaths(): array
{
    return [
        __DIR__ . '/../Includes',
    ];
}

/**
 * Returnerer mapper, der skal ekskluderes fra refaktorering.
 *
 * @return array<string>
 */
function getExcludedPaths(): array
{
    return [
        __DIR__ . '/../vendor',
        __DIR__ . '/../MyRectorFiles/MyRectorLogsAndBackupFiles',
        __DIR__ . '/../.vscode',
    ];
}

// Generer tidsstemplet én gang
$timestamp = date('Ymd_His');

// Initialiser logning én gang
$loggingHandler = MyRectorLoggingHandler::getInstance(__DIR__, $timestamp);
$loggingHandler->log("Rector-processen er startet.");

try {
    // Hvis du vil udføre backup, sørg for at backup-handleren er aktiv (ikke kommenteret)
    // og at rollback-handleren er kommenteret.
    // --------------------------------------------------------
    // Initialiser backup handler
    $backupHandler = new MyRectorBackupsHandler(__DIR__, $timestamp);
    $backupHandler->createBackups(getProjectPaths());

    // Hvis du vil udføre rollback, sørg for at rollback-handleren er aktiv
    // og at backup-handleren er kommenteret.
    // --------------------------------------------------------
    /*
    // Initialiser rollback handler
    $rollbackHandler = new MyRollbackHandler(__DIR__, $timestamp);
    // Udfør rollback fra seneste backup
    $rollbackHandler->rollbackChanges();
    // Efter rollback behøver vi ikke at fortsætte med refaktorering
    $loggingHandler->log("Rector-processen er afsluttet efter rollback.");
    return;
    */

    return static function (RectorConfig $rectorConfig) use ($timestamp): void {
        // Indlæs projekt- og eksklusionsstier
        $projectPaths = getProjectPaths();
        $excludedPaths = getExcludedPaths();

        // Deaktiver parallelitet for at dele data på tværs af processer
        $rectorConfig->disableParallel();

        // Konfigurer Rector
        $rectorConfig->paths($projectPaths);
        $rectorConfig->skip($excludedPaths);

        // Tilføj første pass regel
        $rectorConfig->ruleWithConfiguration(MyRefactoringFirstPass::class, [
            'projectPaths' => $projectPaths,
            'timestamp' => $timestamp,
        ]);

        // Tilføj andet pass regel
        $rectorConfig->ruleWithConfiguration(MyRefactoringSecondPass::class, [
            'projectPaths' => $projectPaths,
            'timestamp' => $timestamp,
        ]);
    };
} catch (Throwable $e) {
    // Log fejl og genkast for debugging
    if (isset($loggingHandler)) {
        $loggingHandler->log(
            "Fejl under Rector-processen: " . $e->getMessage(),
            'ERROR',
            __FILE__,
            __LINE__
        );
    }
    throw $e;
} finally {
    if (isset($loggingHandler)) {
        $loggingHandler->log("Rector-processen er afsluttet.");
    }
}
