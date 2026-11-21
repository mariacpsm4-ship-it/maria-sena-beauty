
<?php
require 'conexao.php';

$categoria = trim($_GET['categoria'] ?? '');

$sql = "SELECT products.*, brands.name AS marca,
        categories.name AS categoria,
        COALESCE(SUM(product_variants.stock), 0) AS estoque
        FROM products
        INNER JOIN brands ON products.brand_id = brands.id
        INNER JOIN categories ON products.category_id = categories.id
        LEFT JOIN product_variants ON products.id = product_variants.product_id
        WHERE products.active = 1";

if ($categoria !== '') {
    $sql .= " AND categories.name = :categoria";
}

$sql .= " GROUP BY products.id ORDER BY products.id DESC";

$stmt = $pdo->prepare($sql);

if ($categoria !== '') {
    $stmt->bindValue(':categoria', $categoria);
}

$stmt->execute();
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | Maria Sena Beauty</title>

    <style>
        body {
            margin: 0;
            padding: 30px;
            background: #08182F;
            color: #F5F3EF;
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #D4AF37;
            text-align: center;
        }

        .topo {
            text-align: center;
            margin-bottom: 30px;
        }

        .topo a {
            color: #FFD700;
            margin: 10px;
            text-decoration: none;
        }

        .produtos {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .produto {
            background: #142C4E;
            border: 1px solid #D4AF37;
            border-radius: 10px;
            padding: 20px;
            width: 230px;
        }

        .produto img {
            width: 100%;
            height: 190px;
            object-fit: contain;
            background: #F5F3EF;
            border-radius: 8px;
        }

        .sem-imagem {
            height: 190px;
            background: #DCE5F0;
            color: #142C4E;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            text-align: center;
        }

        .produto h2 {
            color: #FFD700;
        }

        .preco {
            font-size: 20px;
            color: #D4AF37;
            font-weight: bold;
        }

        .botao {
            display: inline-block;
            background: #D4AF37;
            color: #08182F;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .botao:hover {
            background: #FFD700;
        }

        .voltar {
            display: inline-block;
            margin-bottom: 20px;
            color: #FFD700;
            text-decoration: none;
        }

        .vazio {
            text-align: center;
            width: 100%;
            padding: 40px;
        }
    </style>
</head>

<body>

<div class="topo">
    <h1>Maria Sena Beauty</h1>
    <p>Sua beleza, seu brilho.</p>
    <a href="index.php">Início</a>
    <a href="produtos.php">Todos os produtos</a>
    <a href="carrinho.php">Meu carrinho</a>
</div>

<a class="voltar" href="index.php">← Voltar à página inicial</a>

<h2>
    <?= $categoria !== ''
        ? 'Categoria: ' . htmlspecialchars($categoria)
        : 'Nossos produtos' ?>
</h2>

<div class="produtos">

<?php if (count($produtos) > 0): ?>

    <?php foreach ($produtos as $produto): ?>

        <div class="produto" id="produto-<?= (int) $produto['id'] ?>">

            <?php if (!empty($produto['image_url'])): ?>

                <img
                    src="<?= htmlspecialchars($produto['image_url']) ?>"
                    alt="<?= htmlspecialchars($produto['name']) ?>"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

                <div class="sem-imagem" style="display:none;">
                    Imagem indisponível
                </div>

            <?php else: ?>

                <div class="sem-imagem">
                    Imagem não cadastrada
                </div>

            <?php endif; ?>

            <h2><?= htmlspecialchars($produto['name']) ?></h2>

            <p>Marca: <?= htmlspecialchars($produto['marca']) ?></p>

            <p>Categoria: <?= htmlspecialchars($produto['categoria']) ?></p>

            <p><?= htmlspecialchars($produto['description'] ?? '') ?></p>

            <p class="preco">
                R$ <?= number_format($produto['price'], 2, ',', '.') ?>
            </p>

            <p>Estoque: <?= (int) $produto['estoque'] ?></p>

            <?php if ((int) $produto['estoque'] > 0): ?>
                <a class="botao"
                   href="carrinho.php?adicionar=<?= (int) $produto['id'] ?>">
                    Adicionar ao carrinho
                </a>
            <?php else: ?>
                <p>Produto indisponível</p>
            <?php endif; ?>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="vazio">
        <h3>Nenhum produto encontrado nesta categoria.</h3>
        <a class="botao" href="produtos.php">Ver todos os produtos</a>
    </div>

<?php endif; ?>

</div>

</body>
</html>
