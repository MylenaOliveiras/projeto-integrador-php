<?php

session_start();
require_once('conexao.php');

if (!isset($_SESSION['admin_logado'])) { //se nao existeir um adm logado, vamos direcionar ele para pagina de login
    header('Location:login.php');
    exit();
}

$busca = isset($_POST['busca']) ? $_POST['busca'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : 1;
try {

    if ($busca && trim($busca) !== '') {
        $stmt = $pdo->prepare("SELECT * FROM CATEGORIA WHERE CATEGORIA_NOME LIKE ? AND CATEGORIA_ATIVO = ?");
        $stmt->execute(array("%$busca%", $status));
    } else if ($status !== null) {
        $stmt = $pdo->prepare("SELECT * FROM CATEGORIA WHERE CATEGORIA_ATIVO = ?");
        $stmt->execute(array($status));
    } else {
        $stmt = $pdo->prepare("SELECT * FROM CATEGORIA");
        $stmt->execute();
    }
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if (isset($_SESSION['success_message_categoria'])) {
    echo "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#45AA2B] text-white px-6 py-4 rounded-lg flex item-center gap-2'>
    <img src='icons/check.svg' class='w-6'>
    <span class='mr-2'>" . $_SESSION['success_message_categoria'] . "</span> 
    </div>";

    // Remover a mensagem de sucesso da sessão
    unset($_SESSION['success_message_categoria']);
}
if (isset($_SESSION['error_message_categoria'])) {
    echo "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#FF0000] text-white px-6 py-4 rounded-lg flex item-center gap-2'>
    <img src='icons/information.svg' class='w-6'>
    <span class='mr-2'>" . $_SESSION['error_message_categoria'] . "</span> 
    </div>";

    // Remover a mensagem de sucesso da sessão
    unset($_SESSION['error_message_categoria']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar categorias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <header class="py-1 flex justify-between px-10">
        <button id="menu">
            <img src="icons/menu.svg">
        </button>
        <div class="flex gap-2">
            <img src="icons/username.svg">
            <span class="font-bold"><?php echo $_SESSION['admin_nome'] ?><span>
        </div>
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
        <section class="w-full bg-[#00AFEF] rounded-tl-lg pt-6 pb-4 px-10">
            <div class="flex items-center justify-between">
                <div class="flex gap-4">
                    <img src="icons/categoria.svg">
                    <h2 class="text-white font-bold text-3xl">Categorias</h2>
                </div>
                <a href="cadastrar_categoria.php" class="text-white">
                    <button id="cadastrar" class="flex gap-2 bg-white text-[#0b7ca5] rounded-lg py-2 px-4 font-bold">
                        <img src="icons/add.svg" class="w-6 h-6">
                        Cadastrar
                    </button>
                </a>
            </div>
            <div class="mt-4">
                <form class="flex items-center justify-between" action="listar_categoria.php" method="POST" id="searchForm">

                    <div class="flex items-center">
                        <input class="p-2 rounded-l-lg outlined-none w-64" type="text" name="busca" placeholder="Buscar por nome da categoria" value="<?php echo htmlspecialchars($busca); ?>" id="searchInput">
                        <button class="bg-[#0b7ca5] p-2 rounded-r-lg" type="submit">
                            <img src="icons/search.svg" class="w-6 h-6">
                        </button>
                        <button id="limparBuscar" class="hidden text-white font-bold ml-4">Limpar busca</button>
                    </div>
                    <div class="flex text-white">
                        <div class="flex text-white">
                            <?php
                            $status = isset($_POST['status']) ? $_POST['status'] : '1';
                            $ativoClass = $status === '1' ? 'bg-[#0b7ca5]' :  'bg-[#00AFEF]' ;
                            $inativoClass = $status === '0' ? 'bg-[#0b7ca5]' : 'bg-[#00AFEF]';
                            ?>
                            <button name="status" value="1" class="<?php echo $ativoClass; ?> p-2 px-6 rounded-l-lg shadow-lg border-2 border-white border-r-0" type="submit">Ativo</button>
                            <button name="status" value="0" class="<?php echo $inativoClass; ?> p-2  px-6 rounded-r-lg border-2 border-white border-l-0" type="submit">Inativo</button>
                        </div>
                    </div>
                </form>
            </div>
            <div>
                <div class="flex justify-center max-h-[560px] overflow-y-auto">
                <?php if (!empty($categorias)) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <?php foreach ($categorias as $categoria) :  ?>
                            <tr>
                                <td><?php echo $categoria['CATEGORIA_ID']; ?></td>
                                <td><?php echo $categoria['CATEGORIA_NOME']; ?></td>
                                <td><?php echo $categoria['CATEGORIA_DESC']; ?></td>
                                <td>
                                    <div class="rounded-lg m-auto text-white w-20" style="background-color: <?php echo $categoria['CATEGORIA_ATIVO'] ? '#109010' : '#D90000'; ?>;">
                                        <?php echo $categoria['CATEGORIA_ATIVO'] ? "Ativo" : "Inativo"; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-3">
                                        <a class="mr-2" href="editar_categoria.php?id=<?php echo $categoria['CATEGORIA_ID']; ?>">
                                            <button>
                                                <img src="icons/edit.svg" />
                                            </button>
                                        </a>
                                        <button onclick="openConfirmDelete(<?php echo $categoria['CATEGORIA_ID']; ?>)" id="delete">
                                            <img src="icons/trash.svg" />
                                        </button>
                                    </div>
                                </td>
                               
                            </tr>
                        <?php endforeach; ?>
                    </table>
                       <?php else : ?>
                <div class="flex flex-col items-center mt-16">
                    <h3 class="font-bold text-white text-lg">Nenhuma categoria encontrada</h3>
                    <img src="imagens/empty-categoria.png" class="w-[450px]">
                </div>
                <?php endif; ?>
                </div>
            </div>
        </section>
                <div id="dialogContainerDelete" class="fixed inset-0 flex items-center justify-center hidden">
            <div id="overlay" class="fixed inset-0 bg-black opacity-50"></div>
            <dialog id="confirmDelete" class="bg-white border-2 border-[#0b7ca5] drop-shadow-lg w-fit font-bold p-6 flex flex-col justify-between gap-6 rounded-lg">
                <form action="excluir_admin.php" method="get">
                    <input type="hidden" name="id" id="id">
                    <div class="flex gap-2 items-center">
                        <img src="icons/info.svg" class="w-7">
                        <h3 class="font-extrabold text-[#0b7ca5]">Tem certeza que deseja excluir esta categoria?</h3>
                    </div>
                    <p class="my-4">Esta ação não poderá ser desfeita.</p>
                    <div class="flex gap-4 justify-end">
                        <a class="border-2 border-[#0b7ca5] text-[#0b7ca5] px-3 py-px rounded-lg hover:border-none hover:bg-[#0b7ca5] hover:px-[14px] hover:py-[3px] hover:text-white" onclick="confirmDelete()">Sim</a>
                        <button id="cancelDelete" class="bg-[#0b7ca5] px-3 py-px rounded-lg text-white" type="button">Não</button>
                    </div>
                </form>
            </dialog>
        </div>
<div id="dialogContainer" class="fixed inset-0 flex items-center justify-center hidden">
            <div id="overlay" class="fixed inset-0 bg-black opacity-50"></div>
            <dialog id="confirmLogout" class="bg-white border-2 border-[#0b7ca5] drop-shadow-lg w-96 font-bold p-6 flex flex-col justify-between gap-6 rounded-lg">
                <h3 class="text-lg">Tem certeza que deseja sair?</h3>
                <div class="flex gap-4 justify-end">
                    <button class="border-2 border-[#0b7ca5] text-[#0b7ca5] px-3 py-px rounded-lg hover:border-none hover:bg-[#0b7ca5] hover:px-[14px] hover:py-[3px] hover:text-white" onclick="window.location.href='logout.php'">Sim</button>
                    <button id="cancel" class="bg-[#0b7ca5] px-3 py-px rounded-lg text-white">Não</button>
                </div>
            </dialog>
        </div></body>
</html>
<script src="index.js"></script>
<script>
function confirmDelete() {
    var id = document.getElementById('id').value;
    window.location.href = "excluir_categoria.php?id=" + id;
}
</script>