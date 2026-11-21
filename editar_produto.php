
<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'conexao.php';

$id = (int) ($_GET['id'] ?? 0);
$mensagem = '';

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    die("Produto não encontrado.");
}

if (isset($_POST['salvar'])) {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = (float) $_POST['preco'];
    $estoque = (int) $_POST['estoque'];

    $sql = "UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $descricao, $preco, $id]);

    $stmt = $pdo->prepare("SELECT id FROM product_variants WHERE product_id = ? LIMIT 1");
    $stmt->execute([$id]);
    $variante = $stmt->fetch();

    if ($variante) {
        $stmt = $pdo->prepare("UPDATE product_variants SET stock = ? WHERE id = ?");
        $stmt->execute([$estoque, $variante['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO product_variants (product_id, variant_name, stock) VALUES (?, 'Padrão', ?)");
        $stmt->execute([$id, $estoque]);
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();

    $mensagem = "Produto atualizado com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar produto | Maria Sena Beauty</title>
</head>
<body>

<h1>Editar produto</h1>

<p><?= htmlspecialchars($mensagem) ?></p>

<form method="POST">

<label>Nome:</label><br>
<input type="text" name="nome" value="<?= htmlspecialchars($produto['name']) ?>" required><br><br>

<label>Descrição:</label><br>
<textarea name="descricao"><?= htmlspecialchars($produto['description'] ?? '') ?></textarea><br><br>

<label>Preço:</label><br>
<input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($produto['price']) ?>" required><br><br>

<label>Estoque:</label><br>
<?php
$stmt = $pdo->prepare("SELECT SUM(stock) AS estoque FROM product_variants WHERE product_id = ?");
$stmt->execute([$id]);
$estoqueAtual = $stmt->fetch()['estoque'] ?? 0;
?>
<input type="number" name="estoque" min="0" value="<?= (int) $estoqueAtual ?>" required><br><br>

<button type="submit" name="salvar">Salvar alterações</button>

</form>

<a href="produtos.php">Voltar para produtos</a>

</body>
</html>
