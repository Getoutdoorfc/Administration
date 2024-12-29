<?php
namespace Administration\Core\Managers;

defined('ABSPATH') || exit;

/**
 * Final Logger class adhering to Singleton pattern.
 */
final class LoggerManager {

    private static $instance = null;
    private $logFile;
    private const MAX_FILE_SIZE = 5242880; // 5MB
    private const LEVELS = array('DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL');

    public static function getInstance(): LoggerManager {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $plugin_dir = plugin_dir_path(dirname(__FILE__, 2));
        $log_dir = trailingslashit($plugin_dir) . 'logs';
        $this->logFile = trailingslashit($log_dir) . 'plugin-adm.log';
        $this->initializeLogFile($log_dir, $this->logFile);
    }

    private function initializeLogFile(string $log_dir, string $logFile): void {
        if (!wp_mkdir_p($log_dir)) {
            error_log("Logger: Failed to create log directory at {$log_dir}");
        }

        if (!file_exists($logFile)) {
            if (false === file_put_contents($logFile, '')) {
                error_log("Logger: Failed to create log file at {$logFile}");
            }

            if (!chmod($logFile, 0640)) {
                error_log("Logger: Failed to set permissions for log file at {$logFile}");
            }
        }
    }

    public function log(string $level, string $message, array $context = array()): void {
        if (!in_array($level, self::LEVELS, true)) {
            $level = 'INFO';
        }

        $timestamp = current_time('Y-m-d H:i:s');
        $backtrace = $this->formatBacktrace();
        $encodedContext = $this->formatContext($context);

        // Formater logbeskeden med fast bredde
        $logEntry = sprintf(
            "[%-19s] %-8s %-120s | %s | %s\n",
            $timestamp,            // Tidsstempel
            strtoupper($level),    // Logniveau (DEBUG, INFO, etc.)
            $message,              // Selve logbeskeden
            $backtrace,            // Lokationen (fil og linje)
            $encodedContext        // Kontekst
        );

        if (file_exists($this->logFile) && filesize($this->logFile) >= self::MAX_FILE_SIZE) {
            $this->rotateLogs();
        }

        $fileHandle = fopen($this->logFile, 'a');
        if ($fileHandle) {
            if (flock($fileHandle, LOCK_EX)) {
                fwrite($fileHandle, $logEntry);
                fflush($fileHandle);
                flock($fileHandle, LOCK_UN);
            } else {
                error_log("Logger: Failed to acquire lock for log file at {$this->logFile}");
            }
            fclose($fileHandle);
        } else {
            error_log("Logger: Failed to open log file at {$this->logFile} for writing.");
        }
    }

    private function formatBacktrace(): string {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $backtrace[2] ?? $backtrace[1];
        $file = $caller['file'] ?? 'Unknown file';
        $line = $caller['line'] ?? 'Unknown line';
        return sprintf("Location: %s line %d", $file, $line);
    }

    private function formatContext(array $context): string {
        if (empty($context)) {
            return '';
        }

        $encodedContext = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $encodedContext = json_encode(['error' => 'Context encoding failed']);
        }

        return $encodedContext;
    }

    public function debug(string $message, array $context = array()): void {
        $this->log('DEBUG', $message, $context);
    }

    public function info(string $message, array $context = array()): void {
        $this->log('INFO', $message, $context);
    }

    public function warning(string $message, array $context = array()): void {
        $this->log('WARNING', $message, $context);
    }

    public function error(string $message, array $context = array()): void {
        $this->log('ERROR', $message, $context);
    }

    public function critical(string $message, array $context = array()): void {
        $this->log('CRITICAL', $message, $context);
    }

    private function rotateLogs(): void {
        $logDir = dirname($this->logFile);
        $archiveDir = trailingslashit($logDir) . 'archive';

        if (!wp_mkdir_p($archiveDir)) {
            error_log("Logger: Failed to create archive directory at {$archiveDir}");
            return;
        }

        $timestamp = current_time('Y-m-d_H-i-s');
        $newName = $archiveDir . '/plugin-log-' . $timestamp . '.txt';

        if (!rename($this->logFile, $newName)) {
            error_log("Logger: Failed to rotate log file from {$this->logFile} to {$newName}");
            return;
        }

        if (false === file_put_contents($this->logFile, '')) {
            error_log("Logger: Failed to recreate log file at {$this->logFile} after rotation.");
        } else {
            if (!chmod($this->logFile, 0640)) {
                error_log("Logger: Failed to set permissions for log file at {$this->logFile} after rotation.");
            }
        }
    }

    private function __clone() {}
    public function __wakeup() {}
}
