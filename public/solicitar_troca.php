<?php
require_once __DIR__ . '/../config/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\TrocaController;
use App\Controllers\LivroController;
use App\Utils\Auth;

Auth::verificarAutenticacao();

$pdo = getPDOConnection();
$authController = new AuthController($pdo);
$trocaController = new TrocaController($pdo);
$livroController = new LivroController($pdo);

$usuario = $authController->getUsuarioLogado();
$mensagem = '';
$sucesso = false;

// Buscar livro solicitado
$livroSolicitadoId = $_GET['livro_id'] ?? null;
$livroSolicitado = $livroSolicitadoId ? $livroController->buscarLivro($livroSolicitadoId) : null;

if (!$livroSolicitado) {
    header('Location: index.php');
    exit;
}

// Buscar livros do usuário para ofertar
$livrosUsuario = $livroController->listarLivrosUsuario($usuario->getId());

// Processar solicitação de troca
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livroOfertadoId = $_POST['livro_ofertado_id'] ?? null;
    
    if ($livroOfertadoId) {
        $resultado = $trocaController->solicitarTroca($livroOfertadoId, $livroSolicitadoId, $usuario->getId());
        $sucesso = $resultado['sucesso'];
        $mensagem = $resultado['mensagem'];
        
        if ($sucesso) {
            header('Location: minhas_trocas.php');
            exit;
        }
    } else {
        $mensagem = 'Selecione um livro para ofertar.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Troca - Troca de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
    <h1>Troca de Livros</h1>
    <nav>
        <a href="index.php">Início</a>
        <?php if ($authController->estaLogado()): ?>
            <a href="perfil.php">Meu Perfil</a>
            <a href="livros.php">Meus Livros</a>
            <a href="minhas_trocas.php">Minhas Trocas</a>
            <a href="?logout=true">Sair</a>
        <?php else: ?>
            <a href="login.php">Entrar</a>
            <a href="cadastro.php">Cadastrar</a>
        <?php endif; ?>
    </nav>
</header>

    <main>
        <h2>Solicitar Troca</h2>
        
        <?php if ($mensagem): ?>
            <div class="<?= $sucesso ? 'mensagem' : 'erro' ?>"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <div class="livro-info">
            <h3>Livro que você deseja:</h3>
            <div class="livro-card">
                <h4><?= htmlspecialchars($livroSolicitado->getTitulo()) ?></h4>
                <p><strong>Autor:</strong> <?= htmlspecialchars($livroSolicitado->getAutor()) ?></p>
                <p><strong>Gênero:</strong> <?= htmlspecialchars($livroSolicitado->getGenero()) ?></p>
                <p><strong>Condição:</strong> <?= htmlspecialchars($livroSolicitado->getCondicao()) ?></p>
                <p><?= nl2br(htmlspecialchars($livroSolicitado->getSinopse())) ?></p>
            </div>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="livro_ofertado_id">Selecione um livro seu para troca:</label>
                <select id="livro_ofertado_id" name="livro_ofertado_id" required>
                    <option value="">Selecione um livro</option>
                    <?php foreach ($livrosUsuario as $livro): ?>
                        <option value="<?= $livro->getId() ?>">
                            <?= htmlspecialchars($livro->getTitulo()) ?> (<?= htmlspecialchars($livro->getCondicao()) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Solicitar Troca</button>
            <a href="index.php" class="button">Cancelar</a>
        </form>
    </main>
</body>
</html>