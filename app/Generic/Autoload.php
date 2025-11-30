<?php
namespace Generic;

class Autoload {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Mapear namespaces para diretórios
            $prefixes = [
                'App\\' => __DIR__ . '/../',
                'Controllers\\' => __DIR__ . '/../Controllers/',
                'Generic\\' => __DIR__ . '/',
                'config\\' => __DIR__ . '/../../config/',
                'Middleware\\' => __DIR__ . '/../Middleware/'
            ];

            foreach ($prefixes as $prefix => $base_dir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    continue;
                }

                $relative_class = substr($class, $len);
                $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

                if (file_exists($file)) {
                    require $file;
                    return true;
                }
            }

            return false;
        });
    }
}
?>