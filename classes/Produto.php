<?php

class Produto {
    public $id;
    public $nome;
    public $preco;
    public $estoque;
    public $fornecedor; // relação: Produto conhece seu Fornecedor

    public function __construct($id, $nome, $preco, $estoque, $fornecedor = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->preco = $preco;
        $this->estoque = $estoque;
        $this->fornecedor = $fornecedor;
    }
}
