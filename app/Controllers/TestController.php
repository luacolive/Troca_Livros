<?php
namespace Controllers;

use Generic\Controller;

class TestController extends Controller {
    public function hello() {
        echo "🔧 DEBUG: TestController funcionando!\n";
        $this->retorno->sucesso(['message' => 'Hello API!']);
    }
}
?>