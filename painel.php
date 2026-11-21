
<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo | Maria Sena Beauty</title>

    <style>
        body {
            background: #08182F;
            color: #F5F3EF;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }

        h1 {
            color: #D4AF37;
        }

        .menu {
            background: #142C4E;
            border: 1px solid #D4AF37;
            border-radius: 10px;
            padding: 30px;
            max-width: 400px;
            margin: auto;
        }

        a {
            display: block;
            background: #D4AF37;
            color: #08182F;
            padding: 12px;
            margin: 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

<h1>Maria Sena Beauty</h1>
<h2>Painel administrativo</h2>

<div class="menu">
    <p>Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</p>

    <a href="cadastrar_produtos.php">Cadastrar produtos</a>
    <a href="gerenciar_produtos.php">Gerenciar produtos</a>
    <a href="pedidos_admin.php">Ver pedidos</a>
    <a href="produtos.php">Ver loja</a>
    <a href="sair.php">Sair</a>
</div>

</body>
</html>

