<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Sistema de Gestão de Produtos</a>
    <div class="navbar-nav">
      <a class="nav-link" href="dashboard.php">Cadastro / Atualização</a>
      <a class="nav-link" href="carrinho.php">Minha Cesta</a>
      <a class="nav-link" href="#" id="linkSair">Sair</a>
    </div>
  </div>
</nav>

<script>
document.getElementById('linkSair').addEventListener('click', async function (event) {
    event.preventDefault();
    await fetch('api/auth.php', { method: 'POST', body: JSON.stringify({ acao: 'logout' }) });
    window.location.href = 'index.php';
});
</script>
