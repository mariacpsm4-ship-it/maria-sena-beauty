
<?php
session_start();
require 'conexao.php';

$mensagem = '';

if (isset($_POST['entrar'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $sql = "SELECT users.*, roles.name AS perfil
            FROM users
            INNER JOIN roles ON users.role_id = roles.id
            WHERE users.email = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['password'])) {

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['name'];
        $_SESSION['perfil'] = $usuario['perfil'];

        if ($usuario['perfil'] === 'admin') {
            header('Location: painel.php');
        } else {
            header('Location: produtos.php');
        }

        exit;

    } else {
        $mensagem = "E-mail ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login | Maria Sena Beauty</title>

    <style>
        body {
            background: #08182F;
            color: #F5F3EF;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }

        form {
            background: #142C4E;
            border: 1px solid #D4AF37;
            padding: 25px;
            border-radius: 10px;
            max-width: 350px;
            margin: auto;
        }

        input {
            padding: 10px;
            width: 90%;
            margin: 10px 0;
        }

        button {
            background: #D4AF37;
            color: #08182F;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        h1 {
            color: #D4AF37;
        }

        a {
            color: #FFD700;
        }
    </style>
</head>

<body>

    <h1>Maria Sena Beauty</h1>
    <h2>Entrar na minha conta</h2>

    <form method="POST">
        <input type="email" name="email" placeholder="E-mail" required>

        <input type="password" name="senha" placeholder="Senha" required>

        <button type="submit" name="entrar">Entrar</button>
    </form>

    <p><?= htmlspecialchars($mensagem) ?></p>

    <p>Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>

    <a href="produtos.php">Voltar à loja</a>

</body>
</html>
