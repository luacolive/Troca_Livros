<?php
namespace App\DAO;

use App\Models\Usuario;
use PDO;

class UsuarioDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function inserir(Usuario $usuario): bool
    {
        $sql = "INSERT INTO usuarios (nome, email, senha_hash, endereco, telefone)
                VALUES (:nome, :email, :senha_hash, :endereco, :telefone)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha_hash' => $usuario->getSenhaHash(),
            ':endereco' => $usuario->getEndereco(),
            ':telefone' => $usuario->getTelefone()
        ]);
    }

    public function buscarPorEmail(string $email): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        
        if (!$row) return null;

        return new Usuario(
            $row['nome'],
            $row['email'],
            $row['senha_hash'],
            $row['endereco'],
            $row['telefone'],
            (int)$row['id'],
            $row['criado_em']
        );
    }

    public function buscarPorId(int $id): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        
        if (!$row) return null;

        return new Usuario(
            $row['nome'],
            $row['email'],
            $row['senha_hash'],
            $row['endereco'],
            $row['telefone'],
            (int)$row['id'],
            $row['criado_em']
        );
    }

    public function atualizar(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, endereco = :endereco, 
                telefone = :telefone WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':endereco' => $usuario->getEndereco(),
            ':telefone' => $usuario->getTelefone(),
            ':id' => $usuario->getId()
        ]);
    }

    public function listarTodos(): array
    {
        $sql = "SELECT * FROM usuarios ORDER BY criado_em DESC";
        $stmt = $this->pdo->query($sql);
        $usuarios = [];
        
        while ($row = $stmt->fetch()) {
            $usuarios[] = new Usuario(
                $row['nome'],
                $row['email'],
                $row['senha_hash'],
                $row['endereco'],
                $row['telefone'],
                (int)$row['id'],
                $row['criado_em']
            );
        }
        
        return $usuarios;
    }
}