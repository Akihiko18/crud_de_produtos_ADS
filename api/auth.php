<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Usuario.php';

$pdo = conectar();
$dados = json_decode(file_get_contents('php://input'), true);
$acao = $dados['acao'] ?? '';

if ($acao === 'cadastrar') {
    $hash = Usuario::hashSenha($dados['senha']);
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
        $stmt->execute([$dados['nome'], $dados['email'], $hash]);
        echo json_encode(['sucesso' => true]);
    } catch (PDOException $e) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email já cadastrado.']);
    }
    exit;
}

if ($acao === 'login') {
    $hash = Usuario::hashSenha($dados['senha']);
    $stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE email = ? AND senha_hash = ?");
    $stmt->execute([$dados['email'], $hash]);
    $linha = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($linha) {
        $usuario = new Usuario($linha['id'], $linha['nome'], $linha['email']);
        $_SESSION['usuario'] = ['id' => $usuario->id, 'nome' => $usuario->nome, 'email' => $usuario->email];
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Credenciais inválidas.']);
    }
    exit;
}

if ($acao === 'logout') {
    session_destroy();
    echo json_encode(['sucesso' => true]);
    exit;
}

echo json_encode(['sucesso' => false, 'erro' => 'Ação inválida.']);
