<?php

class Cesta {
    public $id;
    public $usuarioId;
    public $itens = []; // array de Produto

    public function __construct($id, $usuarioId) {
        $this->id = $id;
        $this->usuarioId = $usuarioId;
    }

    public function adicionarProduto(Produto $produto) {
        $this->itens[] = $produto;
    }

    public function calcularTotal() {
        return array_reduce($this->itens, fn($soma, $p) => $soma + $p->preco, 0);
    }

    public function contarItens() {
        return count($this->itens);
    }
}
