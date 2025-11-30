<?php
namespace Generic;

use config\JwtConfig;

class Acao {
    protected $retorno;

    public function __construct() {
        $this->retorno = new Retorno();
    }

    protected function gerarToken($dadosUsuario) {
        try {
            $payload = [
                'iss' => 'localhost',
                'iat' => time(),
                'exp' => time() + JwtConfig::TOKEN_EXPIRE,
                'data' => $dadosUsuario
            ];

            return \Firebase\JWT\JWT::encode($payload, JwtConfig::getSecret(), JwtConfig::ALGORITHM);
            
        } catch (\Exception $e) {
            throw new \Exception("Erro ao gerar token");
        }
    }

    protected function verificarToken() {
        try {
            $headers = apache_request_headers();
            
            if (!isset($headers['Authorization'])) {
                return null;
            }

            $token = str_replace('Bearer ', '', $headers['Authorization']);

            $decoded = \Firebase\JWT\JWT::decode($token, JwtConfig::getSecret(), [JwtConfig::ALGORITHM]);
            
            return $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function requerirAutenticacao() {
        $usuario = $this->verificarToken();
        if (!$usuario) {
            $this->retorno->erro("Acesso não autorizado", 401);
            exit;
        }
        return $usuario;
    }
}
?>