<?php
// Conexão com o banco
$host = 'localhost';
$db = 'clinica';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    header("Location: cadastro_paciente.html?error=1");
    exit;
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email = $_POST['email'] ?? '';

    // Verifica se CPF já existe
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $existe = $stmt->fetch();

    if ($existe) {
        header("Location: cadastro_paciente.html?error=cpf_duplicado");
        exit;
    }

    // Inserção
    try {
        $stmt = $pdo->prepare("INSERT INTO pacientes (nome, cpf, telefone, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $cpf, $telefone, $email]);
        header("Location: cadastro_paciente.html?success=1");
    } catch (PDOException $e) {
        header("Location: cadastro_paciente.html?error=1");
    }
    exit;
}
?>
