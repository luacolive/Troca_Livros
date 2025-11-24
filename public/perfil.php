<?php
require_once __DIR__ . '/../config/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\UsuarioController;
use App\Utils\Auth;

Auth::verificarAutenticacao();

$pdo = getPDOConnection();
$authController = new AuthController($pdo);
$usuarioController = new UsuarioController($pdo);

$usuario = $authController->getUsuarioLogado();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    
    if ($usuarioController->atualizarPerfil($usuario->getId(), $nome, $email, $endereco, $telefone)) {
        $mensagem = 'Perfil atualizado com sucesso!';
        // Atualizar dados na sessão
        $_SESSION['usuario_nome'] = $nome;
        header('Location: perfil.php');
        exit;
    } else {
        $mensagem = 'Erro ao atualizar perfil.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Troca de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Troca de Livros</h1>
        <nav>
            <a href="index.php">Início</a>
            <a href="livros.php">Meus Livros</a>
            <a href="?logout=true">Sair</a>
        </nav>
    </header>

    <main>
        <h2>Meu Perfil</h2>
        
        <?php if ($mensagem): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario->getNome()) ?>" required>
            </div>
            
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario->getEmail()) ?>" required>
            </div>
            
            <div>
                <label for="endereco">Endereço:</label>
                <textarea id="endereco" name="endereco"><?= htmlspecialchars($usuario->getEndereco() ?? '') ?></textarea>
            </div>
            
            <div>
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" value="<?= htmlspecialchars($usuario->getTelefone() ?? '') ?>">
            </div>
            
            <button type="submit">Atualizar Perfil</button>
        </form>
    </main>

    <?php
    if (isset($_GET['logout'])) {
        $authController->logout();
    }
    ?>
</body>
</html>