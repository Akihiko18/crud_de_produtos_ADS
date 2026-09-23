<?php

class Usuario {
    public $id;
    public $nome;
    public $email;

    public function __construct($id, $nome, $email) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
    }

    public static function hashSenha($senhaPlana) {
        return hash('sha256', $senhaPlana);
    }
}
