<?php
// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para carregar classes automaticamente
spl_autoload_register(function ($class) {
    // Mapeamento de namespaces para diretórios
    $prefixes = [
        'App\\' => __DIR__ . '/../app/',
        'app\\' => __DIR__ . '/../app/', // ← ADICIONE ESTA LINHA (namespace em lowercase)
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
            return;
        }
    }
    
    // Se não encontrou, mostra erro detalhado
    echo "Classe não encontrada: " . $class . "<br>";
    echo "Procurando em: " . $file . "<br>";
});

// Inclui a configuração do banco de dados
require_once __DIR__ . '/database.php';

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}