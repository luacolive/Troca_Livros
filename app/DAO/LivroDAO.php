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
        $sql = "INSERT INTO livros (titulo, autor, genero, sinopse, condicao, disponivel, usuario_id)
                VALUES (:titulo, :autor, :genero, :sinopse, :condicao, :disponivel, :usuario_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titulo' => $livro->getTitulo(),
            ':autor' => $livro->getAutor(),
            ':genero' => $livro->getGenero(),
            ':sinopse' => $livro->getSinopse(),
            ':condicao' => $livro->getCondicao(),
            ':disponivel' => $livro->isDisponivel(),
            ':usuario_id' => $livro->getUsuarioId()
        ]);
    }

    public function buscarPorId(int $id): ?Livro
    {
        $sql = "SELECT * FROM livros WHERE id = :id LIMIT 1";
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
    }

    public function buscarPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT * FROM livros WHERE usuario_id = :usuario_id ORDER BY criado_em DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        
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
    }

    public function listarTodos(): array
    {
        $sql = "SELECT * FROM livros WHERE disponivel = 1 ORDER BY criado_em DESC";
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
    }

    public function atualizar(Livro $livro): bool
    {
        $sql = "UPDATE livros SET titulo = :titulo, autor = :autor, genero = :genero, 
                sinopse = :sinopse, condicao = :condicao, disponivel = :disponivel 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titulo' => $livro->getTitulo(),
            ':autor' => $livro->getAutor(),
            ':genero' => $livro->getGenero(),
            ':sinopse' => $livro->getSinopse(),
            ':condicao' => $livro->getCondicao(),
            ':disponivel' => $livro->isDisponivel(),
            ':id' => $livro->getId()
        ]);
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM livros WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}