<?php
require_once __DIR__ . '/../config/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\TrocaController;

$pdo = getPDOConnection();
$authController = new AuthController($pdo);
$trocaController = new TrocaController($pdo);

// Verificar se usuário está logado
if (!$authController->estaLogado()) {
    header('Location: login.php');
    exit;
}

$usuario = $authController->getUsuarioLogado();
$mensagem = '';
$sucesso = false;

// Processar ações (aceitar, rejeitar, cancelar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solicitacaoId = $_POST['solicitacao_id'] ?? null;
    $acao = $_POST['acao'] ?? '';
    
    if ($solicitacaoId) {
        switch ($acao) {
            case 'aceitar':
                $resultado = $trocaController->responderSolicitacao($solicitacaoId, 'aceita', $usuario->getId());
                break;
            case 'rejeitar':
                $resultado = $trocaController->responderSolicitacao($solicitacaoId, 'rejeitada', $usuario->getId());
                break;
            case 'cancelar':
                $resultado = $trocaController->cancelarSolicitacao($solicitacaoId, $usuario->getId());
                break;
            default:
                $resultado = ['sucesso' => false, 'mensagem' => 'Ação inválida.'];
        }
        
        $sucesso = $resultado['sucesso'];
        $mensagem = $resultado['mensagem'];
    }
}

// Buscar solicitações
$solicitacoesRecebidas = $trocaController->listarSolicitacoesRecebidas($usuario->getId());
$solicitacoesEnviadas = $trocaController->listarSolicitacoesEnviadas($usuario->getId());
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Trocas - Troca de Livros</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        nav {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        nav a:hover {
            background-color: #34495e;
        }
        
        .mensagem {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        
        .sessao-trocas {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .sessao-titulo {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .solicitacao-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: #fafafa;
        }
        
        .solicitacao-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .solicitacao-id {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-aceita {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejeitada {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-cancelada {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .livro-info {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        
        .livro-titulo {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .acoes {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-aceitar {
            background-color: #28a745;
            color: white;
        }
        
        .btn-aceitar:hover {
            background-color: #218838;
        }
        
        .btn-rejeitar {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-rejeitar:hover {
            background-color: #c82333;
        }
        
        .btn-cancelar {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-cancelar:hover {
            background-color: #5a6268;
        }
        
        .vazio {
            text-align: center;
            color: #7f8c8d;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }
        
        .data {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.php">Início</a>
                <a href="perfil.php">Meu Perfil</a>
                <a href="livros.php">Meus Livros</a>
                <a href="minhas_trocas.php">Minhas Trocas</a>
                <a href="?logout=true">Sair</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Minhas Trocas</h1>
        
        <?php if ($mensagem): ?>
            <div class="<?= $sucesso ? 'mensagem' : 'erro' ?>"><?= $mensagem ?></div>
        <?php endif; ?>

        <!-- Solicitações Recebidas -->
        <div class="sessao-trocas">
            <h2 class="sessao-titulo">📥 Solicitações Recebidas</h2>
            
            <?php if (empty($solicitacoesRecebidas)): ?>
                <div class="vazio">
                    <p>Nenhuma solicitação de troca recebida.</p>
                </div>
            <?php else: ?>
                <?php foreach ($solicitacoesRecebidas as $solicitacao): ?>
                    <div class="solicitacao-card">
                        <div class="solicitacao-header">
                            <span class="solicitacao-id">Solicitação #<?= $solicitacao->getId() ?></span>
                            <span class="status status-<?= $solicitacao->getStatus() ?>">
                                <?= ucfirst($solicitacao->getStatus()) ?>
                            </span>
                        </div>
                        
                        <div class="livro-info">
                            <span class="livro-titulo">Livro que você recebe:</span>
                            <p><?= $solicitacao->getLivroOfertado()->getTitulo() ?> 
                               por <?= $solicitacao->getUsuarioSolicitante()->getNome() ?></p>
                        </div>
                        
                        <div class="livro-info">
                            <span class="livro-titulo">Livro que você oferece:</span>
                            <p><?= $solicitacao->getLivroSolicitado()->getTitulo() ?></p>
                        </div>
                        
                        <div class="data">
                            Solicitado em: <?= date('d/m/Y H:i', strtotime($solicitacao->getDataSolicitacao())) ?>
                        </div>
                        
                        <?php if ($solicitacao->getStatus() === 'pendente'): ?>
                            <form method="POST" class="acoes">
                                <input type="hidden" name="solicitacao_id" value="<?= $solicitacao->getId() ?>">
                                <button type="submit" name="acao" value="aceitar" class="btn btn-aceitar">
                                    ✅ Aceitar Troca
                                </button>
                                <button type="submit" name="acao" value="rejeitar" class="btn btn-rejeitar">
                                    ❌ Rejeitar Troca
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Solicitações Enviadas -->
        <div class="sessao-trocas">
            <h2 class="sessao-titulo">📤 Solicitações Enviadas</h2>
            
            <?php if (empty($solicitacoesEnviadas)): ?>
                <div class="vazio">
                    <p>Nenhuma solicitação de troca enviada.</p>
                </div>
            <?php else: ?>
                <?php foreach ($solicitacoesEnviadas as $solicitacao): ?>
                    <div class="solicitacao-card">
                        <div class="solicitacao-header">
                            <span class="solicitacao-id">Solicitação #<?= $solicitacao->getId() ?></span>
                            <span class="status status-<?= $solicitacao->getStatus() ?>">
                                <?= ucfirst($solicitacao->getStatus()) ?>
                            </span>
                        </div>
                        
                        <div class="livro-info">
                            <span class="livro-titulo">Você oferece:</span>
                            <p><?= $solicitacao->getLivroOfertado()->getTitulo() ?></p>
                        </div>
                        
                        <div class="livro-info">
                            <span class="livro-titulo">Você solicita:</span>
                            <p><?= $solicitacao->getLivroSolicitado()->getTitulo() ?> 
                               de <?= $solicitacao->getUsuarioProprietario()->getNome() ?></p>
                        </div>
                        
                        <div class="data">
                            Solicitado em: <?= date('d/m/Y H:i', strtotime($solicitacao->getDataSolicitacao())) ?>
                        </div>
                        
                        <?php if ($solicitacao->getStatus() === 'pendente'): ?>
                            <form method="POST" class="acoes">
                                <input type="hidden" name="solicitacao_id" value="<?= $solicitacao->getId() ?>">
                                <button type="submit" name="acao" value="cancelar" class="btn btn-cancelar">
                                    ✖️ Cancelar Solicitação
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php
    if (isset($_GET['logout'])) {
        $authController->logout();
    }
    ?>
</body>
</html>