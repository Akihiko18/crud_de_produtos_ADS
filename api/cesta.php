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

function obterOuCriarCesta($pdo, $usuarioId) {
    $stmt = $pdo->prepare("SELECT id FROM cestas WHERE usuario_id = ? LIMIT 1");
    $stmt->execute([$usuarioId]);
    $cesta = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cesta) return $cesta['id'];

    $stmt = $pdo->prepare("INSERT INTO cestas (usuario_id) VALUES (?) RETURNING id");
    $stmt->execute([$usuarioId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
}

if ($metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT id FROM cestas WHERE usuario_id = ? LIMIT 1");
    $stmt->execute([$usuarioId]);
    $cesta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cesta) {
        echo json_encode(['itens' => [], 'total' => 0, 'quantidade' => 0]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT p.nome, p.preco FROM cesta_itens ci
        JOIN produtos p ON p.id = ci.produto_id
        WHERE ci.cesta_id = ?
    ");
    $stmt->execute([$cesta['id']]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = array_reduce($itens, fn($soma, $i) => $soma + $i['preco'], 0);

    echo json_encode(['itens' => $itens, 'total' => $total, 'quantidade' => count($itens)]);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

if ($metodo === 'POST') {
    $cestaId = obterOuCriarCesta($pdo, $usuarioId);
    $stmt = $pdo->prepare("INSERT INTO cesta_itens (cesta_id, produto_id) VALUES (?, ?)");
    foreach ($dados['produtoIds'] as $produtoId) {
        $stmt->execute([$cestaId, $produtoId]);
    }
    echo json_encode(['sucesso' => true]);
    exit;
}

if ($metodo === 'DELETE') {
    $stmt = $pdo->prepare("SELECT id FROM cestas WHERE usuario_id = ? LIMIT 1");
    $stmt->execute([$usuarioId]);
    $cesta = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtProduto = $pdo->prepare("SELECT id FROM produtos WHERE nome = ? LIMIT 1");
    $stmtProduto->execute([$dados['nomeProduto']]);
    $produto = $stmtProduto->fetch(PDO::FETCH_ASSOC);

    if (!$cesta || !$produto) {
        echo json_encode(['sucesso' => false]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM cesta_itens WHERE cesta_id = ? AND produto_id = ?");
    $stmt->execute([$cesta['id'], $produto['id']]);
    echo json_encode(['sucesso' => $stmt->rowCount() > 0]);
    exit;
}

echo json_encode(['erro' => 'Método não suportado.']);
