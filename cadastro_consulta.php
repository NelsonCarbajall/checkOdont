<?php
$host = 'localhost';
$db = 'clinica';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        $cpf = $_POST['cpf'] ?? '';
        $data_consulta = $_POST['data_consulta'] ?? '';
        $hora_consulta = $_POST['hora_consulta'] ?? '';

        if (empty($cpf) || empty($data_consulta) || empty($hora_consulta)) {
            header("Location: cadastro_consulta.html?error=" . urlencode("Por favor, preencha todos os campos."));
            exit;
        }

        // Verifica se o paciente existe
        $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $paciente = $stmt->fetch();

        if (!$paciente) {
            header("Location: cadastro_consulta.html?error=" . urlencode("Paciente não encontrado."));
            exit;
        }

        // Insere consulta
        $stmt = $pdo->prepare("INSERT INTO consultas (paciente_id, data_consulta, hora_consulta, status) VALUES (?, ?, ?, 'agendada')");
        $stmt->execute([$paciente['id'], $data_consulta, $hora_consulta]);

        header("Location: cadastro_consulta.html?success=1");
        exit;

    } catch (PDOException $e) {
        header("Location: cadastro_consulta.html?error=" . urlencode("Erro na conexão com o banco de dados."));
        exit;
    }
}
