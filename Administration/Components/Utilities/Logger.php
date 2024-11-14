<?php
namespace Administration\Components\Utilities;

defined('ABSPATH') || exit;

/**
 * Final Logger class adhering to Singleton pattern.
 */
final class Logger {

    /**
     * The Singleton instance.
     *
     * @var Logger|null
     */
    private static $instance = null;

    /**
     * Path to the log file.
     *
     * @var string
     */
    private $logFile;

    /**
     * Maximum allowed log file size in bytes before rotation (5MB).
     *
     * @var int
     */
    private const MAX_FILE_SIZE = 5242880; // 5 * 1024 * 1024

    /**
     * Available logging levels.
     *
     * @var array
     */
    private const LEVELS = array('DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL');

    /**
     * Get the Singleton instance of Logger.
     *
     * @return Logger
     */
    public static function getInstance(): Logger {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {
        // Antag, at Logger.php er placeret i src/Utilities
        $plugin_dir = plugin_dir_path(dirname(__FILE__, 2));
        $log_dir = trailingslashit($plugin_dir) . 'logs';
        $this->logFile = trailingslashit($log_dir) . 'plugin-log.txt';
        $this->initializeLogFile($log_dir, $this->logFile);
    }

    /**
     * Initialize the log file, ensuring directory and file exist with correct permissions.
     *
     * @param string $log_dir Log directory path.
     * @param string $logFile Log file path.
     *
     * @return void
     */
    private function initializeLogFile(string $log_dir, string $logFile): void {
        // Opret log directory, hvis den ikke eksisterer
        if (!wp_mkdir_p($log_dir)) {
            // Log fejlen til PHP error log, men undgå at stoppe execution
            error_log("Logger: Failed to create log directory at {$log_dir}");
        }

        // Opret log file, hvis den ikke eksisterer
        if (!file_exists($logFile)) {
            if (false === file_put_contents($logFile, '')) {
                // Log fejlen til PHP error log
                error_log("Logger: Failed to create log file at {$logFile}");
            }
            // Sæt sikre filrettigheder
            if (!chmod($logFile, 0640)) {
                error_log("Logger: Failed to set permissions for log file at {$logFile}");
            }
        }
    }

    /**
     * Log a message with the given level and context.
     *
     * @param string $level   The log level (DEBUG, INFO, WARNING, ERROR, CRITICAL).
     * @param string $message The message to log.
     * @param array  $context Additional context for the log message.
     *
     * @return void
     */
    public function log(string $level, string $message, array $context = array()): void {
        if (!in_array($level, self::LEVELS, true)) {
            $level = 'INFO';
        }

        // Brug WordPress' aktuelle tid
        $timestamp = current_time('Y-m-d H:i:s');
        $encodedContext = '';

        // Kod konteksten til JSON, hvis den ikke er tom
        if (!empty($context)) {
            $encodedContext = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Håndter JSON kodningsfejl
                $encodedContext = json_encode(['message' => 'Context encoding error', 'original_context' => $context]);
            }
        }

        // Formater logbeskeden
        $logEntry = sprintf("[%s] %s: %s %s\n", $timestamp, $level, $message, $encodedContext);

        // Log rotation: Rotér hvis filen overskrider MAX_FILE_SIZE
        if (file_exists($this->logFile) && filesize($this->logFile) >= self::MAX_FILE_SIZE) {
            $this->rotateLogs();
        }

        // Skriv til logfil med fillåse for at forhindre race conditions
        $fileHandle = fopen($this->logFile, 'a');
        if ($fileHandle) {
            if (flock($fileHandle, LOCK_EX)) { // Lås filen eksklusivt
                fwrite($fileHandle, $logEntry);
                fflush($fileHandle); // Sørg for at alle data er skrevet
                flock($fileHandle, LOCK_UN); // Lås op
            } else {
                // Håndter låsefejl
                error_log("Logger: Failed to acquire lock for log file at {$this->logFile}");
            }
            fclose($fileHandle);
        } else {
            // Håndter fejlen ved at åbne filen
            error_log("Logger: Failed to open log file at {$this->logFile} for writing.");
        }
    }

    /**
     * Log a DEBUG level message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug(string $message, array $context = array()): void {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log an INFO level message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info(string $message, array $context = array()): void {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log a WARNING level message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning(string $message, array $context = array()): void {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log an ERROR level message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error(string $message, array $context = array()): void {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log a CRITICAL level message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical(string $message, array $context = array()): void {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Rotate the log file by moving it to the archive directory with a timestamp.
     *
     * @return void
     */
    private function rotateLogs(): void {
        $logDir = dirname($this->logFile);
        $archiveDir = trailingslashit($logDir) . 'archive';

        // Opret arkivmappen, hvis den ikke eksisterer
        if (!wp_mkdir_p($archiveDir)) {
            error_log("Logger: Failed to create archive directory at {$archiveDir}");
            return;
        }

        // Generer et tidsstempel
        $timestamp = current_time('Y-m-d_H-i-s');
        $newName = $archiveDir . '/plugin-log-' . $timestamp . '.txt';

        // Flyt logfilen til arkivet
        if (!rename($this->logFile, $newName)) {
            error_log("Logger: Failed to rotate log file from {$this->logFile} to {$newName}");
            return;
        }

        // Opret en ny, tom logfil efter rotation
        if (false === file_put_contents($this->logFile, '')) {
            error_log("Logger: Failed to recreate log file at {$this->logFile} after rotation.");
        } else {
            // Sæt sikre filrettigheder
            if (!chmod($this->logFile, 0640)) {
                error_log("Logger: Failed to set permissions for log file at {$this->logFile} after rotation.");
            }
        }
    }

    /**
     * Prevent cloning of the Singleton instance.
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the Singleton instance.
     */
    private function __wakeup() {}
}