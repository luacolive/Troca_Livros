<?php
namespace Controllers;

use Generic\Acao;
use App\DAO\UsuarioDAO;
use App\Models\Usuario;
use Generic\MysqlSingleton;

class AuthController extends Acao {
    private $usuarioDAO;
    private $pdo;

    public function __construct() {
        parent::__construct();
        $this->pdo = MysqlSingleton::getInstance();
        $this->usuarioDAO = new UsuarioDAO($this->pdo);
    }

    public function login() {
        try {
            $dados = json_decode(file_get_contents("php://input"), true);

            if (empty($dados['email']) || empty($dados['senha'])) {
                $this->retorno->erro("Email e senha são obrigatórios", 400);
                return;
            }

            $usuario = $this->usuarioDAO->buscarPorEmail($dados['email']);
            
            if (!$usuario || !password_verify($dados['senha'], $usuario->getSenhaHash())) {
                $this->retorno->erro("Credenciais inválidas", 401);
                return;
            }

            $token = $this->gerarToken([
                'usuario_id' => $usuario->getId(),
                'nome' => $usuario->getNome(),
                'email' => $usuario->getEmail()
            ]);

            $this->retorno->sucesso([
                'mensagem' => 'Login realizado com sucesso',
                'token' => $token,
                'usuario' => [
                    'id' => $usuario->getId(),
                    'nome' => $usuario->getNome(),
                    'email' => $usuario->getEmail()
                ]
            ]);

        } catch (\Exception $e) {
            $this->retorno->erro("Erro interno no servidor", 500);
        }
    }

    public function registrar() {
        try {
            $dados = json_decode(file_get_contents("php://input"), true);

            if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
                $this->retorno->erro("Nome, email e senha são obrigatórios", 400);
                return;
            }

            if ($this->usuarioDAO->buscarPorEmail($dados['email'])) {
                $this->retorno->erro("Email já cadastrado", 400);
                return;
            }

            $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

            $usuario = new Usuario(
                $dados['nome'],
                $dados['email'],
                $senhaHash,
                $dados['endereco'] ?? null,
                $dados['telefone'] ?? null
            );

            $resultado = $this->usuarioDAO->inserir($usuario);
            
            if ($resultado) {
                $this->retorno->sucesso(['mensagem' => 'Usuário criado com sucesso'], 201);
            } else {
                $this->retorno->erro("Erro ao criar usuário", 500);
            }

        } catch (\Exception $e) {
            $this->retorno->erro("Erro interno no servidor", 500);
        }
    }

    public function perfil() {
        try {
            $usuario = $this->requerirAutenticacao();
            
            $usuarioCompleto = $this->usuarioDAO->buscarPorId($usuario->usuario_id);
            
            if ($usuarioCompleto) {
                $this->retorno->sucesso([
                    'usuario' => [
                        'id' => $usuarioCompleto->getId(),
                        'nome' => $usuarioCompleto->getNome(),
                        'email' => $usuarioCompleto->getEmail(),
                        'endereco' => $usuarioCompleto->getEndereco(),
                        'telefone' => $usuarioCompleto->getTelefone(),
                        'criado_em' => $usuarioCompleto->getCriadoEm()
                    ]
                ]);
            } else {
                $this->retorno->erro("Usuário não encontrado", 404);
            }

        } catch (\Exception $e) {
            $this->retorno->erro("Erro interno no servidor", 500);
        }
    }
}
?>