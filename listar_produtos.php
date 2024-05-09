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
        $stmt = $pdo->prepare("SELECT PRODUTO.*, CATEGORIA.CATEGORIA_NOME, PRODUTO_IMAGEM.IMAGEM_URL, PRODUTO_ESTOQUE.PRODUTO_QTD 
        FROM PRODUTO 
        JOIN CATEGORIA ON PRODUTO.CATEGORIA_ID = CATEGORIA.CATEGORIA_ID
        LEFT JOIN PRODUTO_IMAGEM ON PRODUTO.PRODUTO_ID = PRODUTO_IMAGEM.PRODUTO_ID
        LEFT JOIN PRODUTO_ESTOQUE ON PRODUTO.PRODUTO_ID = PRODUTO_ESTOQUE.PRODUTO_ID
        WHERE PRODUTO.PRODUTO_NOME LIKE ? AND PRODUTO.PRODUTO_ATIVO = ?");
        $stmt->execute(array("%$busca%", $status));
    } else if ($status !== null) {
        $stmt = $pdo->prepare("SELECT PRODUTO.*, CATEGORIA.CATEGORIA_NOME, PRODUTO_IMAGEM.IMAGEM_URL, PRODUTO_ESTOQUE.PRODUTO_QTD 
        FROM PRODUTO 
        JOIN CATEGORIA ON PRODUTO.CATEGORIA_ID = CATEGORIA.CATEGORIA_ID
        LEFT JOIN PRODUTO_IMAGEM ON PRODUTO.PRODUTO_ID = PRODUTO_IMAGEM.PRODUTO_ID
        LEFT JOIN PRODUTO_ESTOQUE ON PRODUTO.PRODUTO_ID = PRODUTO_ESTOQUE.PRODUTO_ID
        WHERE PRODUTO.PRODUTO_ATIVO = ?");
        $stmt->execute(array($status));
    } else {
        $stmt = $pdo->prepare("SELECT PRODUTO.*, CATEGORIA.CATEGORIA_NOME, PRODUTO_IMAGEM.IMAGEM_URL, PRODUTO_ESTOQUE.PRODUTO_QTD 
        FROM PRODUTO 
        JOIN CATEGORIA ON PRODUTO.CATEGORIA_ID = CATEGORIA.CATEGORIA_ID
        LEFT JOIN PRODUTO_IMAGEM ON PRODUTO.PRODUTO_ID = PRODUTO_IMAGEM.PRODUTO_ID
        LEFT JOIN PRODUTO_ESTOQUE ON PRODUTO.PRODUTO_ID = PRODUTO_ESTOQUE.PRODUTO_ID");
        $stmt->execute();
    }
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error : " . $e->getMessage() . "</p>";
}
if (isset($_SESSION['success_message'])) {
    echo "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#45AA2B] text-white px-6 py-4 rounded-lg flex item-center gap-2'>
    <img src='icons/check.svg' class='w-6'>
    <span class='mr-2'>" . $_SESSION['success_message'] . "</span> 
    </div>";

    // Remover a mensagem de sucesso da sessão
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
echo "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#FF0000] text-white px-6 py-4 rounded-lg flex item-center gap-2'>
                <img src='icons/information.svg' class='w-6'>
                <span class='mr-2'>" . $_SESSION['error_message'] . "</span> 
                </div>";
}

function formatMoney($amount){
    if ($amount === null) {
        return 'R$ 0,00';
    } else {
        return 'R$' . number_format((float)$amount, 2, ',', '.');
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Produtos</title>
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
        <section class="w-full bg-[#00AFEF] rounded-tl-lg pt-6 pb-4 px-6">
            <div class="flex items-center justify-between">
                <div class="flex gap-4">
                    <img src="icons/produtos.svg">
                    <h2 class="text-white font-bold text-3xl">Produtos</h2>
                </div>
                <a href="cadastrar_produto.php" class="text-white">
                    <button id="cadastrar" class="flex gap-2 bg-white text-[#0b7ca5] rounded-lg py-2 px-4 font-bold">
                        <img src="icons/add.svg" class="w-6 h-6">
                        Cadastrar
                    </button>
                </a>
            </div>
            <div class="mt-4">
                <form class="flex items-center justify-between" action="listar_produtos.php" method="POST" id="searchForm">
                    <div class="flex items-center">
                        <input class="p-2 rounded-l-lg outlined-none w-60" type="text" name="busca" placeholder="Buscar por nome do produto" value="<?php echo htmlspecialchars($busca); ?>" id="searchInput">
                        <button class="bg-[#0b7ca5] p-2 rounded-r-lg" type="submit">
                            <img src="icons/search.svg" class="w-6 h-6">
                        </button>
                        <button id="limparBuscar" class="hidden text-white font-bold ml-4">Limpar busca</button>
                    </div>
                    <div class="flex text-white">
                        <div class="flex text-white">
                            <?php
                            $status = isset($_POST['status']) ? $_POST['status'] : '1';
                            $ativoClass = $status === '1' ? 'bg-[#0b7ca5]' : 'bg-[#00AFEF]';
                            $inativoClass = $status === '0' ? 'bg-[#0b7ca5]' : 'bg-[#00AFEF]';
                            ?>
                            <button name="status" value="1" class="<?php echo $ativoClass; ?> p-2 px-6 rounded-l-lg shadow-lg border-2 border-white border-r-0" type="submit">Ativo</button>
                            <button name="status" value="0" class="<?php echo $inativoClass; ?> p-2  px-6 rounded-r-lg border-2 border-white border-l-0" type="submit">Inativo</button>
                        </div>
                </form>
            </div>
            <div id="table-container" class="max-w-[1000px] flex justify-center max-h-[560px] overflow-auto m-auto ">
                <?php if (!empty($produtos)) : ?>
                <table class="">
                    <thead>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Categoria</th>
                        <th>Ativo</th>
                        <th>Desconto</th>
                        <th>Estoque</th>
                        <th>Imagem</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <?php foreach ($produtos as $produto) :  ?>
                        <tr>
                            <td><?php echo $produto['PRODUTO_ID']; ?></td>
                            <td><?php echo $produto['PRODUTO_NOME']; ?></td>
                            <td><?php echo $produto['PRODUTO_DESC']; ?></td>
                            <td><?php

                                echo formatMoney($produto['PRODUTO_PRECO']);
                                ?></td>
                            <td><?php echo $produto['CATEGORIA_NOME']; ?></td>
                            <td><div class="rounded-lg m-auto text-white w-20" style="background-color: <?php echo $produto['PRODUTO_ATIVO'] ? '#109010' : '#D90000'; ?>;">
                                        <?php echo $produto['PRODUTO_ATIVO'] ? "Ativo" : "Inativo"; ?>
                            </div></td>
                            <td><?php echo formatMoney($produto['PRODUTO_DESCONTO']); ?></td>
                            <td><?php echo $produto['PRODUTO_QTD']; ?></td>
                            <td><img src="<?php echo $produto['IMAGEM_URL'] ?>" alt="<?php echo $produto['PRODUTO_NOME']; ?>" width="50"></td>
                             <td>
                                <div class="flex items-center justify-center gap-3 w-20">
                                    <a class="mr-2" href="editar_produto.php?id=<?php echo $produto['PRODUTO_ID']; ?>" >
                                        <button>
                                            <img src="icons/edit.svg" />
                                        </button>
                                    </a>
                                    <button onclick="openConfirmDelete(<?php echo $produto['PRODUTO_ID']; ?>)" id="delete">
                                        <img src="icons/trash.svg"/>
                                    </button>
                                    </div>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                  <?php else : ?>
                <div class="flex flex-col items-center mt-16">
                    <h3 class="font-bold text-white text-lg">Nenhum produto encontrado</h3>
                    <img src="imagens/empty-produtos.svg" class="w-[450px]">
                </div>
                <?php endif; ?>
            </div>
        </section>
        <div id="dialogContainerDelete" class="fixed inset-0 flex items-center justify-center hidden">
            <div id="overlay" class="fixed inset-0 bg-black opacity-50"></div>
            <dialog id="confirmDelete" class="bg-white border-2 border-[#0b7ca5] drop-shadow-lg w-fit font-bold p-6 flex flex-col justify-between gap-6 rounded-lg">
                <form action="excluir_admin.php" method="get">
                    <input type="hidden" name="id" id="id">
                    <div class="flex gap-2 items-center">
                        <img src="icons/info.svg" class="w-7">
                        <h3 class="font-extrabold text-[#0b7ca5]">Tem certeza que deseja excluir este produto?</h3>
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
        </div>
    </main>
</body>

</html>
<script src="index.js"></script>
<script>
function confirmDelete() {
    var id = document.getElementById('id').value;
    window.location.href = "excluir_produto.php?id=" + id;
}</script>
