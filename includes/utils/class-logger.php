<?php

namespace Includes;

class Logger
{
    private static $logFile;

    public static function init($logFile = null): void
    {
        $defaultDir = __DIR__ . '/../../logs/';
        self::$logFile = $logFile ?: $defaultDir . 'application_' . date('Y-m-d') . '.log';

        // Create the logs directory if it doesn't exist
        if (!file_exists($defaultDir)) {
            mkdir($defaultDir, 0755, true);
        }
    }

    public static function log($level, $message, $context = [], $category = 'application'): void
    {
        if (!self::$logFile) {
            self::init();
        }

        // Build the log file path dynamically based on the category
        $defaultDir = __DIR__ . '/../../logs/';
        $logFile = $defaultDir . $category . '_' . date('Y-m-d') . '.log';

        // Ensure the directory exists
        if (!file_exists($defaultDir)) {
            mkdir($defaultDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextString = self::formatContext($context);

        // Ensure JSON validity
        $logData = json_encode([
            'timestamp' => $timestamp,
            'level'     => $level,
            'message'   => $message,
            'context'   => $contextString,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Write to log file
        file_put_contents($logFile, $logData . PHP_EOL, FILE_APPEND);
    }

    public static function debug($message, $context = [], $category = 'application'): void
    {
        self::log('DEBUG', $message, $context, $category);
    }

    public static function info($message, $context = [], $category = 'application'): void
    {
        self::log('INFO', $message, $context, $category);
    }

    public static function warning($message, $context = [], $category = 'application'): void
    {
        self::log('WARNING', $message, $context, $category);
    }

    public static function error($message, $context = [], $category = 'application'): void
    {
        self::log('ERROR', $message, $context, $category);
    }

    public static function critical($message, $context = [], $category = 'application'): void
    {
        self::log('CRITICAL', $message, $context, $category);
    }

    private static function formatContext($context): array
    {
        // Ensure context is an array
        if (!is_array($context)) {
            return ['error' => 'Invalid context type'];
        }

        // Validate JSON encoding for each context element
        $validContext = [];
        foreach ($context as $key => $value) {
            $validContext[$key] = is_scalar($value) || $value === null
                ? $value
                : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $validContext;
    }
}
