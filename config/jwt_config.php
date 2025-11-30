<?php
namespace config;  // ✅ minúsculo

class JwtConfig {  // ✅ classe pode ser maiúscula (convenção)
    const SECRET_KEY = 'sua_chave_secreta_muito_longa_e_segura_para_troca_de_livros_2024';
    const ALGORITHM = 'HS256';
    const TOKEN_EXPIRE = 86400;
    
    public static function getSecret() {
        return self::SECRET_KEY;
    }
}
?>