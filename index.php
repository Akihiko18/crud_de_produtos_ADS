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
    <title>Login</title>
</head>
<body class="bg-light">
    <div class="container" style="max-width:400px; margin-top:80px;">
        <h1 class="text-center mb-4 text-primary">Sistema de Gestão de Produtos</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Login</h5>
                <form id="formLogin">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" id="loginEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" class="form-control" id="loginSenha">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <div id="resultadoLogin" class="mt-3 text-danger"></div>
                <p class="text-center mt-3">Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formLogin').addEventListener('submit', async function (event) {
            event.preventDefault();
            const resposta = await fetch('api/auth.php', {
                method: 'POST',
                body: JSON.stringify({
                    acao: 'login',
                    email: document.getElementById('loginEmail').value,
                    senha: document.getElementById('loginSenha').value
                })
            });
            const resultado = await resposta.json();
            if (resultado.sucesso) {
                window.location.href = 'dashboard.php';
            } else {
                document.getElementById('resultadoLogin').innerText = resultado.erro;
            }
        });
    </script>
</body>
</html>
