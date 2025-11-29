<?php
// DEBUG - Mostrar todos os erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 DEBUG: API iniciada\n";

// Carregar APENAS database e sessão
require_once __DIR__ . '/../config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "🔧 DEBUG: Database e sessão carregados\n";

// ⭐⭐ CARREGAR TODAS AS CLASSES GENERIC NA ORDEM CORRETA ⭐⭐
require_once __DIR__ . '/../app/Generic/Retorno.php';
require_once __DIR__ . '/../app/Generic/Acao.php';
require_once __DIR__ . '/../app/Generic/Controller.php';
require_once __DIR__ . '/../app/Generic/Endpoint.php';
require_once __DIR__ . '/../app/Generic/Rotas.php';
require_once __DIR__ . '/../app/Generic/MysqlSingleton.php';
require_once __DIR__ . '/../app/Generic/Autoload.php';

// Carregar controllers
require_once __DIR__ . '/../app/Controllers/ApiLivroController.php';

echo "🔧 DEBUG: Todas as classes carregadas\n";

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

    // Rotas para Livros
    $rotas->adicionar('GET', '/api/livros', 'ApiLivroController@listar');
    $rotas->adicionar('GET', '/api/livros/{id}', 'ApiLivroController@buscar');
    $rotas->adicionar('POST', '/api/livros', 'ApiLivroController@criar');
    $rotas->adicionar('PUT', '/api/livros/{id}', 'ApiLivroController@atualizar');
    $rotas->adicionar('DELETE', '/api/livros/{id}', 'ApiLivroController@excluir');
    echo "🔧 DEBUG: Rotas adicionadas\n";

    // Executar a rota
    $uri = str_replace('/Troca_Livros-main/public/api.php', '', $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = $uri ?: '/';
$rotas->executar();;
    
} catch (Exception $e) {
    echo "🔧 DEBUG: Erro: " . $e->getMessage();
}
?>