<?php

session_start();
require_once('conexao.php');

if (!isset($_SESSION['admin_logado'])) { //se nao existeir um adm logado, vamos direcionar ele para pagina de login
    header('Location:login.php');
    exit();
}

$totalAdmins = 0;
$totalProdutos = 0;
$totalCategoria = 0;

try {
    $sql = "SELECT COUNT(*) as total FROM ADMINISTRADOR";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalAdmins = $result['total'];

    $sql = "SELECT COUNT(*) as total FROM PRODUTO";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalProdutos = $result['total'];

    $sql = "SELECT COUNT(*) as total FROM CATEGORIA";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCategoria = $result['total'];
} catch (PDOException $e) {
    echo "Erro ao obter a quantidade: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do ADM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <header class="py-1 flex gap-2 justify-end px-10">
        <img src="icons/username.svg">
        <span class="font-bold"><?php echo $_SESSION['admin_nome'] ?><span>
    </header>
    <main class="flex gap-4 overflow-hidden">
        <nav class="h-full bg-[#00AFEF] min-w-72 max-w-72  flex flex-col rounded-tr-lg gap-[200px] px-6">
            <div class="flex flex-col justify-between">
                <a href="painel_admin.php" class="flex text-white font-semibold">
                    <div class="bg-white rounded-tl-lg rounded-tr-[30px] rounded-bl-[30px] rounded-br-lg max-w-96 m-auto h-36 my-10">
                        <img src="imagens/logo.webp" class="-mt-10">
                    </div>
                </a>
                <ul>
                    <li>
                        <a href="painel_admin.php" class="item-menu">
                            <img src="icons/painel.svg">
                            <button>Painel</button>
                        </a>
                    </li>
                    <li>
                        <a href="listar_admin.php" class="item-menu">
                            <img src="icons/admin.svg">
                            <button>Admin</button>
                        </a>
                    </li>
                    <li>
                        <a href="listar_produtos.php" class="item-menu">
                            <img src="icons/produtos.svg">
                            <button class="-ml-px">Produtos</button>
                        </a>
                    </li>
                     <li>
                        <a href="listar_categoria.php" class="item-menu">
                            <img src="icons/categoria.svg">
                            <button class="-ml-px">Categoria</button>
                        </a>
                    </li>
                </ul>
            </div>
            <button id="logout" class="flex text-white font-xl font-semibold gap-4 items-center mb-10 ml-2.5">
                <img src="icons/logout.svg" class="w-7">
                    Sair            
            </button>
        </nav>

        <section class="w-full bg-[#00AFEF] rounded-tl-lg flex flex-col items-center gap-20">
            <div class="flex p-6 gap-4 pt-10 justify-center">
                <div class="bg-white p-6 rounded-3xl flex flex-col items-center gap-2 h-32">
                    <div class="flex items-center gap-3 text-[#2E3FD9] text-lg font-bold">
                        <img class="icon" src="icons/users-painel.svg">
                        <span>Usuários cadastrados</span>
                    </div>
                    <span class="text-3xl font-bold text-[#2E3FD9]"><?php echo $totalAdmins; ?></span>
                </div>
                <div class="bg-white p-6 rounded-3xl flex flex-col items-center gap-2 h-32">
                    <div class="flex items-center gap-3 text-[#2E3FD9] text-lg font-bold">
                        <img class="icon" src="icons/produtos-painel.svg">
                        <span>Produtos cadastrados</span>
                    </div>
                    <span class="text-3xl font-bold text-[#2E3FD9]"><?php echo $totalProdutos; ?></span>
                </div>
                <div class="bg-white p-6 rounded-3xl flex flex-col items-center gap-2 h-32">
                    <div class="flex items-center gap-3 text-[#2E3FD9] text-lg font-bold">
                        <img class="icon" src="icons/categoria-painel.svg">
                        <span>Categorias cadastradas</span>
                    </div>
                    <span class="text-3xl font-bold text-[#2E3FD9]"><?php echo $totalCategoria; ?></span>
                </div>
            </div>
            <img src="imagens/painel.png" class="object-cover w-[600px]">
        </section>

        <div id="dialogContainer" class="fixed inset-0 flex items-center justify-center hidden">
            <div id="overlay" class="fixed inset-0 bg-black opacity-50"></div>
            <dialog id="confirmLogout" class="bg-white border-2 border-[#2E3FD9] drop-shadow-lg w-96 font-bold p-6 flex flex-col justify-between gap-6 rounded-lg">
                <h3 class="text-lg">Tem certeza que deseja sair?</h3>
                <div class="flex gap-4 justify-end">
                    <button class="border-2 border-[#2E3FD9] text-[#2E3FD9] px-3 py-px rounded-lg hover:border-none hover:bg-[#2E3FD9] hover:px-[14px] hover:py-[3px] hover:text-white" onclick="window.location.href='logout.php'">Sim</button>
                    <button id="cancel" class="bg-[#2E3FD9] px-3 py-px rounded-lg text-white">Não</button>
                </div>
            </dialog>
        </div>
    </main>
</body>
</html>

<script src="index.js"></script>
