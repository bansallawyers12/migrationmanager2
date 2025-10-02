<?php
/**
 * Route Conversion Script for Laravel 12 Migration
 * This script converts string-based controller syntax to class-based syntax
 * Based on the controller audit: 503 total references
 */

class RouteConverter
{
    private $controllerMapping;
    private $conversions = [];
    private $errors = [];

    public function __construct()
    {
        // Include the controller mapping
        require_once __DIR__ . '/controller-import-mapper.php';
        $mapper = new ControllerImportMapper();
        $this->controllerMapping = $mapper->getControllerMapping();
    }

    /**
     * Convert a single route string from old to new syntax
     */
    public function convertRouteString(string $routeString): string
    {
        // Pattern to match: 'Controller@method'
        $pattern = "/'([A-Za-z\\\\]+Controller)@([a-zA-Z_]+)'/";
        
        if (preg_match($pattern, $routeString, $matches)) {
            $oldController = $matches[1];
            $method = $matches[2];
            
            if (isset($this->controllerMapping[$oldController])) {
                $newController = $this->controllerMapping[$oldController];
                $controllerClass = basename(str_replace('\\', '/', $newController));
                
                return "[{$controllerClass}::class, '{$method}']";
            } else {
                $this->errors[] = "Controller not found in mapping: {$oldController}";
                return $routeString; // Return original if not found
            }
        }
        
        return $routeString;
    }

    /**
     * Convert entire route file content
     */
    public function convertRouteFile(string $content): string
    {
        $lines = explode("\n", $content);
        $convertedLines = [];
        
        foreach ($lines as $lineNumber => $line) {
            $originalLine = $line;
            
            // Skip comments and empty lines
            if (trim($line) === '' || strpos(trim($line), '//') === 0 || strpos(trim($line), '/*') === 0) {
                $convertedLines[] = $line;
                continue;
            }
            
            // Convert route definitions
            if (strpos($line, 'Route::') !== false) {
                $convertedLine = $this->convertRouteString($line);
                
                if ($convertedLine !== $originalLine) {
                    $this->conversions[] = [
                        'line' => $lineNumber + 1,
                        'original' => trim($originalLine),
                        'converted' => trim($convertedLine)
                    ];
                }
                
                $convertedLines[] = $convertedLine;
            } else {
                $convertedLines[] = $line;
            }
        }
        
        return implode("\n", $convertedLines);
    }

    /**
     * Get conversion statistics
     */
    public function getConversionStats(): array
    {
        $stats = [
            'total_conversions' => count($this->conversions),
            'errors' => count($this->errors),
            'controllers_converted' => [],
            'methods_converted' => []
        ];

        foreach ($this->conversions as $conversion) {
            // Extract controller and method from original string
            if (preg_match("/'([A-Za-z\\\\]+Controller)@([a-zA-Z_]+)'/", $conversion['original'], $matches)) {
                $controller = $matches[1];
                $method = $matches[2];
                
                if (!in_array($controller, $stats['controllers_converted'])) {
                    $stats['controllers_converted'][] = $controller;
                }
                
                if (!in_array($method, $stats['methods_converted'])) {
                    $stats['methods_converted'][] = $method;
                }
            }
        }

        return $stats;
    }

    /**
     * Get all conversions made
     */
    public function getConversions(): array
    {
        return $this->conversions;
    }

    /**
     * Get all errors encountered
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Generate conversion report
     */
    public function generateReport(): string
    {
        $stats = $this->getConversionStats();
        $report = "Route Conversion Report\n";
        $report .= "=====================\n\n";
        $report .= "Generated on: " . date('Y-m-d H:i:s') . "\n";
        $report .= "Total conversions: " . $stats['total_conversions'] . "\n";
        $report .= "Errors: " . $stats['errors'] . "\n";
        $report .= "Controllers converted: " . count($stats['controllers_converted']) . "\n";
        $report .= "Methods converted: " . count($stats['methods_converted']) . "\n\n";

        if (!empty($stats['controllers_converted'])) {
            $report .= "Controllers converted:\n";
            foreach ($stats['controllers_converted'] as $controller) {
                $report .= "- {$controller}\n";
            }
            $report .= "\n";
        }

        if (!empty($this->errors)) {
            $report .= "Errors encountered:\n";
            foreach ($this->errors as $error) {
                $report .= "- {$error}\n";
            }
            $report .= "\n";
        }

        return $report;
    }

    /**
     * Validate converted routes
     */
    public function validateConversions(): array
    {
        $validation = [
            'valid' => [],
            'invalid' => [],
            'warnings' => []
        ];

        foreach ($this->conversions as $conversion) {
            $converted = $conversion['converted'];
            
            // Check if the converted syntax is valid
            if (strpos($converted, '::class') !== false && strpos($converted, '[') !== false) {
                $validation['valid'][] = $conversion;
            } else {
                $validation['invalid'][] = $conversion;
            }
        }

        return $validation;
    }

    /**
     * Process route file and generate converted version
     */
    public function processRouteFile(string $inputFile, string $outputFile = null): bool
    {
        if (!file_exists($inputFile)) {
            $this->errors[] = "Input file not found: {$inputFile}";
            return false;
        }

        $content = file_get_contents($inputFile);
        if ($content === false) {
            $this->errors[] = "Failed to read input file: {$inputFile}";
            return false;
        }

        $convertedContent = $this->convertRouteFile($content);
        
        if ($outputFile === null) {
            $outputFile = $inputFile . '.converted';
        }

        $result = file_put_contents($outputFile, $convertedContent);
        if ($result === false) {
            $this->errors[] = "Failed to write output file: {$outputFile}";
            return false;
        }

        return true;
    }
}

// Usage example (uncomment to test):
/*
$converter = new RouteConverter();

// Process the route file
$success = $converter->processRouteFile('routes/web.php', 'routes/web.converted.php');

if ($success) {
    echo "Route conversion completed successfully!\n";
    
    // Generate and display report
    $report = $converter->generateReport();
    echo $report;
    
    // Save report to file
    file_put_contents('conversion-report.txt', $report);
    echo "Conversion report saved to: conversion-report.txt\n";
} else {
    echo "Route conversion failed!\n";
    $errors = $converter->getErrors();
    foreach ($errors as $error) {
        echo "- {$error}\n";
    }
}
*/
