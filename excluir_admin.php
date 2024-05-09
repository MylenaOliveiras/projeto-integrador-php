<?php
session_start();
require_once('conexao.php');

if (!isset($_SESSION['admin_logado'])) {
    header('Location:login.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM ADMINISTRADOR WHERE ADM_ID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $message = "Administrador excluído com sucesso!";
            $_SESSION['success_message_admin'] = $message;

        } else {
            $message = "Erro ao excluir Administrador!";
        }
    } catch (PDOException $e) {
        $message = "Erro: " . $e->getMessage();


    }
}

// Redirecionamento de volta para a mesma página com a mensagem
header('Location: listar_admin.php?message=' . urlencode($message));
exit();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Administrador</title>
</head>
<body>
    <h2>Excluir Administrador</h2>
    <p><?php echo $message; ?></p>
    <a href="listar_admin.php">Voltar à Lista de Administradores</a>
</body>
</html>