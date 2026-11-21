
<?php
require 'conexao.php';

$categorias = $pdo->query(
    "SELECT * FROM categories ORDER BY name"
)->fetchAll();

$sql = "SELECT products.*, brands.name AS marca,
        COALESCE(SUM(product_variants.stock), 0) AS estoque
        FROM products
        INNER JOIN brands ON products.brand_id = brands.id
        LEFT JOIN product_variants ON products.id = product_variants.product_id
        WHERE products.active = 1
        GROUP BY products.id
        ORDER BY products.id DESC
        LIMIT 4";

$produtos = $pdo->query($sql)->fetchAll();

$simbolos = ['✦', '✧', '❖', '✺', '✷', '✹'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Maria Sena Beauty</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #08182F;
            --navy-light: #142C4E;
            --gold: #D4AF37;
            --gold-light: #FFD966;
            --white: #F8F7F3;
            --gray: #B9C1CE;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--navy);
            color: var(--white);
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: #061326;
            padding: 18px 0;
            border-bottom: 1px solid rgba(212,175,55,.35);
        }

        .navbar-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 29px;
            font-weight: 700;
            color: var(--gold-light);
            letter-spacing: 2px;
        }

        .navbar-brand:hover {
            color: var(--gold);
        }

        .nav-link {
            color: white;
            font-size: 13px;
            margin-left: 20px;
        }

        .nav-link:hover {
            color: var(--gold-light);
        }

        .hero {
            min-height: 510px;
            display: flex;
            align-items: center;
            background:
                linear-gradient(90deg, rgba(8,24,47,.98) 35%, rgba(8,24,47,.35)),
                url('https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=1600')
                center/cover;
            padding: 80px 0;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-subtitle, .section-subtitle {
            color: var(--gold-light);
            letter-spacing: 4px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(45px, 7vw, 76px);
            line-height: 1.05;
            margin: 20px 0;
        }

        .hero h1 span {
            color: var(--gold-light);
            font-style: italic;
        }

        .hero p {
            color: #D2D8E1;
            font-size: 14px;
            line-height: 1.9;
            max-width: 450px;
        }

        .btn-gold {
            display: inline-block;
            background: linear-gradient(135deg, #F5D77C, #B8860B);
            color: var(--navy);
            padding: 14px 28px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            border-radius: 3px;
            margin-top: 20px;
        }

        .btn-gold:hover {
            background: #FFE69A;
            color: var(--navy);
        }

        .categories {
            padding: 65px 0;
            text-align: center;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 43px;
            margin: 12px 0 35px;
        }

        .category {
            display: block;
            border: 1px solid rgba(212,175,55,.4);
            padding: 23px 10px;
            height: 100%;
            color: var(--white);
            text-decoration: none;
            transition: .3s;
        }

        .category:hover {
            background: var(--navy-light);
            border-color: var(--gold);
            transform: translateY(-5px);
            color: var(--gold-light);
        }

        .category .symbol {
            font-size: 29px;
            color: var(--gold-light);
        }

        .category h5 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 23px;
            margin-top: 12px;
        }

        .products {
            background: #0D203A;
            padding: 65px 0 80px;
            text-align: center;
        }

        .product-card {
            background: var(--navy-light);
            border: 1px solid rgba(212,175,55,.25);
            height: 100%;
            text-align: left;
            transition: .3s;
        }

        .product-card:hover {
            transform: translateY(-6px);
            border-color: var(--gold);
        }

        .product-card img {
            width: 100%;
            height: 240px;
            object-fit: contain;
            background: #F5F3EF;
        }

        .sem-imagem {
            height: 240px;
            background: #DCE5F0;
            color: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-info {
            padding: 20px;
        }

        .product-info h5 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
        }

        .product-info p {
            color: var(--gray);
            font-size: 12px;
        }

        .price {
            color: var(--gold-light);
            font-weight: 600;
            font-size: 17px;
        }

        .btn-outline-gold {
            border: 1px solid var(--gold);
            color: var(--gold-light);
            padding: 9px 15px;
            text-decoration: none;
            display: inline-block;
            font-size: 11px;
            margin-top: 10px;
        }

        .btn-outline-gold:hover {
            background: var(--gold);
            color: var(--navy);
        }

        footer {
            background: #061326;
            border-top: 1px solid rgba(212,175,55,.35);
            padding: 40px 15px;
            text-align: center;
        }

        footer h3 {
            font-family: 'Cormorant Garamond', serif;
            color: var(--gold-light);
            letter-spacing: 2px;
        }

        footer p {
            color: var(--gray);
            font-size: 12px;
            margin-top: 12px;
        }

        @media (max-width: 768px) {
            .hero {
                min-height: 450px;
                text-align: center;
            }

            .hero-content {
                margin: auto;
            }

            .hero p {
                margin: auto;
            }

            .nav-link {
                margin-left: 0;
            }

            .section-title {
                font-size: 35px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            MARIA SENA BEAUTY
        </a>

        <button class="navbar-toggler bg-light" type="button"
            data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">INÍCIO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="produtos.php">PRODUTOS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="carrinho.php">CARRINHO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">MINHA CONTA</a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<section class="hero">
    <div class="container">
        <div class="hero-content">

            <span class="hero-subtitle">BELEZA EM CADA DETALHE</span>

            <h1>
                Descubra o poder da sua <span>beleza.</span>
            </h1>

            <p>
                Encontre seus produtos favoritos e transforme
                cada momento em uma oportunidade de brilhar.
                Sua beleza merece o melhor!
            </p>

            <a href="produtos.php" class="btn-gold">
                EXPLORAR PRODUTOS ✦
            </a>

        </div>
    </div>
</section>

<section class="categories">
    <div class="container">

        <span class="section-subtitle">ENCONTRE SEUS FAVORITOS</span>

        <h2 class="section-title">Explore nossas categorias</h2>

        <div class="row g-4">

            <?php foreach ($categorias as $indice => $categoria): ?>

                <div class="col-6 col-md-3">

                    <a class="category"
                       href="produtos.php?categoria=<?= urlencode($categoria['name']) ?>">

                        <div class="symbol">
                            <?= $simbolos[$indice % count($simbolos)] ?>
                        </div>

                        <h5>
                            <?= htmlspecialchars($categoria['name']) ?>
                        </h5>

                    </a>

                </div>

            <?php endforeach; ?>

        </div>
    </div>
</section>

<section class="products">
    <div class="container">

        <span class="section-subtitle">SELEÇÃO ESPECIAL</span>

        <h2 class="section-title">Nossos destaques</h2>

        <div class="row g-4">

            <?php if (count($produtos) > 0): ?>

                <?php foreach ($produtos as $produto): ?>

                    <div class="col-12 col-sm-6 col-lg-3">

                        <div class="product-card">

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

                            <div class="product-info">

                                <h5>
                                    <?= htmlspecialchars($produto['name']) ?>
                                </h5>

                                <p>
                                    <?= htmlspecialchars($produto['marca']) ?>
                                </p>

                                <div class="price">
                                    R$ <?= number_format($produto['price'], 2, ',', '.') ?>
                                </div>

                                <a href="produtos.php#produto-<?= (int) $produto['id'] ?>"
                                   class="btn-outline-gold">
                                    VER PRODUTO
                                </a>

                            </div>
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p>Nossos produtos estarão disponíveis em breve.</p>

            <?php endif; ?>

        </div>
    </div>
</section>

<footer>
    <h3>MARIA SENA BEAUTY</h3>

    <p>Sua beleza, seu brilho.</p>

    <p>© 2026 Maria Sena Beauty. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
