<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('./imagens/paisagem1.png');
            background-size: cover;
            background-repeat: no-repeat;
            overflow-x: hidden;
        }

        input[type="text"],
        input[type="password"] {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            background-color: #00AFEF;
            padding: 20px !important;
            height: 46px;
            width: 300px;
        }

        .icon {
            border: 2px solid #00AFEF;
            background-color: white;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            height: 46px;
            width: 50px;
            display: flex;
            justify-content: center;
        }

        input::placeholder {
            color: white !important;
            font-weight: 500;
        }

        input:focus,
        input:focus-visible {
            outline: none;
        }
    </style>
</head>
<body>
    <section class="flex h-screen w-screen justify-center m-auto my-20 mb-40 max-h-[600px]">
        <div class="bg-[#00AFEF] flex items-center w-[480px] rounded-lg rounded-tr-none rounded-br-none">
            <div class="bg-white rounded-tl-lg rounded-tr-[30px] rounded-bl-[30px] rounded-br-lg max-w-96 m-auto h-80">
                <img src="imagens/logo.webp" class="-mt-4">
            </div>
        </div>
        <div class="bg-white/80  border-2 border-[#00AFEF] p-6 flex flex-col justify-center gap-4 rounded-lg rounded-tl-none rounded-bl-none items-center  w-[480px]">
            <img src="icons/user-login.svg" class="w-44">
            <form action="processa_login.php" method="post" class="flex flex-col gap-5 mt-10 items-center">
                <div class="flex">
                    <input type="text" id="nome" name="nome" placeholder="usuário" required>
                    <div class="icon">
                        <img src="icons/user.svg">
                    </div>
                </div>
                <div class="flex">
                    <input type="password" id="senha" name="senha" placeholder="senha" required>
                    <div class="icon">
                        <img src="icons/key.svg" class="w-7">
                    </div>
                </div>
                <input class="px-20 py-2 rounded-xl bg-[#0B7CA5] drop-shadow-xl mt-4 flex justify-center items-center font-bold text-white cursor-pointer" type="submit" value="Entrar">
                <span class="font-semibold cursor-pointer">Redefinir a senha</span>

                <?php
                if (isset($_GET['erro'])) {
                    echo '<p style="color:red;">Nome de usuário e/ou senha incorreta!</p>';
                }
                ?>
            </form>
        </div>
    </section>
</body>
</html>
