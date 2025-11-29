<?php
namespace Controllers;

use Generic\Acao;
use App\DAO\LivroDAO;
use App\Models\Livro;
use Generic\MysqlSingleton;

class ApiLivroController extends Acao {
    private $livroDAO;
    private $pdo;

    public function __construct() {
        parent::__construct();
        
        // ✅ USANDO SINGLETON CORRETAMENTE
        $this->pdo = MysqlSingleton::getInstance();
        $this->livroDAO = new LivroDAO($this->pdo);
    }

    public function listar() {
        try {
            $livros = $this->livroDAO->listarTodos();
            
            // Converter objetos Livro para array
            $livrosArray = [];
            foreach ($livros as $livro) {
                $livrosArray[] = [
                    'id' => $livro->getId(),
                    'titulo' => $livro->getTitulo(),
                    'autor' => $livro->getAutor(),
                    'genero' => $livro->getGenero(),
                    'sinopse' => $livro->getSinopse(),
                    'condicao' => $livro->getCondicao(),
                    'disponivel' => $livro->isDisponivel(),
                    'usuario_id' => $livro->getUsuarioId(),
                    'criado_em' => $livro->getCriadoEm()
                ];
            }
            
            $this->retorno->sucesso($livrosArray);
        } catch (\Exception $e) {
            $this->retorno->erro($e->getMessage());
        }
    }

    public function buscar($params) {
        try {
            $livro = $this->livroDAO->buscarPorId($params['id']);
            if ($livro) {
                $livroArray = [
                    'id' => $livro->getId(),
                    'titulo' => $livro->getTitulo(),
                    'autor' => $livro->getAutor(),
                    'genero' => $livro->getGenero(),
                    'sinopse' => $livro->getSinopse(),
                    'condicao' => $livro->getCondicao(),
                    'disponivel' => $livro->isDisponivel(),
                    'usuario_id' => $livro->getUsuarioId(),
                    'criado_em' => $livro->getCriadoEm()
                ];
                $this->retorno->sucesso($livroArray);
            } else {
                $this->retorno->erro("Livro não encontrado", 404);
            }
        } catch (\Exception $e) {
            $this->retorno->erro($e->getMessage());
        }
    }

    public function criar() {
        try {
            $dados = json_decode(file_get_contents("php://input"), true);
            
            if (empty($dados['titulo']) || empty($dados['autor'])) {
                $this->retorno->erro("Título e autor são obrigatórios", 400);
                return;
            }

            $livro = new Livro(
                $dados['titulo'],
                $dados['autor'],
                $dados['genero'] ?? '',
                $dados['sinopse'] ?? '',
                $dados['condicao'] ?? 'Bom',
                true,
                $dados['usuario_id'] ?? 1,
                null,
                null
            );

            $resultado = $this->livroDAO->inserir($livro);
            if ($resultado) {
                $lastId = $this->pdo->lastInsertId();
                $this->retorno->sucesso([
                    'mensagem' => 'Livro criado com sucesso!',
                    'id' => $lastId
                ], 201);
            } else {
                $this->retorno->erro("Erro ao criar livro no banco de dados", 500);
            }

        } catch (\Exception $e) {
            $this->retorno->erro($e->getMessage(), 400);
        }
    }

    public function atualizar($params) {
        try {
            $dados = json_decode(file_get_contents("php://input"), true);
            
            $livroExistente = $this->livroDAO->buscarPorId($params['id']);
            if (!$livroExistente) {
                $this->retorno->erro("Livro não encontrado", 404);
                return;
            }

            $livro = new Livro(
                $dados['titulo'] ?? $livroExistente->getTitulo(),
                $dados['autor'] ?? $livroExistente->getAutor(),
                $dados['genero'] ?? $livroExistente->getGenero(),
                $dados['sinopse'] ?? $livroExistente->getSinopse(),
                $dados['condicao'] ?? $livroExistente->getCondicao(),
                $dados['disponivel'] ?? $livroExistente->isDisponivel(),
                $dados['usuario_id'] ?? $livroExistente->getUsuarioId(),
                $params['id'],
                $livroExistente->getCriadoEm()
            );

            $resultado = $this->livroDAO->atualizar($livro);
            if ($resultado) {
                $this->retorno->sucesso(['mensagem' => 'Livro atualizado com sucesso']);
            } else {
                $this->retorno->erro("Erro ao atualizar livro", 500);
            }
        } catch (\Exception $e) {
            $this->retorno->erro($e->getMessage(), 400);
        }
    }

    public function excluir($params) {
        try {
            // Verificar se o livro existe antes de excluir
            $livro = $this->livroDAO->buscarPorId($params['id']);
            if (!$livro) {
                $this->retorno->erro("Livro não encontrado", 404);
                return;
            }

            $resultado = $this->livroDAO->excluir($params['id']);
            if ($resultado) {
                $this->retorno->sucesso(['mensagem' => 'Livro excluído com sucesso']);
            } else {
                $this->retorno->erro("Erro ao excluir livro", 500);
            }
        } catch (\Exception $e) {
            $this->retorno->erro($e->getMessage(), 400);
        }
    }
}
?>