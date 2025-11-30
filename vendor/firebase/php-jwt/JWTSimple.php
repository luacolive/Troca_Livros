<?php
namespace Firebase\JWT;

class JWT
{
    public static function encode($payload, $key, $alg = 'HS256')
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $alg
        ];

        // Codificar header e payload
        $segments = [];
        $segments[] = self::base64UrlEncode(json_encode($header));
        $segments[] = self::base64UrlEncode(json_encode($payload));
        
        // Criar assinatura
        $signing_input = implode('.', $segments);
        $signature = self::sign($signing_input, $key, $alg);
        $segments[] = self::base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public static function decode($jwt, $key, $algs = ['HS256'])
    {
        // Dividir o token
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new \Exception('Token inválido: número de segmentos incorreto');
        }

        list($headb64, $bodyb64, $cryptob64) = $tks;
        
        // Decodificar header
        $header = json_decode(self::base64UrlDecode($headb64));
        if ($header === null) {
            throw new \Exception('Token inválido: header corrompido');
        }
        
        // Decodificar payload
        $payload = json_decode(self::base64UrlDecode($bodyb64));
        if ($payload === null) {
            throw new \Exception('Token inválido: payload corrompido');
        }
        
        // Decodificar assinatura
        $sig = self::base64UrlDecode($cryptob64);
        if ($sig === false) {
            throw new \Exception('Token inválido: assinatura corrompida');
        }

        // Verificar algoritmo
        if (empty($header->alg)) {
            throw new \Exception('Token inválido: algoritmo não especificado');
        }
        if (!in_array($header->alg, $algs)) {
            throw new \Exception('Token inválido: algoritmo não permitido');
        }

        // Verificar assinatura
        $signing_input = $headb64 . '.' . $bodyb64;
        if (!self::verify($signing_input, $sig, $key, $header->alg)) {
            throw new \Exception('Token inválido: assinatura incorreta');
        }

        // Verificar expiração
        if (isset($payload->exp) && time() >= $payload->exp) {
            throw new \Exception('Token expirado');
        }

        return $payload;
    }

    private static function sign($msg, $key, $alg)
    {
        switch ($alg) {
            case 'HS256':
                return hash_hmac('SHA256', $msg, $key, true);
            case 'HS384':
                return hash_hmac('SHA384', $msg, $key, true);
            case 'HS512':
                return hash_hmac('SHA512', $msg, $key, true);
            default:
                throw new \Exception('Algoritmo não suportado: ' . $alg);
        }
    }

    private static function verify($msg, $signature, $key, $alg)
    {
        $hash = self::sign($msg, $key, $alg);
        return hash_equals($signature, $hash);
    }

    private static function base64UrlDecode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    private static function base64UrlEncode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}
?>