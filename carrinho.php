

<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$mensagem = '';

if (isset($_GET['adicionar'])) {
    $id = (int) $_GET['adicionar'];

    $stmt = $pdo->prepare(
        "SELECT products.id, COALESCE(SUM(product_variants.stock), 0) AS estoque
         FROM products
         LEFT JOIN product_variants ON products.id = product_variants.product_id
         WHERE products.id = ? AND products.active = 1
         GROUP BY products.id"
    );

    $stmt->execute([$id]);
    $produto = $stmt->fetch();

    if ($produto) {
        $quantidadeAtual = $_SESSION['carrinho'][$id] ?? 0;

        if ($quantidadeAtual < (int) $produto['estoque']) {
            $_SESSION['carrinho'][$id] = $quantidadeAtual + 1;
        } else {
            $_SESSION['mensagem_carrinho'] = 'Quantidade indisponível no estoque.';
        }
    }

    header('Location: carrinho.php');
    exit;
}

if (isset($_GET['remover'])) {
    $id = (int) $_GET['remover'];
    unset($_SESSION['carrinho'][$id]);

    header('Location: carrinho.php');
    exit;
}

$mensagem = $_SESSION['mensagem_carrinho'] ?? '';
unset($_SESSION['mensagem_carrinho']);

$produtos = [];
$total = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    $marcadores = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT products.id, products.name, products.price,
            COALESCE(SUM(product_variants.stock), 0) AS estoque
            FROM products
            LEFT JOIN product_variants ON products.id = product_variants.product_id
            WHERE products.id IN ($marcadores) AND products.active = 1
            GROUP BY products.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho | Maria Sena Beauty</title>

    <style>
        body {
            background: #08182F;
            color: #F5F3EF;
            font-family: Arial;
            padding: 30px;
        }

        h1 { color: #D4AF37; }

        .item {
            background: #142C4E;
            border: 1px solid #D4AF37;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        a { color: #FFD700; }

        .total {
            color: #D4AF37;
            font-size: 22px;
        }

        .mensagem {
            color: #FFD700;
        }
    </style>
</head>

<body>

<h1>Meu carrinho</h1>

<?php if ($mensagem): ?>
    <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
<?php endif; ?>

<?php if (empty($produtos)): ?>

    <p>Seu carrinho está vazio.</p>

<?php else: ?>

    <?php foreach ($produtos as $produto): ?>

        <?php
        $quantidade = $_SESSION['carrinho'][$produto['id']];
        $subtotal = $produto['price'] * $quantidade;
        $total += $subtotal;
        ?>

        <div class="item">
            <h2><?= htmlspecialchars($produto['name']) ?></h2>

            <p>Quantidade: <?= (int) $quantidade ?></p>

            <p>Preço: R$ <?= number_format($produto['price'], 2, ',', '.') ?></p>

            <p>Subtotal: R$ <?= number_format($subtotal, 2, ',', '.') ?></p>

            <p>Estoque disponível: <?= (int) $produto['estoque'] ?></p>

            <a href="carrinho.php?remover=<?= (int) $produto['id'] ?>">Remover</a>
        </div>

    <?php endforeach; ?>

    <p class="total">
        Total: R$ <?= number_format($total, 2, ',', '.') ?>
    </p>

    <a href="finalizar.php"
       style="display:inline-block; background:#D4AF37; color:#08182F; padding:12px; text-decoration:none; border-radius:5px;">
        Finalizar compra
    </a>

<?php endif; ?>

<br><br>
<a href="produtos.php">Continuar comprando</a>

</body>
</html>

