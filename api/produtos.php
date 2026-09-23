<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

$pdo = conectar();
$metodo = $_SERVER['REQUEST_METHOD'];
$usuarioId = $_SESSION['usuario']['id'];

if ($metodo === 'GET') {
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

if ($metodo === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, estoque, fornecedor_id, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$dados['nome'], $dados['preco'], $dados['estoque'] ?? 0, $dados['fornecedor_id'], $usuarioId]);
    echo json_encode(['sucesso' => true]);
    exit;
}

if ($metodo === 'PUT') {
    $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE nome = ?");
    $stmt->execute([$dados['novoNome'], $dados['novoPreco'], $dados['nomeAtual']]);
    echo json_encode(['sucesso' => $stmt->rowCount() > 0]);
    exit;
}

echo json_encode(['erro' => 'Método não suportado.']);
