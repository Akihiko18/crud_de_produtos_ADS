<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Cadastrar Usuário</title>
</head>
<body class="bg-light">
    <div class="container" style="max-width:400px; margin-top:80px;">
        <h1 class="text-center mb-4 text-primary">Sistema de Gestão de Produtos</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Cadastrar Usuário</h5>
                <form id="formCadastro">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" id="cadNome">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" id="cadEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" class="form-control" id="cadSenha">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                </form>
                <div id="resultadoCadastro" class="mt-3"></div>
                <p class="text-center mt-3">Já tem conta? <a href="index.php">Fazer login</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formCadastro').addEventListener('submit', async function (event) {
            event.preventDefault();
            const resposta = await fetch('api/auth.php', {
                method: 'POST',
                body: JSON.stringify({
                    acao: 'cadastrar',
                    nome: document.getElementById('cadNome').value,
                    email: document.getElementById('cadEmail').value,
                    senha: document.getElementById('cadSenha').value
                })
            });
            const resultado = await resposta.json();
            const div = document.getElementById('resultadoCadastro');
            if (resultado.sucesso) {
                div.className = 'mt-3 text-success';
                div.innerText = 'Cadastrado! Redirecionando pro login...';
                setTimeout(() => { window.location.href = 'index.php'; }, 1500);
            } else {
                div.className = 'mt-3 text-danger';
                div.innerText = resultado.erro;
            }
        });
    </script>
</body>
</html>
