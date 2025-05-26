<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Deletado</title>
    <style>
        .deleted-profile {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .deleted-message {
            font-size: 1.2em;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="deleted-profile">
    <img src="../assets/images/deleted-user.png" alt="Perfil Deletado" style="width: 100px;">
    <div class="deleted-message">
        <h2>Este perfil não está mais disponível</h2>
        <p>O usuário <?php echo htmlspecialchars($username); ?> excluiu sua conta.</p>
    </div>
</div>
</body>
</html>