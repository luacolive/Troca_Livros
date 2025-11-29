<?php
namespace Generic;

class Retorno {
    public function sucesso($dados = null, $codigo = 200) {
        http_response_code($codigo);
        echo json_encode([
            'sucesso' => true,
            'dados' => $dados
        ], JSON_UNESCAPED_UNICODE);
    }

    public function erro($mensagem, $codigo = 400) {
        http_response_code($codigo);
        echo json_encode([
            'sucesso' => false,
            'erro' => $mensagem
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>