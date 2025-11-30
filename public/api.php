<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Autoload
require_once __DIR__ . '/../app/Generic/Autoload.php';
Generic\Autoload::register();

// INCLUIR JWT CONFIG MANUALMENTE
require_once __DIR__ . '/../config/jwt_config.php';

// JWT
require_once __DIR__ . '/../vendor/firebase/php-jwt/JWTSimple.php';

// Headers CORS
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $rotas = new Generic\Rotas();

    // Rotas públicas
    $rotas->adicionar('POST', '/auth/login', 'AuthController@login');
    $rotas->adicionar('POST', '/auth/registrar', 'AuthController@registrar');
    $rotas->adicionar('GET', '/auth/perfil', 'AuthController@perfil');

    // Rotas de livros
    $rotas->adicionar('GET', '/livros', 'ApiLivroController@listar');
    $rotas->adicionar('GET', '/livros/{id}', 'ApiLivroController@buscar');
    $rotas->adicionar('POST', '/livros', 'ApiLivroController@criar');
    $rotas->adicionar('PUT', '/livros/{id}', 'ApiLivroController@atualizar');
    $rotas->adicionar('DELETE', '/livros/{id}', 'ApiLivroController@excluir');

    $rotas->executar();
    
} catch (Exception $e) {
    // ⚠️ DEBUG do erro
    error_log("ERRO API: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["erro" => "Erro interno do servidor"]);
}
?>