
<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

$mensagem = '';

if (isset($_POST['finalizar'])) {
    $pagamento = $_POST['pagamento'] ?? '';

    if (!in_array($pagamento, ['PIX', 'Boleto', 'Cartão'], true)) {
        $mensagem = 'Forma de pagamento inválida.';
    } else {
        try {
            $pdo->beginTransaction();

            $total = 0;
            $itens = [];

            foreach ($_SESSION['carrinho'] as $id => $quantidade) {
                $id = (int) $id;
                $quantidade = (int) $quantidade;

                if ($id <= 0 || $quantidade <= 0) {
                    throw new Exception('Produto ou quantidade inválida.');
                }

                $sql = "SELECT products.id, products.name, products.price,
                        product_variants.id AS variante,
                        product_variants.stock
                        FROM products
                        INNER JOIN product_variants
                        ON products.id = product_variants.product_id
                        WHERE products.id = ?
                        AND products.active = 1
                        AND product_variants.stock >= ?
                        ORDER BY product_variants.id
                        LIMIT 1
                        FOR UPDATE";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id, $quantidade]);
                $produto = $stmt->fetch();

                if (!$produto) {
                    throw new Exception(
                        'Estoque insuficiente ou produto indisponível. Volte ao carrinho.'
                    );
                }

                $total += $produto['price'] * $quantidade;

                $itens[] = [
                    'produto' => $produto,
                    'quantidade' => $quantidade
                ];
            }

            $sql = "INSERT INTO orders
                    (user_id, payment_method, total, status)
                    VALUES (?, ?, ?, 'Pendente')";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_SESSION['usuario_id'],
                $pagamento,
                $total
            ]);

            $pedido = $pdo->lastInsertId();

            foreach ($itens as $item) {
                $produto = $item['produto'];
                $quantidade = $item['quantidade'];

                $sql = "INSERT INTO order_items
                        (order_id, product_id, variant_id, quantity, unit_price)
                        VALUES (?, ?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $pedido,
                    $produto['id'],
                    $produto['variante'],
                    $quantidade,
                    $produto['price']
                ]);

                $sql = "UPDATE product_variants
                        SET stock = stock - ?
                        WHERE id = ? AND stock >= ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $quantidade,
                    $produto['variante'],
                    $quantidade
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new Exception('Não foi possível atualizar o estoque.');
                }
            }

            $pdo->commit();

            $_SESSION['carrinho'] = [];

            $mensagem = 'Pedido realizado com sucesso! Número do pedido: ' . $pedido;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $mensagem = $e instanceof PDOException
                ? 'Não foi possível concluir o pedido. Tente novamente.'
                : $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Finalizar compra | Maria Sena Beauty</title>

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

        form {
            background: #142C4E;
            border: 1px solid #D4AF37;
            padding: 25px;
            border-radius: 10px;
            max-width: 350px;
            margin: auto;
        }

        select, button {
            padding: 12px;
            margin: 10px;
        }

        button {
            background: #D4AF37;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        a {
            color: #FFD700;
        }
    </style>
</head>

<body>

<h1>Maria Sena Beauty</h1>
<h2>Finalizar compra</h2>

<?php if ($mensagem): ?>
    <p><?= htmlspecialchars($mensagem) ?></p>
<?php endif; ?>

<?php if (!empty($_SESSION['carrinho'])): ?>

    <form method="POST">
        <p>Escolha a forma de pagamento:</p>

        <select name="pagamento" required>
            <option value="PIX">PIX</option>
            <option value="Boleto">Boleto</option>
            <option value="Cartão">Cartão</option>
        </select>

        <br>

        <button type="submit" name="finalizar">
            Confirmar pedido
        </button>
    </form>

<?php endif; ?>

<p><a href="carrinho.php">Voltar ao carrinho</a></p>

</body>
</html>