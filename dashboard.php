<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body class="bg-light">
    <?php include 'includes/nav.php'; ?>

    <div class="container">
        <h2 class="text-primary">Cadastro</h2>
        <div class="row g-3 mb-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cadastrar Fornecedor</h5>
                        <form id="formFornecedor">
                            <div class="mb-2"><label class="form-label">Nome</label><input type="text" class="form-control" id="fornecedorNome"></div>
                            <div class="mb-2"><label class="form-label">CNPJ</label><input type="text" class="form-control" id="fornecedorCnpj"></div>
                            <div class="mb-2"><label class="form-label">Telefone</label><input type="text" class="form-control" id="fornecedorTelefone"></div>
                            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cadastrar Produto</h5>
                        <form id="formProduto">
                            <div class="mb-2"><label class="form-label">Nome</label><input type="text" class="form-control" id="produtoNome"></div>
                            <div class="mb-2"><label class="form-label">Preço</label><input type="number" step="0.01" class="form-control" id="produtoPreco"></div>
                            <div class="mb-2"><label class="form-label">Estoque</label><input type="number" class="form-control" id="produtoEstoque"></div>
                            <div class="mb-2"><label class="form-label">Fornecedor</label><select class="form-select" id="produtoFornecedor"></select></div>
                            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-primary">Atualização (AJAX)</h2>
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Editar Produto</h5>
                        <form id="formEditarProduto">
                            <div class="mb-2"><label class="form-label">Nome atual</label><input type="text" class="form-control" id="editProdutoNomeAtual"></div>
                            <div class="mb-2"><label class="form-label">Novo nome</label><input type="text" class="form-control" id="editProdutoNovoNome"></div>
                            <div class="mb-2"><label class="form-label">Novo preço</label><input type="number" step="0.01" class="form-control" id="editProdutoNovoPreco"></div>
                            <button type="submit" class="btn btn-secondary w-100">Atualizar</button>
                        </form>
                        <div id="statusAtualizarProduto" class="mt-2 small"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Editar Fornecedor</h5>
                        <form id="formEditarFornecedor">
                            <div class="mb-2"><label class="form-label">Nome atual</label><input type="text" class="form-control" id="editFornecedorNomeAtual"></div>
                            <div class="mb-2"><label class="form-label">Novo nome</label><input type="text" class="form-control" id="editFornecedorNovoNome"></div>
                            <div class="mb-2"><label class="form-label">Novo telefone</label><input type="text" class="form-control" id="editFornecedorNovoTelefone"></div>
                            <button type="submit" class="btn btn-secondary w-100">Atualizar</button>
                        </form>
                        <div id="statusAtualizarFornecedor" class="mt-2 small"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Remover Produto da Cesta</h5>
                        <form id="formRemoverItem">
                            <div class="mb-2"><label class="form-label">Nome do produto</label><input type="text" class="form-control" id="removerProdutoNome"></div>
                            <button type="submit" class="btn btn-danger w-100">Remover</button>
                        </form>
                        <div id="statusRemoverItem" class="mt-2 small"></div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-primary">Produtos Disponíveis</h2>
        <ul id="listaProdutos" class="list-group mb-3"></ul>
        <button class="btn btn-success mb-5" onclick="adicionarACesta()">Adicionar selecionados à cesta</button>
    </div>

    <script>
        async function carregarFornecedoresNoSelect() {
            const resp = await fetch('api/fornecedores.php');
            const fornecedores = await resp.json();
            document.getElementById('produtoFornecedor').innerHTML =
                fornecedores.map(f => `<option value="${f.id}">${f.nome}</option>`).join('');
        }

        async function listarProdutosComCheckbox() {
            const resp = await fetch('api/produtos.php');
            const produtos = await resp.json();
            document.getElementById('listaProdutos').innerHTML = produtos.map(p => `
                <li class="list-group-item">
                    <label class="d-flex align-items-center gap-2 m-0">
                        <input type="checkbox" class="check-produto form-check-input mt-0" value="${p.id}">
                        ${p.nome} - R$ ${Number(p.preco).toFixed(2)}
                    </label>
                </li>
            `).join('');
        }

        function coletarSelecionados() {
            return Array.from(document.querySelectorAll('.check-produto:checked')).map(c => c.value);
        }

        async function adicionarACesta() {
            const produtoIds = coletarSelecionados();
            if (produtoIds.length === 0) { alert('Selecione ao menos um produto antes de continuar.'); return; }

            const resp = await fetch('api/cesta.php', {
                method: 'POST',
                body: JSON.stringify({ produtoIds })
            });
            const resultado = await resp.json();
            if (resultado.sucesso) {
                window.location.href = 'carrinho.php';
            }
        }

        document.getElementById('formFornecedor').addEventListener('submit', async function (event) {
            event.preventDefault();
            await fetch('api/fornecedores.php', {
                method: 'POST',
                body: JSON.stringify({
                    nome: document.getElementById('fornecedorNome').value,
                    cnpj: document.getElementById('fornecedorCnpj').value,
                    telefone: document.getElementById('fornecedorTelefone').value
                })
            });
            carregarFornecedoresNoSelect();
        });

        document.getElementById('formProduto').addEventListener('submit', async function (event) {
            event.preventDefault();
            await fetch('api/produtos.php', {
                method: 'POST',
                body: JSON.stringify({
                    nome: document.getElementById('produtoNome').value,
                    preco: parseFloat(document.getElementById('produtoPreco').value),
                    estoque: parseInt(document.getElementById('produtoEstoque').value || '0', 10),
                    fornecedor_id: document.getElementById('produtoFornecedor').value
                })
            });
            listarProdutosComCheckbox();
        });

        document.getElementById('formEditarProduto').addEventListener('submit', async function (event) {
            event.preventDefault();
            const resp = await fetch('api/produtos.php', {
                method: 'PUT',
                body: JSON.stringify({
                    nomeAtual: document.getElementById('editProdutoNomeAtual').value,
                    novoNome: document.getElementById('editProdutoNovoNome').value,
                    novoPreco: parseFloat(document.getElementById('editProdutoNovoPreco').value)
                })
            });
            const resultado = await resp.json();
            document.getElementById('statusAtualizarProduto').innerText = resultado.sucesso ? 'Produto atualizado!' : 'Produto não encontrado.';
            listarProdutosComCheckbox();
        });

        document.getElementById('formEditarFornecedor').addEventListener('submit', async function (event) {
            event.preventDefault();
            const resp = await fetch('api/fornecedores.php', {
                method: 'PUT',
                body: JSON.stringify({
                    nomeAtual: document.getElementById('editFornecedorNomeAtual').value,
                    novoNome: document.getElementById('editFornecedorNovoNome').value,
                    novoTelefone: document.getElementById('editFornecedorNovoTelefone').value
                })
            });
            const resultado = await resp.json();
            document.getElementById('statusAtualizarFornecedor').innerText = resultado.sucesso ? 'Fornecedor atualizado!' : 'Fornecedor não encontrado.';
            carregarFornecedoresNoSelect();
        });

        document.getElementById('formRemoverItem').addEventListener('submit', async function (event) {
            event.preventDefault();
            const resp = await fetch('api/cesta.php', {
                method: 'DELETE',
                body: JSON.stringify({ nomeProduto: document.getElementById('removerProdutoNome').value })
            });
            const resultado = await resp.json();
            document.getElementById('statusRemoverItem').innerText = resultado.sucesso ? 'Item removido!' : 'Não encontrado.';
        });

        carregarFornecedoresNoSelect();
        listarProdutosComCheckbox();
    </script>
</body>
</html>
