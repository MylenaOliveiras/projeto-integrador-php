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
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;


    try{
        $sql = "INSERT INTO ADMINISTRADOR 
        (ADM_NOME, ADM_EMAIL, ADM_SENHA, ADM_ATIVO) 
        VALUES (:nome, :email, :senha, :ativo)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_STR);
        $stmt->execute();

        $adm_id = $pdo->lastInsertId();

           
    $_SESSION['success_message_admin'] = "Admin adicionado com sucesso.";

    header('Location: listar_admin.php');
    exit();

    }catch (PDOException $e) {

        echo  "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#cc1b1b] text-white px-6 py-4 rounded-lg flex gap-2'>
        <img src='icons/info.svg'>
        <span class='mr-2'>Erro ao cadastrar o administrador</span> 
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
    <title>Cadastro do Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
    <style>
        
        input[type="text"],
        input[type="password"],
        input[type="email"]{
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            background-color: white;
            padding: 20px !important;
            height: 50px;
            width: 100%;
            border: 2px solid #0b7ca5;
            border-right: none;
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
        <h2 class="text-white font-bold text-3xl">Cadastrar novo administrador</h2>
        </div>
    <div class="w-[612px] mt-10">
    <form class="flex flex-col gap-6" action="" method="post" enctype="multipart/form-data">
        <div class="flex">
        <input type="text" name="nome" id="nome" required  placeholder="nome do adm">
        <div class="icon">
            <img src="icons/user.svg">
        </div>
        </div>
        <div class="flex">
         <input type="email" name="email" id="email" required placeholder="email do adm">
        <div class="icon">
            <img src="icons/email.svg" class="w-7">
        </div>
    </div>
        <div class="flex">
         <input type="password" name="senha" id="senha" required placeholder="senha">
        <div class="icon">
            <img src="icons/key.svg" class="w-7">
        </div>
    </div>
        <div class="flex items-center justify-between">
            <label for="ativo" class="text-white">Administrador ativo?</label>
            <label class="switch">
                <input type="checkbox" name="ativo" id="ativo" value="1" checked>
                <span class="slider round"></span>
            </label>
        </div>
        <input type="submit" value="Cadastrar" class="bg-[#0b7ca5] px-3 py-2 text-white rounded-md hover:bg-[#0b7ca5]/60">
    </form>
    </div>
    </section>
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

    