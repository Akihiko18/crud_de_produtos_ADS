<?php

function conectar() {
    $host = 'aws-0-sa-east-1.pooler.supabase.com';
    $porta = '5432';
    $banco = 'postgres';
    $usuario = 'postgres.bdselqtawasfdtipsyqf';
    $senha = 'carlosearthur12345';

    $dsn = "pgsql:host=$host;port=$porta;dbname=$banco;sslmode=require";

    $pdo = new PDO($dsn, $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    criarTabelas($pdo);

    return $pdo;
}

function criarTabelas($pdo) {
    $pdo->exec("CREATE EXTENSION IF NOT EXISTS pgcrypto");

    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        nome text NOT NULL,
        email text UNIQUE NOT NULL,
        senha_hash text NOT NULL,
        criado_em timestamp DEFAULT now()
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS fornecedores (
        id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        nome text NOT NULL,
        cnpj text,
        telefone text,
        usuario_id uuid REFERENCES usuarios(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
        id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        nome text NOT NULL,
        preco numeric NOT NULL,
        estoque int DEFAULT 0,
        fornecedor_id uuid REFERENCES fornecedores(id),
        usuario_id uuid REFERENCES usuarios(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS cestas (
        id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        usuario_id uuid REFERENCES usuarios(id),
        criado_em timestamp DEFAULT now()
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS cesta_itens (
        id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        cesta_id uuid REFERENCES cestas(id),
        produto_id uuid REFERENCES produtos(id)
    )");
}
