<?php
// uma sessão é iniciada e verifica-se se um administrador está logado. Se não estiver, ele é redirecionado para a página de login.
session_start();

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login.php');
    exit();
}

// o script faz uma conexão com o banco de dados, usando os detalhes de configuração especificados em conexao.php
require_once('conexao.php');

// Se a página foi acessada via método GET, o script tenta recuperar os detalhes do produto com base no ID passado na URL.
if ($_SERVER['REQUEST_METHOD'] == 'GET') { // A superglobal $_SERVER é um array que contém informações sobre cabeçalhos, caminhos e locais de scripts. O REQUEST_METHOD é um dos índices deste array e é usado para determinar qual método de requisição foi utilizado para acessar a página, seja ele GET, POST, PUT, entre outros

    if (isset($_GET['id'])) { // $_GET é uma superglobal em PHP, o que significa que ela está disponível em qualquer lugar do seu script, sem necessidade de definição ou importação global. Ela contém dados enviados através da URL (também conhecidos como parâmetros de query string). Quando um usuário acessa uma URL como http://exemplo.com/pagina.php?id=123, o valor 123 é passado para o script pagina.php através do método GET, e você pode acessá-lo com $_GET['id'].
        $id = $_GET['id'];
        try {
            $stmt = $pdo->prepare("SELECT * FROM CATEGORIA WHERE CATEGORIA_ID = :id"); // Quando você executa uma consulta SELECT no banco de dados usando PDO e utiliza o método fetch(PDO::FETCH_ASSOC), o resultado é um array associativo, onde cada chave do array é o nome de uma coluna da tabela no banco de dados, e o valor associado a essa chave é o valor correspondente daquela coluna para o registro selecionado
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // PDO::PARAM_INT especifica que o valor é um inteiro. Isso é útil para o PDO saber como tratar o valor antes de enviá-lo ao banco de dados.  Especificar o tipo de dado pode melhorar o desempenho e a segurança da sua aplicação. É uma constante da classe PDO que representa o tipo de dado inteiro para ser usado com métodos como bindParam()
            $stmt->execute();
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC); //$produto é um array associativo que contém os detalhes do produto que foi recuperado do banco de dados. Por exemplo, se a tabela de produtos tem colunas como ID, NOME, DESCRICAO, PRECO, e URL_IMAGEM, então o array $produto terá essas chaves, e você pode acessar os valores correspondentes usando a sintaxe de colchetes, 

       
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    } else {
        header('Location: listar_categoria.php');
        exit();
    }
}

// Se o formulário de edição foi submetido, a página é acessada via método POST, e o script tenta atualizar os detalhes do produto no banco de dados com as informações fornecidas no formulário.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;

        echo "Valor de ativo: " . $ativo; // Debugging

    try {
        $stmt = $pdo->prepare("UPDATE CATEGORIA SET CATEGORIA_NOME = :nome, CATEGORIA_DESC = :descricao, CATEGORIA_ATIVO = :ativo WHERE CATEGORIA_ID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->execute();

             $_SESSION['success_message_categoria'] = "Categoria alterada com sucesso.";
            header('Location: listar_categoria.php');
            exit();
    } catch (PDOException $e) {
         echo  "<div id='snackbar' class='fixed bottom-4 right-4 bg-[#cc1b1b] text-white px-6 py-4 rounded-lg flex gap-2'>
        <img src='icons/info.svg'>
        <span class='mr-2'>Erro ao editar categoria</span> 
        " . $e->getMessage() . "
        </div>";
    }
}
?>
<!-- Um formulário de edição é apresentado ao administrador, preenchido com os detalhes atuais do produto, permitindo que ele faça modificações e submeta o formulário para atualizar os detalhes do produto -->
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Editar categoria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
    <style>
        input,
        textarea {
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

        textarea {
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

        input:checked+.slider {
            background-color: #0b7ca5;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #0b7ca5;
        }

        input:checked+.slider:before {
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
                <h2 class="text-white font-bold text-3xl">Editar categoria</h2>
            <div class="w-[612px] mt-10">
                <form action="editar_categoria.php" method="post" class="flex flex-col gap-4">
                    <input type="hidden" name="id" value="<?php echo $categoria['CATEGORIA_ID']; ?>">
                <input type="text" name="nome" id="nome" value="<?php echo $categoria['CATEGORIA_NOME']; ?>"  placeholder="nome da categoria" >
                    <textarea type="text" name="descricao" id="descricao"  placeholder="descrição"><?php echo $categoria['CATEGORIA_DESC']; ?></textarea>
                     <div class="flex items-center justify-between my-4">
            <label for="ativo" class="text-white">Categoria ativa?</label>
            <label class="switch">
            <input type="checkbox" name="ativo" id="ativo" <?php echo ($categoria['CATEGORIA_ATIVO'] == 1) ? 'checked' : ''; ?>>
                <span class="slider round"></span>
            </label>
        </div>
                    <input type="submit" value="Atualizar categoria" class="bg-[#0b7ca5] !px-3 !py-2 text-white rounded-md hover:bg-[#0b7ca5]/60 mt-5">
                </form>
            </div>
        </section>
    </main>
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
</body>

</html>
<script src="index.js"></script>
