<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'conexao.php';

$marcas = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$categorias = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $marca = $_POST['marca'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $variacao = $_POST['variacao'];
    $estoque = $_POST['estoque'];
$imagem = $_POST['imagem'];
    $sql = "INSERT INTO products
    (brand_id, category_id, name, description, price, image_url)
VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$marca, $categoria, $nome, $descricao, $preco, $imagem]);

    $produto = $pdo->lastInsertId();

    $sql = "INSERT INTO product_variants
    (product_id, variant_name, stock)
    VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$produto, $variacao, $estoque]);

    echo "Produto cadastrado!";
}
?>

<h1>Cadastro de produtos</h1>

<form method="POST">
    <label>Nome do produto:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Marca:</label><br>
    <select name="marca" required>
        <?php foreach ($marcas as $marca): ?>
            <option value="<?= $marca['id'] ?>">
                <?= htmlspecialchars($marca['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Categoria:</label><br>
    <select name="categoria" required>
        <?php foreach ($categorias as $categoria): ?>
            <option value="<?= $categoria['id'] ?>">
                <?= htmlspecialchars($categoria['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Descrição:</label><br>
    <textarea name="descricao"></textarea><br><br>
<label>Link da imagem:</label><br>
<input type="text" name="https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcSMHUcmqflNpSHmnZdjmZh2LkIVCRXGHHPCstDQAlWWbgeOJ9NKe--9O_PC1yQ0D3Qf4q_hzkdpyjwWF_69zG2oluA2jpl8ddmNlsigmVqsjageduoESYeN6MTnJv3BNu38WQdbESQ&usqp=CAc"><br><br>
    <label>Preço:</label><br>
    <input type="number" name="preco" step="0.01" min="0.01" required><br><br>

    <label>Variação ou tom:</label><br>
    <input type="text" name="variacao" value="Padrão" required><br><br>

    <label>Estoque:</label><br>
    <input type="number" name="estoque" min="0" required><br><br>

    <button type="submit" name="cadastrar">Cadastrar produto</button>
</form>