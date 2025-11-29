<?php
namespace Generic;

class Acao {
    protected $retorno;

    public function __construct() {
        $this->retorno = new Retorno();
    }
}
?>