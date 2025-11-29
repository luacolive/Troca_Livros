<?php
namespace Controllers;

use Generic\Controller;

class ApiLivroController extends Controller {
    
    public function listar() {
        // DADOS DE TESTE - deve aparecer no Postman
        $livrosTeste = [
            [
                'id' => 1,
                'titulo' => 'Dom Casmurro - API Test',
                'autor' => 'Machado de Assis',
                'genero' => 'Romance'
            ],
            [
                'id' => 2,
                'titulo' => '1984 - API Test', 
                'autor' => 'George Orwell',
                'genero' => 'Ficção'
            ]
        ];
        
        $this->retorno->sucesso($livrosTeste);
    }

    public function buscar($params) {
        $this->retorno->sucesso([
            'id' => $params['id'],
            'titulo' => 'Livro específico - API Test',
            'mensagem' => 'Busca funcionando! ID: ' . $params['id']
        ]);
    }

    public function criar() {
        $dados = json_decode(file_get_contents("php://input"), true);
        $this->retorno->sucesso([
            'mensagem' => 'Livro criado com sucesso!',
            'dados_recebidos' => $dados
        ], 201);
    }

    public function atualizar($params) {
        $this->retorno->sucesso([
            'mensagem' => 'Livro atualizado!',
            'id' => $params['id']
        ]);
    }

    public function excluir($params) {
        $this->retorno->sucesso([
            'mensagem' => 'Livro excluído!',
            'id' => $params['id']
        ]);
    }
}
?>