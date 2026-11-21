
<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'conexao.php';

$sql = "SELECT products.*, brands.name AS marca
        FROM products
        INNER JOIN brands ON products.brand_id = brands.id
        ORDER BY products.id DESC";

$produtos = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar produtos | Maria Sena Beauty</title>
<style>
body {
    background: #08182F;
    color: #F5F3EF;
    font-family: Arial;
    padding: 30px;
}
h1 {
    color: #D4AF37;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: #142C4E;
}
th, td {
    padding: 12px;
    border: 1px solid #D4AF37;
    text-align: left;
}
th {
    color: #FFD700;
}
a {
    color: #FFD700;
}
</style>
</head>
<body>

<h1>Gerenciar produtos</h1>

<table>
<tr>
    <th>Produto</th>
    <th>Marca</th>
    <th>Preço</th>
    <th>Ações</th>
</tr>

<?php foreach ($produtos as $produto): ?>
<tr>
    <td><?= htmlspecialchars($produto['name']) ?></td>
    <td><?= htmlspecialchars($produto['marca']) ?></td>
    <td>R$ <?= number_format($produto['price'], 2, ',', '.') ?></td>
    <td>
       <a href="editar_produto.php?id=<?= (int) $produto['id'] ?>">Editar</a>

<form method="POST" action="excluir_produto.php" onsubmit="return confirm('Deseja realmente desativar este produto?');">
    <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
    <button type="submit">Excluir</button>
</form>
    </td>
</tr>
<?php endforeach; ?>

</table>

<br>
<a href="painel.php">Voltar ao painel</a>

</body>
</html>
