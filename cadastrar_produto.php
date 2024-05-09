<?php
session_start();
require_once('conexao.php');

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login.php');
    exit();
}

// Verifica se há dados salvos do formulário de produto na sessão
if (isset($_SESSION['produto_nome'])) {
    $produto_nome = $_SESSION['produto_nome'];
    $produto_descricao = $_SESSION['produto_descricao'];
    $produto_preco = $_SESSION['produto_preco'];
    $produto_desconto = $_SESSION['produto_desconto'];
    $categoria_id = $_SESSION['categoria_id'];
} else {
    // Inicializa as variáveis de formulário com valores padrão
    $produto_nome = '';
    $produto_descricao = '';
    $produto_preco = '';
    $produto_desconto = '';
    $categoria_id = '';
}

try {
    $stmt_categoria = $pdo->prepare("SELECT * FROM CATEGORIA");
    $stmt_categoria->execute();
    $categorias = $stmt_categoria->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'> Erro ao buscar categorias: " . $e->getMessage() . "</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_type'])) {
    $form_type = $_POST['form_type'];

    if ($form_type == 'produto') {

        // Salva os valores do formulário de produto na sessão
       $_SESSION['produto_nome'] = $_POST['nome'];
        $_SESSION['produto_descricao'] = $_POST['descricao'];
        $_SESSION['produto_preco'] = $_POST['preco'];
        $_SESSION['produto_desconto'] = $_POST['desconto'];
        $_SESSION['categoria_id'] = $_POST['categoria_id'];

        // Inserção do produto no banco de dados
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $preco = $_POST['preco'];
        $desconto = $_POST['desconto'];
        $categoria_id = $_POST['categoria_id'];
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        try {
            $sql = "INSERT INTO PRODUTO 
            (PRODUTO_NOME, PRODUTO_DESC, PRODUTO_PRECO, PRODUTO_DESCONTO, CATEGORIA_ID, PRODUTO_ATIVO) 
            VALUES (:nome, :descricao, :preco, :desconto, :categoria_id, :ativo)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
            $stmt->bindParam(':desconto', $desconto, PDO::PARAM_STR);
            $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_STR);
            $stmt->bindParam(':ativo', $ativo, PDO::PARAM_STR);
            $stmt->execute();

            $_SESSION['success_message'] = "Produto adicionado com sucesso.";
            header('Location: listar_produtos.php');
            exit();
        } catch (PDOException $e) {

            $_SESSION['error_message'] = "Erro ao cadastrar o produto: " . $e->getMessage();
            header('Location: listar_produtos.php');
            exit();
        }
    } elseif ($form_type == 'categoria') {
        $nome_categoria = $_POST['nome_categoria'];
        $descricao_categoria = $_POST['descricao_categoria'];
        $ativo_categoria = isset($_POST['ativo_categoria']) ? 1 : 0;

        try {
            $sql = "INSERT INTO CATEGORIA 
            (CATEGORIA_NOME, CATEGORIA_DESC, CATEGORIA_ATIVO) 
            VALUES (:nome, :descricao, :ativo)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome_categoria, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao_categoria, PDO::PARAM_STR);
            $stmt->bindParam(':ativo', $ativo_categoria, PDO::PARAM_STR);
            $stmt->execute();

            $produto_nome = $_SESSION['produto_nome'];
            $produto_descricao = $_SESSION['produto_descricao'];
            $produto_preco = $_SESSION['produto_preco'];
            $produto_desconto = $_SESSION['produto_desconto'];
            $categoria_id = $_SESSION['categoria_id'];

                echo "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#45AA2B] text-white px-6 py-4 rounded-lg flex item-center gap-2'>
                <img src='icons/check.svg' class='w-6'>
                <span class='mr-2'> Categoria adicionada com sucesso</span> 
                </div>";

        } catch (PDOException $e) {
            echo "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#FF0000] text-white px-6 py-4 rounded-lg flex item-center gap-2'>
                <img src='icons/check.svg' class='w-6'>
                <span class='mr-2'> Erro ao cadastrar a categoria: " . $e->getMessage() . "</span> 
                </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro do Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        <script>
        //Adiciona um novo campo de imagem URL
        function adicionarImagem() {

            const novoDiv = document.createElement("div");
            novoDiv.className = 'imagem-input grid grid-cols-2 gap-4';
            
            const novoInputURL = document.createElement("input");
            novoInputURL.type = "text";
            novoInputURL.name = "imagem_url[]";
            novoInputURL.placeholder = "URL da imagem";
            novoInputURL.required="true";
            novoInputURL.id = "imagem_url";

            const novoInputOrdem = document.createElement("input");
            novoInputOrdem.type = "number";
            novoInputOrdem.name = "imagem_ordem[]";
            novoInputOrdem.id = "imagem_ordem";
            novoInputOrdem.placeholder = "ordem";
            novoInputOrdem.required="true";
            novoInputOrdem.min = '1';
            novoDiv.appendChild(novoInputURL);
            novoDiv.appendChild(novoInputOrdem);
            containerImagens.appendChild(novoDiv);
        }
    </script>
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
                <div class="bg-white rounded-full p-px">
                <img src="icons/add.svg" class='w-6'>
                </div>
        <h2 class="text-white font-bold text-3xl">Cadastrar novo produto</h2>
        </div>
    <div class="w-[612px] mt-10">
    <form action="" method="post" enctype="multipart/form-data" id="form-produto">
        <input type="hidden" name="form_type" value="produto">
        <div class="grid grid-cols-2 gap-4"> 
            <input type="text" name="nome" id="nome" required placeholder="nome do produto" class="col-span-1" onkeyup="armazenarValoresProduto()">
            <textarea name="descricao" id="descricao" required placeholder="descrição" class="col-start-2 row-span-2" onchange="armazenarValoresProduto()"></textarea>
            <input type="number" name="preco" id="preco" step="0.01" required placeholder="preço" class="col-span-1" onchange="armazenarValoresProduto()"> 
            <input type="number" name="desconto" id="desconto" step="0.01" placeholder="desconto" onchange="armazenarValoresProduto()">
            <input type="number" name="produto_qtd[]" id="produto_qtd" required placeholder="quantidade" onchange="armazenarValoresProduto()">
        </div>
       <div class="flex items-center justify-between my-4">
            <label for="ativo" class="text-white">Produto ativo?</label>
            <label class="switch">
                <input type="checkbox" name="ativo" id="ativo" value="1" checked>
                <span class="slider round"></span>
            </label>
        </div>
        <div>
            <h3 class="text-white font-bold text-lg mb-2">Categoria</h3>
            <div class="flex items-center gap-5">
                <select name="categoria_id" id="categoria_id" required placeholder="categoria" value="1">
                    <?php
                    foreach ($categorias as $categoria) {
                        echo "<option value='{$categoria['CATEGORIA_ID']}'> {$categoria['CATEGORIA_NOME']} </option>";
                    }
                    ?>
                </select>
                <button id="addCategoriaBnt" class="p-3 bg-[#0b7ca5] text-white rounded-lg w-64" type="button" onclick="armazenarValoresProduto()">Cadastrar categoria</button>
            </div>
        </div>
        <div class="mt-5">
         <h3 class="text-white font-bold text-lg mb-2">Imagem</h3>

        <div id="containerImagens" class="flex flex-col gap-4 overflow-auto max-h-[124px]">
            <div class="grid grid-cols-2 gap-4">
            <input type="text" name="imagem_url[]" placeholder="URL imagem" required id="imagem_url">
            <input type="number" name="imagem_ordem[]" placeholder="ordem" min="1" required id="imagem_ordem">
            </div>
        </div>
        <button type="button" onclick="adicionarImagem()" class="mt-4">Adicionar mais imagens</button>
        </div>
        <button type="submit" class="bg-[#0b7ca5] px-3 py-2 text-white rounded-md hover:bg-[#0b7ca5]/60 w-full mt-6">Cadastrar Produto</button>
    </form>
    </div>
    </section>
     <div id="dialogAddCategoria" class="fixed inset-0 flex items-center justify-center hidden">
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 "></div>
            <dialog id="addCategoria" class="bg-white border-2 border-[#0b7ca5] drop-shadow-lg w-96 font-bold p-6 flex flex-col justify-between gap-6 rounded-lg w-[600px]">
                <h3 class="text-lg font-bold">Cadastrar nova categoria</h3>
                    <form action="" method="post" enctype="multipart/form-data" class="flex flex-col gap-4" id="form_categoria">
            <input type="hidden" name="form_type" value="categoria">
        <input type="text" name="nome_categoria" id="nome_categoria" required placeholder="nome da categoria">
        <textarea name="descricao_categoria" id="descricao_categoria" required placeholder="descrição"></textarea>
 <div class="flex items-center justify-between">
            <label for="ativo" class="text-[#0b7ca5]">Categoria ativa?</label>
            <label class="switch">
                <input type="checkbox" name="ativo_categoria" id="ativo" value="1" checked>
                <span class="slider round"></span>
            </label>
        </div>
                <input type="submit" value="Cadastrar" class="bg-[#0b7ca5] !px-3 !py-2 text-white rounded-md hover:bg-[#0b7ca5]/60">

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
<script src="./index.js"></script>
<script>
    const dialogAddCategoria = document.getElementById("dialogAddCategoria");
    const addCategoria = document.getElementById("addCategoria");
    const buttonAddCategoria = document.getElementById("addCategoriaBnt");

    buttonAddCategoria.addEventListener("click", function () {
  addCategoria.open = true;
  dialogAddCategoria.classList.remove("hidden");
});

    function armazenarValoresProduto() {
        localStorage.setItem('produto_nome', document.getElementById('nome').value);
        localStorage.setItem('produto_descricao', document.getElementById('descricao').value);
        localStorage.setItem('produto_preco', document.getElementById('preco').value);
        localStorage.setItem('produto_desconto', document.getElementById('desconto').value);
        localStorage.setItem('categoria_id', document.getElementById('categoria_id').value);
        localStorage.setItem('produto_qtd', document.getElementById('produto_qtd').value);
        localStorage.setItem('imagem_url', document.getElementById('imagem_url').value);
        localStorage.setItem('imagem_ordem', document.getElementById('imagem_ordem').value);
    }

    function restaurarValoresProduto() {
        document.getElementById('nome').value = localStorage.getItem('produto_nome') || '';
        document.getElementById('descricao').value = localStorage.getItem('produto_descricao') || '';
        document.getElementById('preco').value = localStorage.getItem('produto_preco') || '';
        document.getElementById('desconto').value = localStorage.getItem('produto_desconto') || '';
        document.getElementById('categoria_id').value = localStorage.getItem('categoria_id') || '';
        document.getElementById('produto_qtd').value = localStorage.getItem('produto_qtd') || '';
        document.getElementById('imagem_url').value = localStorage.getItem('imagem_url') || '';
        document.getElementById('imagem_ordem').value = localStorage.getItem('imagem_ordem') || '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        restaurarValoresProduto(); 
        const formCategoria = document.getElementById('form_categoria');
        formCategoria.addEventListener('submit', function (event) {
            event.preventDefault();
            formCategoria.submit();
        });
    });

 var categoriaSubmetida = false;

document.getElementById('form-produto').addEventListener('submit', function() {
    localStorage.removeItem('produto_nome');
    localStorage.removeItem('produto_descricao');
    localStorage.removeItem('produto_preco');
    localStorage.removeItem('produto_desconto');
    localStorage.removeItem('categoria_id');
    localStorage.removeItem('produto_qtd');
    localStorage.removeItem('imagem_url');
    localStorage.removeItem('imagem_ordem');

});

document.querySelector('#form_categoria').addEventListener('submit', function() {
    categoriaSubmetida = true;
});

window.addEventListener('unload', function() {
    if (!categoriaSubmetida) {
        localStorage.removeItem('produto_nome');
        localStorage.removeItem('produto_descricao');
        localStorage.removeItem('produto_preco');
        localStorage.removeItem('produto_desconto');
        localStorage.removeItem('categoria_id');
localStorage.removeItem('produto_qtd');
    localStorage.removeItem('imagem_url');
    localStorage.removeItem('imagem_ordem');
    }
});

</script>
