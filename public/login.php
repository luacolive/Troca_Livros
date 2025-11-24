<?php
require_once __DIR__ . '/../config/bootstrap.php';

use App\Controllers\AuthController;
use App\Utils\Auth;

Auth::redirecionarSeAutenticado();

$pdo = getPDOConnection();
$authController = new AuthController($pdo);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if ($authController->login($email, $senha)) {
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Email ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Troca de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Troca de Livros</h1>
        <nav>
            <a href="index.php">Início</a>
            <a href="cadastro.php">Cadastrar</a>
        </nav>
    </header>

    <main>
        <h2>Login</h2>
        
        <?php if ($erro): ?>
            <div class="erro"><?= $erro ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit">Entrar</button>
        </form>
        
        <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
    </main>
</body>
</html>