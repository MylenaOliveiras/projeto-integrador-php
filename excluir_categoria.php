<?php
session_start();
require_once('conexao.php');

if(!isset($_SESSION['admin_logado'])){ //se nao existeir um adm logado, vamos direcionar ele para pagina de login
    header('Location:login.php');
    exit(); 
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM produto WHERE CATEGORIA_ID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $message = "Erro: A categoria está vinculada a produtos e não pode ser excluída.";
              $_SESSION['error_message_categoria'] = $message;

                                header('Location: listar_categoria.php');
exit();
            
        } else {
            $stmt = $pdo->prepare("DELETE FROM categoria WHERE CATEGORIA_ID = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $message = "Categoria excluída com sucesso!";
                $_SESSION['success_message_categoria'] = $message;
                header('Location: listar_categoria.php');
                exit();

            } else {
                $message = "Erro ao excluir Categoria!";
                                $_SESSION['error_message_categoria'] = $message;

                                header('Location: listar_categoria.php');
exit();
            }


        }
    } catch (PDOException $e) {
        $message = "Erro: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Categoria</title>
</head>
<body>
    <h2>Excluir Categoria</h2>
    <p><?php echo $message; ?></p>
    <a href="listar_categoria.php">Voltar à Lista de Categoria</a>
</body>
</html>