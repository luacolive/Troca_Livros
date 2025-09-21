<?php
require_once __DIR__ . '/../config/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\LivroController;

$pdo = getPDOConnection();
$authController = new AuthController($pdo);
$livroController = new LivroController($pdo);

$livros = $livroController->listarLivros();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troca de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Troca de Livros</h1>
        <nav>
            <?php if ($authController->estaLogado()): ?>
                <a href="perfil.php">Meu Perfil</a>
                <a href="livros.php">Meus Livros</a>
                <a href="?logout=true">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a href="cadastro.php">Cadastrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Livros Disponíveis para Troca</h2>
        
        <?php if (empty($livros)): ?>
            <p>Nenhum livro disponível no momento.</p>
        <?php else: ?>
            <div class="livros-grid">
                <?php foreach ($livros as $livro): ?>
                    <div class="livro-card">
                        <h3><?= htmlspecialchars($livro->getTitulo()) ?></h3>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($livro->getAutor()) ?></p>
                        <p><strong>Gênero:</strong> <?= htmlspecialchars($livro->getGenero()) ?></p>
                        <p><strong>Condição:</strong> <?= htmlspecialchars($livro->getCondicao()) ?></p>
                        <p><?= nl2br(htmlspecialchars($livro->getSinopse())) ?></p>
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