
<?php
//uma sessão é iniciada e verifica-se se um administrador está logado. Se não estiver, ele é redirecionado para a página de login.
session_start();

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login.php');
    exit();
}

//o script faz uma conexão com o banco de dados, usando os detalhes de configuração especificados em conexao.php
require_once('conexao.php');

//busca categoria
try{
    $stmt_categoria = $pdo->prepare("SELECT * FROM CATEGORIA");
    $stmt_categoria ->execute();
    $categorias = $stmt_categoria->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    echo "<p style='color:red;'> Erro ao buscar categorias:" .$e->getMessage()."</p>";
}

// Se a página foi acessada via método GET, o script tenta recuperar os detalhes do produto com base no ID passado na URL.
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        try {
            // Consulta para recuperar os detalhes do produto
            $stmt = $pdo->prepare("SELECT PRODUTO.*, CATEGORIA.CATEGORIA_NOME, PRODUTO_IMAGEM.IMAGEM_URL, PRODUTO_IMAGEM.IMAGEM_ORDEM, PRODUTO_ESTOQUE.PRODUTO_QTD 
            FROM PRODUTO 
            INNER JOIN CATEGORIA ON PRODUTO.CATEGORIA_ID = CATEGORIA.CATEGORIA_ID
            LEFT JOIN PRODUTO_IMAGEM ON PRODUTO.PRODUTO_ID = PRODUTO_IMAGEM.PRODUTO_ID
            LEFT JOIN PRODUTO_ESTOQUE ON PRODUTO.PRODUTO_ID = PRODUTO_ESTOQUE.PRODUTO_ID
            WHERE PRODUTO.PRODUTO_ID = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            // Consulta para recuperar as imagens do produto específico
            $stmt_imagem = $pdo->prepare("SELECT * FROM PRODUTO_IMAGEM WHERE PRODUTO_ID = :id");
            $stmt_imagem->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_imagem->execute();
            $imagem_url = $stmt_imagem->fetchAll(PDO::FETCH_ASSOC);


        } catch (PDOException $e) {

              echo  "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#cc1b1b] text-white px-6 py-4 rounded-lg flex gap-2'>
        <img src='icons/info.svg'>
        <span class='mr-2'>Erro ao cadastrar produto</span> 
        " . $e->getMessage() . "
        </div>";
        }
    } else {
        header('Location: listar_produtos.php');
        exit();
    }
}


