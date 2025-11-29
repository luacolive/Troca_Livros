<?php
// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclui a configuração do banco de dados
require_once __DIR__ . '/database.php';

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>