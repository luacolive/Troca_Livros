<?php
namespace App\Controllers;

use App\DAO\LivroDAO;
use App\Models\Livro;

class LivroController
{
    private $livroDAO;

    public function __construct($pdo)
    {
        $this->livroDAO = new LivroDAO($pdo);
    }

    public function cadastrar($titulo, $autor, $genero, $sinopse, $condicao, $usuarioId)
    {
        $livro = new Livro($titulo, $autor, $genero, $sinopse, $condicao, true, $usuarioId);
        return $this->livroDAO->inserir($livro);
    }

    public function listarLivros()
    {
        return $this->livroDAO->listarTodos();
    }

    public function listarLivrosUsuario($usuarioId)
    {
        return $this->livroDAO->buscarPorUsuario($usuarioId);
    }

    public function buscarLivro($id)
    {
        return $this->livroDAO->buscarPorId($id);
    }

    public function atualizarLivro($id, $titulo, $autor, $genero, $sinopse, $condicao, $disponivel)
    {
        $livro = $this->livroDAO->buscarPorId($id);
        
        if (!$livro) {
            return false;
        }

        $livro->setTitulo($titulo);
        $livro->setAutor($autor);
        $livro->setGenero($genero);
        $livro->setSinopse($sinopse);
        $livro->setCondicao($condicao);
        $livro->setDisponivel($disponivel);

        return $this->livroDAO->atualizar($livro);
    }

    public function excluirLivro($id)
    {
        return $this->livroDAO->excluir($id);
    }
}