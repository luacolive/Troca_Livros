<?php
// DEBUG - Mostrar todos os erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 DEBUG: API iniciada\n";

// ⭐⭐ CARREGAR E REGISTRAR AUTOLOAD PRIMEIRO ⭐⭐
require_once __DIR__ . '/../app/Generic/Autoload.php';
Generic\Autoload::register();

echo "🔧 DEBUG: Autoload registrado\n";

// Configurar cabeçalhos CORS
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Se for OPTIONS (preflight), retorna sucesso
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

echo "🔧 DEBUG: Headers configurados\n";

try {
    // Sistema de rotas da API
    $rotas = new Generic\Rotas();
    echo "🔧 DEBUG: Rotas criadas\n";

    // Rotas para Livros - URLs BONITAS
    $rotas->adicionar('GET', '/livros', 'ApiLivroController@listar');
    $rotas->adicionar('GET', '/livros/{id}', 'ApiLivroController@buscar');
    $rotas->adicionar('POST', '/livros', 'ApiLivroController@criar');
    $rotas->adicionar('PUT', '/livros/{id}', 'ApiLivroController@atualizar');
    $rotas->adicionar('DELETE', '/livros/{id}', 'ApiLivroController@excluir');
    echo "🔧 DEBUG: Rotas adicionadas\n";

    // Executar a rota
    $rotas->executar();
    echo "🔧 DEBUG: Rotas executadas\n";
    
} catch (Exception $e) {
    echo "🔧 DEBUG: Erro: " . $e->getMessage();
}
?>