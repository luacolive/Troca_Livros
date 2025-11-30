<?php
namespace App\DAO;

use App\Models\Livro;
use PDO;

class LivroDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function inserir(Livro $livro): bool
    {
        try {
            $sql = "INSERT INTO livros (titulo, autor, genero, sinopse, condicao, disponivel, usuario_id) 
                    VALUES (:titulo, :autor, :genero, :sinopse, :condicao, :disponivel, :usuario_id)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':titulo' => $livro->getTitulo(),
                ':autor' => $livro->getAutor(),
                ':genero' => $livro->getGenero(),
                ':sinopse' => $livro->getSinopse(),
                ':condicao' => $livro->getCondicao(),
                ':disponivel' => $livro->isDisponivel() ? 1 : 0,
                ':usuario_id' => $livro->getUsuarioId()
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao cadastrar livro");
        }
    }

    public function atualizar(Livro $livro): bool
    {
        try {
            $sql = "UPDATE livros SET titulo = :titulo, autor = :autor, genero = :genero, 
                    sinopse = :sinopse, condicao = :condicao, disponivel = :disponivel, 
                    usuario_id = :usuario_id WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':titulo' => $livro->getTitulo(),
                ':autor' => $livro->getAutor(),
                ':genero' => $livro->getGenero(),
                ':sinopse' => $livro->getSinopse(),
                ':condicao' => $livro->getCondicao(),
                ':disponivel' => $livro->isDisponivel() ? 1 : 0,
                ':usuario_id' => $livro->getUsuarioId(),
                ':id' => $livro->getId()
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao atualizar livro");
        }
    }

    public function excluir(int $id): bool
    {
        try {
            $sql = "DELETE FROM livros WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao excluir livro");
        }
    }

    public function listarTodos(): array
    {
        try {
            $sql = "SELECT * FROM livros ORDER BY criado_em DESC";
            $stmt = $this->pdo->query($sql);
            $livros = [];
            
            while ($row = $stmt->fetch()) {
                $livros[] = new Livro(
                    $row['titulo'],
                    $row['autor'],
                    $row['genero'],
                    $row['sinopse'],
                    $row['condicao'],
                    (bool)$row['disponivel'],
                    (int)$row['usuario_id'],
                    (int)$row['id'],
                    $row['criado_em']
                );
            }
            
            return $livros;
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao listar livros");
        }
    }

    public function buscarPorId(int $id): ?Livro
    {
        try {
            $sql = "SELECT * FROM livros WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            
            if (!$row) return null;

            return new Livro(
                $row['titulo'],
                $row['autor'],
                $row['genero'],
                $row['sinopse'],
                $row['condicao'],
                (bool)$row['disponivel'],
                (int)$row['usuario_id'],
                (int)$row['id'],
                $row['criado_em']
            );
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar livro");
        }
    }
}
?>