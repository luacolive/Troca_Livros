<?php
namespace Generic;

class Endpoint {
    protected $metodo;
    protected $caminho;
    protected $handler;

    public function __construct($metodo, $caminho, $handler) {
        $this->metodo = $metodo;
        $this->caminho = $caminho;
        $this->handler = $handler;
    }

    public function getMetodo() { return $this->metodo; }
    public function getCaminho() { return $this->caminho; }
    public function getHandler() { return $this->handler; }
}
?>