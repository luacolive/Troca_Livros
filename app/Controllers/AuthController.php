<?php
namespace App\Controllers;

use App\DAO\UsuarioDAO;
use App\Models\Usuario;

class AuthController
{
    private $usuarioDAO;

    public function __construct($pdo)
    {
        $this->usuarioDAO = new UsuarioDAO($pdo);
    }

    public function login($email, $senha)
    {
        $usuario = $this->usuarioDAO->buscarPorEmail($email);
        
        if ($usuario && password_verify($senha, $usuario->getSenhaHash())) {
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['usuario_nome'] = $usuario->getNome();
            return true;
        }
        
        return false;
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function registrar($nome, $email, $senha, $endereco = null, $telefone = null)
    {
        // Verificar se o email já existe
        if ($this->usuarioDAO->buscarPorEmail($email)) {
            return false;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $usuario = new Usuario($nome, $email, $senhaHash, $endereco, $telefone);
        
        if ($this->usuarioDAO->inserir($usuario)) {
            return $this->login($email, $senha);
        }
        
        return false;
    }

    public function estaLogado()
    {
        return isset($_SESSION['usuario_id']);
    }

    public function getUsuarioLogado()
    {
        if ($this->estaLogado()) {
            return $this->usuarioDAO->buscarPorId($_SESSION['usuario_id']);
        }
        return null;
    }
}