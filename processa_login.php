<?php

session_start(); //inicia uma sessão 

require_once('conexao.php'); //puxa o arquivo de conexao 

$nome = $_POST['nome']; //puxa do arquivo de login o campo "name" > puxa do input
$senha = $_POST['senha']; 

$sql = "SELECT * FROM ADMINISTRADOR WHERE ADM_NOME = :nome AND ADM_SENHA = :senha AND ADM_ATIVO = 1"; //o placeholder nao precisa ser igual da variavel 

$query = $pdo->prepare($sql); //declaracao preparada para evitar injecao de sql, evita fraude, por isso foi criado 2 placeholders ':nome' e ':senha'

$query->bindParam(':nome',$nome, PDO::PARAM_STR); //esta ligando o placeholder 'nome' a variavel, mas antes de usar, esta verificando se veio strings no campo
$query->bindParam(':senha',$senha, PDO::PARAM_STR);

$query-> execute(); //executa o comando SQL

$admin = $query->fetch(PDO::FETCH_ASSOC);


if($query->rowCount()>0){
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_nome'] = $admin['ADM_NOME'];
    $_SESSION['admin_id'] = $admin['ADM_ID'];
    header('Location: painel_admin.php');
}else{
    header('Location: login.php?erro');
}
?>