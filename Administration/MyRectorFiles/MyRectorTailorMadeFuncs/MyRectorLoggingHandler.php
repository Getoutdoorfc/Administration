<?php

declare(strict_types=1);

namespace Administration\MyRectorFiles\MyRectorTailorMadeFuncs;

/**
 * Handler til at administrere logning for Rector-processen.
 */
class MyRectorLoggingHandler
{
    private string $logDir;
    private string $logFile;
    private static ?self $instance = null;

    /**
     * Privat konstruktør for singleton-mønsteret.
     *
     * @param string $projectRoot Rodstien til projektet.
     * @param string $timestamp Tidsstempel til logfilnavngivning.
     */
    private function __construct(string $projectRoot, string $timestamp)
    {
        $this->logDir = rtrim($projectRoot, DIRECTORY_SEPARATOR) . '/MyRectorLogsAndBackupFiles/Logs/';
        $this->ensureLogDirectoryExists();

        // Opret kun én logfil pr. proces med det givne tidsstempel
        if (empty($this->logFile)) {
            $this->logFile = $this->logDir . 'rector_log_' . $timestamp . '.log';
        }
    }

    /**
     * Henter singleton-instansen af logging handleren.
     *
     * @param string $projectRoot Rodstien til projektet.
     * @param string $timestamp Tidsstempel til logfilnavngivning.
     * @return self Singleton-instansen.
     */
    public static function getInstance(string $projectRoot, string $timestamp): self
    {
        if (self::$instance === null) {
            self::$instance = new self($projectRoot, $timestamp);
        }
        return self::$instance;
    }

    /**
     * Sikrer, at logmappen eksisterer.
     *
     * @return void
     */
    private function ensureLogDirectoryExists(): void
    {
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
    }

    /**
     * Logger en besked til logfilen.
     *
     * @param string $message Beskeden der skal logges.
     * @param string $level Logniveau (f.eks. INFO, WARNING, ERROR).
     * @param string|null $file Filnavn for fejlen (valgfrit).
     * @param int|null $line Linjenummer for fejlen (valgfrit).
     * @return void
     */
    public function log(string $message, string $level = 'INFO', ?string $file = null, ?int $line = null): void
    {
        $formattedMessage = sprintf(
            "[%s] [%s] %s",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );

        if ($file !== null && $line !== null) {
            $formattedMessage .= sprintf(" [Fil: %s, Linje: %d]", $file, $line);
        }

        $formattedMessage .= PHP_EOL;
        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
    }
}
