<?php

class Fornecedor {
    public $id;
    public $nome;
    public $cnpj;
    public $telefone;

    public function __construct($id, $nome, $cnpj, $telefone) {
        $this->id = $id;
        $this->nome = $nome;
        $this->cnpj = $cnpj;
        $this->telefone = $telefone;
    }
}
