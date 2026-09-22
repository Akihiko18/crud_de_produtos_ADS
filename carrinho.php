<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Produto.php';
require_once __DIR__ . '/classes/Cesta.php';

$pdo = conectar();
$usuarioId = $_SESSION['usuario']['id'];

$stmt = $pdo->prepare("SELECT id FROM cestas WHERE usuario_id = ? LIMIT 1");
$stmt->execute([$usuarioId]);
$cestaRow = $stmt->fetch(PDO::FETCH_ASSOC);

$cesta = new Cesta($cestaRow['id'] ?? null, $usuarioId);

if ($cestaRow) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.nome, p.preco, p.estoque FROM cesta_itens ci
        JOIN produtos p ON p.id = ci.produto_id
        WHERE ci.cesta_id = ?
    ");
    $stmt->execute([$cestaRow['id']]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
        $cesta->adicionarProduto(new Produto($linha['id'], $linha['nome'], $linha['preco'], $linha['estoque']));
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Minha Cesta</title>
</head>
<body class="bg-light">
    <?php include 'includes/nav.php'; ?>

    <div class="container" style="max-width:500px;">
        <h1 class="text-primary mb-4">Minha Cesta</h1>

        <ul class="list-group mb-3">
            <?php if (empty($cesta->itens)): ?>
                <li class="list-group-item">Sua cesta está vazia.</li>
            <?php else: ?>
                <?php foreach ($cesta->itens as $produto): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= htmlspecialchars($produto->nome) ?></span>
                        <span>R$ <?= number_format($produto->preco, 2, ',', '.') ?></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <p class="fs-5">
            Itens na cesta: <strong><?= $cesta->contarItens() ?></strong><br>
            Valor total: <strong>R$ <?= number_format($cesta->calcularTotal(), 2, ',', '.') ?></strong>
        </p>
    </div>
</body>
</html>
