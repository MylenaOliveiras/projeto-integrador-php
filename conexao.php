<?php

$host = 'localhost';
$db = 'Echo';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host; dbname=$db;charset=$charset";

try{
$pdo = new PDO($dsn, $user, $pass);
    echo "<script>console.log('Conexão com o banco de dados realizada com sucesso');</script>";
} catch(PDOException $e){
    echo "<script>console.log('Erro ao tentar conectar com o banco de dados');</script>" . $e;
}
?>
