<?php
require_once __DIR__ . '/../config/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\LivroController;
use App\Utils\Auth;

Auth::verificarAutenticacao();

$pdo = getPDOConnection();
$authController = new AuthController($pdo);
$livroController = new LivroController($pdo);

$usuario = $authController->getUsuarioLogado();
$livros = $livroController->listarLivrosUsuario($usuario->getId());

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cadastrar_livro'])) {
        $titulo = $_POST['titulo'] ?? '';
        $autor = $_POST['autor'] ?? '';
        $genero = $_POST['genero'] ?? '';
        $sinopse = $_POST['sinopse'] ?? '';
        $condicao = $_POST['condicao'] ?? '';
        
        if ($livroController->cadastrar($titulo, $autor, $genero, $sinopse, $condicao, $usuario->getId())) {
            $mensagem = 'Livro cadastrado com sucesso!';
            header('Location: livros.php');
            exit;
        } else {
            $mensagem = 'Erro ao cadastrar livro.';
        }
    }
    
    if (isset($_POST['excluir_livro'])) {
        $livroId = $_POST['livro_id'] ?? '';
        if ($livroController->excluirLivro($livroId)) {
            $mensagem = 'Livro excluído com sucesso!';
            header('Location: livros.php');
            exit;
        } else {
            $mensagem = 'Erro ao excluir livro.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Livros - Troca de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Troca de Livros</h1>
        <nav>
            <a href="index.php">Início</a>
            <a href="perfil.php">Meu Perfil</a>
            <a href="?logout=true">Sair</a>
        </nav>
    </header>

    <main>
        <h2>Meus Livros</h2>
        
        <?php if ($mensagem): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <h3>Cadastrar Novo Livro</h3>
        <form method="POST">
            <input type="hidden" name="cadastrar_livro" value="1">
            
            <div>
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div>
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" required>
            </div>
            
            <div>
                <label for="genero">Gênero:</label>
                <input type="text" id="genero" name="genero" required>
            </div>
            
            <div>
                <label for="condicao">Condição:</label>
                <select id="condicao" name="condicao" required>
                    <option value="Novo">Novo</option>
                    <option value="Seminovo">Seminovo</option>
                    <option value="Usado">Usado</option>
                </select>
            </div>
            
            <div>
                <label for="sinopse">Sinopse:</label>
                <textarea id="sinopse" name="sinopse" required></textarea>
            </div>
            
            <button type="submit">Cadastrar Livro</button>
        </form>
        
        <h3>Meus Livros Cadastrados</h3>
        <?php if (empty($livros)): ?>
            <p>Você ainda não cadastrou nenhum livro.</p>
        <?php else: ?>
            <div class="livros-grid">
                <?php foreach ($livros as $livro): ?>
                    <div class="livro-card">
                        <h4><?= htmlspecialchars($livro->getTitulo()) ?></h4>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($livro->getAutor()) ?></p>
                        <p><strong>Gênero:</strong> <?= htmlspecialchars($livro->getGenero()) ?></p>
                        <p><strong>Condição:</strong> <?= htmlspecialchars($livro->getCondicao()) ?></p>
                        <p><?= nl2br(htmlspecialchars($livro->getSinopse())) ?></p>
                        <p><strong>Disponível:</strong> <?= $livro->isDisponivel() ? 'Sim' : 'Não' ?></p>
                        
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="excluir_livro" value="1">
                            <input type="hidden" name="livro_id" value="<?= $livro->getId() ?>">
                            <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este livro?')">Excluir</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php
    if (isset($_GET['logout'])) {
        $authController->logout();
    }
    ?>
</body>
</html>