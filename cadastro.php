
<?php
require 'conexao.php';

$mensagem = '';

if (isset($_POST['cadastrar'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($nome == '' || $email == '' || $senha == '') {
        $mensagem = "Preencha todos os campos.";
    } else {
        $consulta = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $consulta = $pdo->prepare("SELECT id FROM roles WHERE name = 'cliente'");
            $consulta->execute();
            $papel = $consulta->fetch();

            if ($papel) {
                $sql = "INSERT INTO users (name, email, password, role_id)
                        VALUES (?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $senhaHash, $papel['id']]);

                $mensagem = "Cadastro realizado! Agora você pode entrar.";
            } else {
                $mensagem = "Não foi possível encontrar o perfil de cliente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro | Maria Sena Beauty</title>
    <style>
        body {
            background: #08182F;
            color: #F5F3EF;
            font-family: Arial;
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
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        h1 { color: #D4AF37; }
    </style>
</head>
<body>

<h1>Maria Sena Beauty</h1>
<h2>Criar minha conta</h2>

<form method="POST">
    <input type="text" name="nome" placeholder="Nome completo" required>
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="password" name="senha" placeholder="Senha" required>
    <button type="submit" name="cadastrar">Cadastrar</button>
</form>

<p><?= htmlspecialchars($mensagem) ?></p>

<a href="produtos.php" style="color:#FFD700;">Voltar à loja</a>

</body>
</html>
