<?php
namespace App\Controllers;

use App\DAO\UsuarioDAO;
use App\Models\Usuario;

class UsuarioController
{
    private $usuarioDAO;

    public function __construct($pdo)
    {
        $this->usuarioDAO = new UsuarioDAO($pdo);
    }

    public function atualizarPerfil($id, $nome, $email, $endereco, $telefone)
    {
        $usuario = $this->usuarioDAO->buscarPorId($id);
        
        if (!$usuario) {
            return false;
        }

        $usuario->setNome($nome);
        $usuario->setEmail($email);
        $usuario->setEndereco($endereco);
        $usuario->setTelefone($telefone);

        return $this->usuarioDAO->atualizar($usuario);
    }

    public function buscarUsuario($id)
    {
        return $this->usuarioDAO->buscarPorId($id);
    }

    public function listarUsuarios()
    {
        return $this->usuarioDAO->listarTodos();
    }
}