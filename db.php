<?php
$servername = "localhost";
$username = "root";  // Usuário do banco de dados
$password = "";      // Senha do banco de dados
$dbname = "clinica"; // Nome do banco de dados

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
