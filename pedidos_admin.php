
<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'conexao.php';

$sql = "SELECT orders.*, users.name AS cliente
        FROM orders
        INNER JOIN users ON orders.user_id = users.id
        ORDER BY orders.id DESC";

$pedidos = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Pedidos | Maria Sena Beauty</title>

<style>
body {
    background: #08182F;
    color: #F5F3EF;
    font-family: Arial, sans-serif;
    padding: 30px;
}

h1 {
    color: #D4AF37;
    text-align: center;
}

.pedido {
    background: #142C4E;
    border: 1px solid #D4AF37;
    border-radius: 10px;
    padding: 20px;
    margin: 20px auto;
    max-width: 700px;
}

h2 {
    color: #FFD700;
}

a {
    color: #FFD700;
}
</style>
</head>

<body>

<h1>Pedidos realizados</h1>

<?php if (empty($pedidos)): ?>

    <p>Nenhum pedido realizado até o momento.</p>

<?php else: ?>

    <?php foreach ($pedidos as $pedido): ?>

        <div class="pedido">

            <h2>Pedido #<?= (int) $pedido['id'] ?></h2>

            <p><strong>Cliente:</strong>
            <?= htmlspecialchars($pedido['cliente']) ?></p>

            <p><strong>Data:</strong>
            <?= htmlspecialchars($pedido['created_at']) ?></p>

            <p><strong>Pagamento:</strong>
            <?= htmlspecialchars($pedido['payment_method']) ?></p>

            <p><strong>Status:</strong>
            <?= htmlspecialchars($pedido['status']) ?></p>

            <h3>Produtos:</h3>

            <?php
            $stmt = $pdo->prepare(
                "SELECT products.name, order_items.quantity,
                        order_items.unit_price
                 FROM order_items
                 INNER JOIN products
                 ON order_items.product_id = products.id
                 WHERE order_items.order_id = ?"
            );

            $stmt->execute([$pedido['id']]);
            $itens = $stmt->fetchAll();
            ?>

            <?php foreach ($itens as $item): ?>

                <p>
                    <?= htmlspecialchars($item['name']) ?>
                    — Quantidade: <?= (int) $item['quantity'] ?>
                    — R$ <?= number_format($item['unit_price'], 2, ',', '.') ?>
                </p>

            <?php endforeach; ?>

            <h3>
                Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
            </h3>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

<a href="painel.php">Voltar ao painel</a>

</body>
</html>
