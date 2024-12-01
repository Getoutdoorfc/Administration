<?php

declare(strict_types=1);

namespace Administration\MyRectorFiles\MyRectorTailorMadeFuncs;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Handler til at administrere rollback-processen for Rector.
 */
class MyRollbackHandler
{
    private string $projectRoot;
    private string $backupDir;
    private MyRectorLoggingHandler $logger;
    private string $timestamp;

    /**
     * Konstruktor.
     *
     * Initialiserer projektroden, backup-mappen og loggeren.
     *
     * @param string $projectRoot Rodstien til projektet.
     * @param string $timestamp Tidsstempel for at matche backup-mappen.
     */
    public function __construct(string $projectRoot, string $timestamp)
    {
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
        $this->backupDir = $this->projectRoot . '/MyRectorLogsAndBackupFiles/Backups/';
        $this->timestamp = $timestamp;
        $this->logger = MyRectorLoggingHandler::getInstance($projectRoot, $timestamp);
        $this->ensureBackupDirectoryExists();
    }

    /**
     * Sikrer, at backup-mappen eksisterer. Hvis ikke, oprettes den.
     *
     * @return void
     */
    private function ensureBackupDirectoryExists(): void
    {
        if (!is_dir($this->backupDir)) {
            if (mkdir($this->backupDir, 0777, true)) {
                $this->logger->log("Oprettede backup-mappen: {$this->backupDir}");
            } else {
                $this->logger->log("Fejl ved oprettelse af backup-mappen: {$this->backupDir}", 'ERROR');
            }
        }
    }

    /**
     * Ruller ændringer tilbage ved at gendanne fra en specificeret eller seneste backup.
     *
     * @param string|null $backupTimestamp Tidsstempel for den backup, der skal gendannes. Hvis null, bruges seneste.
     * @return void
     */
    public function rollbackChanges(?string $backupTimestamp = null): void
    {
        $this->logger->log("Rollback-processen er startet.");

        if ($backupTimestamp !== null) {
            $backupFolder = $this->backupDir . $backupTimestamp;
            if (!is_dir($backupFolder)) {
                $this->logger->log("Backup med tidsstempel {$backupTimestamp} findes ikke.", 'ERROR');
                return;
            }
        } else {
            // Find alle backup-mapper
            $backupFolders = glob($this->backupDir . '*', GLOB_ONLYDIR);
            if (empty($backupFolders)) {
                $this->logger->log("Ingen backups fundet.", 'WARNING');
                return;
            }

            // Sorter backup-mapper efter dato (seneste først)
            usort($backupFolders, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            $backupFolder = $backupFolders[0];
        }

        $this->logger->log("Backup til gendannelse: {$backupFolder}");

        // Gendan filer fra backup
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($backupFolder, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $relativePath = str_replace($backupFolder, '', $item->getPathname());
                $destPath = $this->projectRoot . $relativePath;

                if ($item->isDir()) {
                    if (!is_dir($destPath)) {
                        if (mkdir($destPath, 0777, true)) {
                            $this->logger->log("Oprettede mappe: {$destPath}");
                        } else {
                            $this->logger->log("Fejl ved oprettelse af mappe: {$destPath}", 'ERROR');
                        }
                    }
                } else {
                    if (copy($item->getPathname(), $destPath)) {
                        $this->logger->log("Gendannede fil: {$destPath}");
                    } else {
                        $this->logger->log("Fejl ved gendannelse af fil: {$destPath}", 'ERROR');
                    }
                }
            }

            $this->logger->log("Rollback fuldført fra backup: {$backupFolder}");
            echo "Rollback fuldført fra backup: {$backupFolder}" . PHP_EOL;
        } catch (\Exception $e) {
            $this->logger->log("Undtagelse under rollback: " . $e->getMessage(), 'ERROR');
            echo "Fejl under rollback: " . $e->getMessage() . PHP_EOL;
        }
    }
}