// Se o formulário de edição foi submetido, a página é acessada via método POST, e o script tenta atualizar os detalhes do produto no banco de dados com as informações fornecidas no formulário.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $desconto = $_POST['desconto'];
    $categoria_id = $_POST['categoria_id'];
    $produto_qtd = $_POST['qtd'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $imagem_url = $_POST['imagem_url'];
    $imagem_ordem = $_POST['imagem_ordem'];


    try {


        if (isset($_POST['imagem_url']) && isset($_POST['imagem_ordem'])) {
            foreach ($_POST['imagem_url'] as $index => $url) {
                if (!empty($url) && isset($_POST['imagem_ordem'][$index])) {
                    $ordem = $_POST['imagem_ordem'][$index];
                    $imagem_id = $_POST['imagem_id'][$index]; // Certifique-se de incluir campos ocultos com imagem_id em seu formulário
            
                    // Atualizar cada entrada de imagem
                    $stmt_imagem = $pdo->prepare("UPDATE PRODUTO_IMAGEM SET IMAGEM_ORDEM = :imagem_ordem, IMAGEM_URL = :imagem_url WHERE PRODUTO_ID = :id AND IMAGEM_ID = :imagem_id");
                    
                    $stmt_imagem->bindParam(':imagem_url', $url, PDO::PARAM_STR);
                    $stmt_imagem->bindParam(':imagem_ordem', $ordem, PDO::PARAM_INT);
                    $stmt_imagem->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt_imagem->bindParam(':imagem_id', $imagem_id, PDO::PARAM_INT);
                    $stmt_imagem->execute();
                }
            }
        }
        

        $stmtProdutoEstoque = $pdo->prepare("UPDATE PRODUTO_ESTOQUE SET PRODUTO_QTD = :qtd WHERE PRODUTO_ID = :id");
        $stmtProdutoEstoque->bindParam(':qtd',$produto_qtd,PDO::PARAM_STR);
        $stmtProdutoEstoque->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtProdutoEstoque->execute();

        $stmt = $pdo->prepare("UPDATE PRODUTO SET PRODUTO_NOME = :nome, PRODUTO_DESC = :descricao, PRODUTO_PRECO = :preco, PRODUTO_DESCONTO = :desconto, CATEGORIA_ID = :categoria_id,  PRODUTO_ATIVO = :ativo WHERE PRODUTO_ID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
        $stmt->bindParam(':desconto', $desconto, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->execute();

        
            $_SESSION['success_message'] = "Produto editado com sucesso.";
       
        header('Location: listar_produtos.php');
        exit();
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
<!-- Um formulário de edição é apresentado ao administrador, preenchido com os detalhes atuais do produto, permitindo que ele faça modificações e submeta o formulário para atualizar os detalhes do produto -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="listar_produtos.css"><script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
    <style>
        input, textarea, select{
            border-radius: 10px;
            background-color: white;
            padding: 20px !important;
            height: 50px;
            width: 100%;
            border: 2px solid #0b7ca5;
        }

        textarea {
            height: auto;
        }

        select{
            padding: 0 12px !important;
        }

        input::placeholder {
            color: #0b7ca5;
            font-weight: 700;
        }

        input:focus,
        input:focus-visible {
            outline: none;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 24px;
        }

        .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: -2px;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #0b7ca5;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #0b7ca5;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(18px);
            -ms-transform: translateX(18px);
            transform: translateX(18px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 12px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
        </style>
       
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
        <section class="w-full bg-[#00AFEF] rounded-tl-lg pt-6 pb-4 px-10 flex flex-col justify-center items-center">
            <div class="flex items-center gap-2">
        <h2 class="text-white font-bold text-3xl">Editar produto</h2>
        </div>
    <div class="w-[612px] mt-10">

        <form action="editar_produto.php" method="post">
                    <div class="grid grid-cols-2 gap-4"> 

            <input type="hidden" name="id" value="<?php echo $produto['PRODUTO_ID']; ?>" >
            <input type="text" name="nome" id="nome" value="<?php echo $produto['PRODUTO_NOME']; ?>" class="col-span-1">
            <textarea name="descricao" id="descricao" class="col-start-2 row-span-2"><?php echo $produto['PRODUTO_DESC']; ?></textarea>
            <input type="number" name="preco" id="preco" value="<?php echo $produto['PRODUTO_PRECO']; ?>" class="col-span-1">
            <input type="number" name="desconto" id="desconto" value="<?php echo $produto['PRODUTO_DESCONTO']; ?>">
        <input type="number" name="qtd" id="qtd" value="<?php echo $produto['PRODUTO_QTD']; ?>">
</div>
 <div class="flex items-center justify-between my-4">
            <label for="ativo" class="text-white">Produto ativo?</label>
            <label class="switch">
            <input type="checkbox" name="ativo" id="ativo" <?php echo ($produto['PRODUTO_ATIVO'] == 1) ? 'checked' : ''; ?>>
                <span class="slider round"></span>
            </label>
        </div>
          <div>
            <h3 class="text-white font-bold text-lg mb-2">Categoria</h3>
             <select name="categoria_id" id="categoria_id" required>
            <?php foreach($categorias as $categoria): ?>
            <option value="<?= $categoria['CATEGORIA_ID']?>">
                <?= $categoria['CATEGORIA_NOME'] ?>
            </option>
            <?php endforeach; ?>
        </select>
        </div>

         <div class="mt-5">
         <h3 class="text-white font-bold text-lg mb-2">Imagem</h3>
        <div id="containerImagens" class="flex flex-col gap-4 overflow-auto max-h-[124px]">
        <?php 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            }

        $stmt = $pdo->prepare("SELECT * FROM PRODUTO_IMAGEM WHERE PRODUTO_ID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $imagem_url = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($imagem_url as $imagem){
            echo "<div class='flex items-center gap-4'>";
            echo "<input type='hidden' name='imagem_id[]' value='{$imagem['IMAGEM_ID']}'>";
            echo "<input type='text' name='imagem_url[]' value='{$imagem['IMAGEM_URL']}' class='col-span-1'>";
            echo "<input type='text' name='imagem_ordem[]' value='{$imagem['IMAGEM_ORDEM']}' class='col-span-1'>";
            echo "</div>";
        }
        ?>

        </div>

                </div>

        
      
        
        <input type="submit" value="Atualizar Produto" class="bg-[#0b7ca5] !px-3 !py-2 text-white rounded-md hover:bg-[#0b7ca5]/60 mt-5">
        </form>    
</div>
</section>
</main>
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
</body>
</html>

<script src="./index.js"></script>
