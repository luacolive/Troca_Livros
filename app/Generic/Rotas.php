<?php
namespace Generic;

class Rotas {
    private $rotas = [];

    public function adicionar($metodo, $caminho, $handler) {
        $this->rotas[] = [
            'metodo' => $metodo, 
            'caminho' => $caminho, 
            'handler' => $handler
        ];
    }

    public function executar() {
    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // ⭐⭐ REMOVER o base path da URL ⭐⭐
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
        $uri = substr($uri, strlen($scriptDir));
    }
    
    // Garantir que a URI não esteja vazia
    if (empty($uri) || $uri === '/') {
        $uri = '/';
    }
    
    echo "🔧 DEBUG: URI processada: $uri\n";
    
    foreach ($this->rotas as $rota) {
        if ($rota['metodo'] === $metodo && $this->corresponde($rota['caminho'], $uri)) {
            list($controller, $acao) = explode('@', $rota['handler']);
            $controller = "Controllers\\{$controller}";
            
            if (class_exists($controller)) {
                (new $controller)->$acao($this->extrairParametros($rota['caminho'], $uri));
                return;
            } else {
                http_response_code(500);
                echo json_encode(["erro" => "Controller não encontrado: $controller"]);
                return;
            }
        }
    }

    http_response_code(404);
    echo json_encode(["erro" => "Rota não encontrada: $metodo $uri"]);
}

    private function corresponde($padrao, $uri) {
        $padraoRegex = str_replace('/', '\/', $padrao);
        $padraoRegex = preg_replace('/\{[^}]+\}/', '([^\/]+)', $padraoRegex);
        return preg_match("/^{$padraoRegex}$/", $uri);
    }

    private function extrairParametros($padrao, $uri) {
        $parametros = [];
        
        $partesPadrao = explode('/', $padrao);
        $partesUri = explode('/', $uri);
        
        foreach ($partesPadrao as $index => $parte) {
            if (preg_match('/^{([^}]+)}$/', $parte, $matches)) {
                $parametros[$matches[1]] = $partesUri[$index] ?? null;
            }
        }
        
        return $parametros;
    }
}
?>