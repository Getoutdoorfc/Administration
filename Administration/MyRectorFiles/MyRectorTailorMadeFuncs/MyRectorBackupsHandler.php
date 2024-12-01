<?php

declare(strict_types=1);

namespace Administration\MyRectorFiles\MyRectorTailorMadeFuncs;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Handler til at administrere backup-processen for Rector.
 */
class MyRectorBackupsHandler
{
    private string $projectRoot;
    private string $backupDir;
    private MyRectorLoggingHandler $logger;
    private ?string $backupFolder = null;
    private string $timestamp;

    /**
     * Konstruktor.
     *
     * @param string $projectRoot Rodstien til projektet.
     * @param string $timestamp Tidsstemplet for processen.
     */
    public function __construct(string $projectRoot, string $timestamp)
    {
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
        $this->backupDir = $this->projectRoot . '/MyRectorLogsAndBackupFiles/Backups/';
        $this->logger = MyRectorLoggingHandler::getInstance($projectRoot, $timestamp);
        $this->timestamp = $timestamp;
        $this->ensureBackupDirectoryExists();
    }

    /**
     * Sikrer, at backup-mappen eksisterer.
     *
     * @return void
     */
    private function ensureBackupDirectoryExists(): void
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    /**
     * Opretter backup af de angivne stier.
     *
     * @param array<string> $paths Stier der skal tages backup af.
     * @return void
     */
    public function createBackups(array $paths): void
    {
        $this->logger->log("Backup-processen er startet.");

        // Brug det samme tidsstempel som givet fra rector.php
        if ($this->backupFolder === null) {
            $this->backupFolder = $this->backupDir . $this->timestamp;
            if (!mkdir($this->backupFolder, 0777, true)) {
                $this->logger->log(
                    "Kunne ikke oprette backup-mappe: {$this->backupFolder}",
                    'ERROR',
                    __FILE__,
                    __LINE__
                );
                throw new \RuntimeException("Backup-mappen kunne ikke oprettes.");
            }
            $this->logger->log("Oprettede backup-mappe: {$this->backupFolder}");
        }

        foreach ($paths as $path) {
            $source = realpath($path);
            if ($source === false) {
                $this->logger->log("Ugyldig sti: {$path}", 'WARNING');
                continue;
            }

            if (is_dir($source)) {
                $this->logger->log("Kopierer mappe: {$source}");
                $this->copyDirectory($source, $this->backupFolder);
            } else {
                $destFile = $this->backupFolder . DIRECTORY_SEPARATOR . basename($source);
                if (copy($source, $destFile)) {
                    $this->logger->log("Kopierede fil: {$source} til {$destFile}");
                } else {
                    $this->logger->log(
                        "Fejl ved kopiering af fil: {$source}",
                        'ERROR',
                        __FILE__,
                        __LINE__
                    );
                }
            }
        }

        $this->logger->log("Backup-processen er afsluttet.");
    }

    /**
     * Kopierer en mappe rekursivt.
     *
     * @param string $source Kildemappen.
     * @param string $destination Destinationsmappen.
     * @return void
     */
    private function copyDirectory(string $source, string $destination): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $item->getPathname());
            $destPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                mkdir($destPath, 0777, true);
            } elseif ($item->isLink()) {
                $this->logger->log("Symbolsk link sprunget over: {$item->getPathname()}", 'WARNING');
            } else {
                if (!copy($item->getPathname(), $destPath)) {
                    $this->logger->log(
                        "Fejl ved kopiering af fil: {$item->getPathname()} til {$destPath}",
                        'ERROR',
                        __FILE__,
                        __LINE__
                    );
                }
            }
        }
    }
}
