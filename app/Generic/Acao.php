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

            echo "🔧 DEBUG: Gerando token para usuário ID: " . $dadosUsuario['usuario_id'] . "\n";
            
            // ✅ USANDO JWT SIMPLES
            $token = \Firebase\JWT\JWT::encode($payload, JwtConfig::getSecret(), JwtConfig::ALGORITHM);
            
            echo "🔧 DEBUG: Token gerado com sucesso\n";
            return $token;
        } catch (\Exception $e) {
            echo "🔧 DEBUG ERRO: Falha ao gerar token: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    protected function verificarToken() {
        try {
            $headers = apache_request_headers();
            
            if (!isset($headers['Authorization'])) {
                echo "🔧 DEBUG: Header Authorization não encontrado\n";
                return null;
            }

            $token = str_replace('Bearer ', '', $headers['Authorization']);
            echo "🔧 DEBUG: Token recebido\n";

            // jwt simples
            $decoded = \Firebase\JWT\JWT::decode($token, JwtConfig::getSecret(), [JwtConfig::ALGORITHM]);
            
            echo "🔧 DEBUG: Token válido para usuário ID: " . $decoded->data->usuario_id . "\n";
            return $decoded->data;
        } catch (\Exception $e) {
            echo "🔧 DEBUG: Token inválido: " . $e->getMessage() . "\n";
            return null;
        }
    }

    protected function requerirAutenticacao() {
        echo "🔧 DEBUG: Verificando autenticação...\n";
        $usuario = $this->verificarToken();
        if (!$usuario) {
            echo "🔧 DEBUG: Autenticação FALHOU\n";
            $this->retorno->erro("Acesso não autorizado", 401);
            exit;
        }
        echo "🔧 DEBUG: Autenticação BEM-SUCEDIDA\n";
        return $usuario;
    }
}
?>