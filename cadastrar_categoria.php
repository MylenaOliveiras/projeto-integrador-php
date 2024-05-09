<?php
//criar no banco de dados uma nova tabela com 6 colunas
session_start();
require_once('conexao.php');

if(!isset($_SESSION['admin_logado'])){ //se nao existeir um adm logado, vamos direcionar ele para pagina de login
    header('Location:login.php');
    exit(); 
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){   //$_SERVER['REQUEST_METHOD'] retorna o metodo usado para acessar a pagina

    //criar um formulario com os nomes dessas variaveis
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $ativo = isset($_POST['ativo']) ? 1:0;
   

    try{
        $sql = "INSERT INTO CATEGORIA 
        (CATEGORIA_NOME, CATEGORIA_DESC, CATEGORIA_ATIVO) 
        VALUES (:nome, :descricao, :ativo)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_STR);
        $stmt->execute();
        
        //pegando o id do ultimo produto inserido
        $produto_id = $pdo->lastInsertID();
        
        $_SESSION['success_message_categoria'] = "Categoria adicionada com sucesso.";
        header('Location: listar_categoria.php');
        exit();

    }catch (PDOException $e) {
          echo  "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#cc1b1b] text-white px-6 py-4 rounded-lg flex gap-2'>
        <img src='icons/info.svg'>
        <span class='mr-2'>Erro ao cadastrar o categoria</span> 
        " . $e->getMessage() . "
        </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria</title>
<script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
    <style>
        
        input, textarea{
            border-radius: 10px;
            background-color: white;
            padding: 20px !important;
            height: 50px;
            width: 100%;
            border: 2px solid #0b7ca5;
        }

        input[type="submit"] {
            padding: 0 !important;
        }

        textarea{
            height: 100px;
        }

        .icon {
            border: 2px solid #0b7ca5;
            background-color: white;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            height: 50px;
            width: 50px;
            display: flex;
            justify-content: center;
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
                <div class="bg-white rounded-full p-px">
                <img src="icons/add.svg" class='w-6'>
                </div>
        <h2 class="text-white font-bold text-3xl">Cadastrar nova categoria</h2>
        </div>
    <div class="w-[612px] mt-10">

    <form action="" method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
        <input type="text" name="nome" id="nome" required placeholder="nome da categoria">
        <textarea name="descricao" id="descricao" required placeholder="descrição"></textarea>
 <div class="flex items-center justify-between">
            <label for="ativo" class="text-white">Categoria ativa?</label>
            <label class="switch">
                <input type="checkbox" name="ativo" id="ativo" value="1" checked>
                <span class="slider round"></span>
            </label>
        </div>
                <input type="submit" value="Cadastrar" class="bg-[#0b7ca5] px-3 py-2 text-white rounded-md hover:bg-[#0b7ca5]/60">

        </form>
       </div>
    </section>
     
    </main>
</body>
</html>
<script src="./index.js"></script>
