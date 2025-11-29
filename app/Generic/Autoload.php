<?php
namespace Generic;

class Autoload {
    public static function register() {
        spl_autoload_register(function ($class) {
            $class = str_replace('\\', '/', $class);
            $base_dir = __DIR__ . '/../';
            $file = $base_dir . $class . '.php';
            
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
?>