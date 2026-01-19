<?php
/**
 * Simple Logging System for Dyslexia App
 */

class Logger {
    private static $instance = null;
    private $logFile;
    private $logLevel;

    // Log levels
    const DEBUG = 0;
    const INFO = 1;
    const WARNING = 2;
    const ERROR = 3;

    private function __construct() {
        $this->logFile = LOG_FILE ?: __DIR__ . '/../logs/app.log';
        $this->logLevel = $this->getLogLevelConstant(LOG_LEVEL ?: 'warning');

        // Create logs directory if it doesn't exist
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function getLogLevelConstant($level) {
        switch (strtolower($level)) {
            case 'debug': return self::DEBUG;
            case 'info': return self::INFO;
            case 'warning': return self::WARNING;
            case 'error': return self::ERROR;
            default: return self::WARNING;
        }
    }

    public function log($level, $message, $context = []) {
        if ($level < $this->logLevel) {
            return;
        }

        $levelName = $this->getLogLevelName($level);
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);

        $logEntry = sprintf("[%s] %s: %s%s\n", $timestamp, $levelName, $message, $contextStr);

        // Write to file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);

        // In development, also output to screen for debugging
        if (APP_ENV === 'development' && $level >= self::WARNING) {
            error_log($logEntry);
        }
    }

    private function getLogLevelName($level) {
        switch ($level) {
            case self::DEBUG: return 'DEBUG';
            case self::INFO: return 'INFO';
            case self::WARNING: return 'WARNING';
            case self::ERROR: return 'ERROR';
            default: return 'UNKNOWN';
        }
    }

    public function debug($message, $context = []) {
        $this->log(self::DEBUG, $message, $context);
    }

    public function info($message, $context = []) {
        $this->log(self::INFO, $message, $context);
    }

    public function warning($message, $context = []) {
        $this->log(self::WARNING, $message, $context);
    }

    public function error($message, $context = []) {
        $this->log(self::ERROR, $message, $context);
    }
}

// Global logging functions for easy use
function log_debug($message, $context = []) {
    Logger::getInstance()->debug($message, $context);
}

function log_info($message, $context = []) {
    Logger::getInstance()->info($message, $context);
}

function log_warning($message, $context = []) {
    Logger::getInstance()->warning($message, $context);
}

function log_error($message, $context = []) {
    Logger::getInstance()->error($message, $context);
}
?>
