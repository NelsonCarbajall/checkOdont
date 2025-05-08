<?php
session_start();

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

$status = '';
$message = '';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $cpf = $_POST['cpf'] ?? '';

    if (empty($cpf)) {
        $status = 'danger';
        $message = 'CPF não enviado.';
    } elseif (!preg_match('/^\d{11}$/', $cpf)) {
        $status = 'danger';
        $message = 'CPF inválido. Informe apenas os 11 dígitos numéricos.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nome FROM pacientes WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $paciente = $stmt->fetch();

        if (!$paciente) {
            $status = 'danger';
            $message = 'Paciente não encontrado.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM consultas WHERE paciente_id = ? AND data_consulta = CURDATE()");
            $stmt->execute([$paciente['id']]);
            $consulta = $stmt->fetch();

            if ($consulta) {
                // Atualiza o status da consulta
                $pdo->prepare("UPDATE consultas SET status = 'confirmada' WHERE id = ?")->execute([$consulta['id']]);

                $status = 'success';
                $hora = date('H:i', strtotime($consulta['hora_consulta']));
                $message = 'Olá, <strong>' . $paciente['nome'] . '</strong>! Seu check-in foi registrado para hoje às <strong>' . $hora . '</strong>.';
            } else {
                $status = 'warning';
                $message = 'Você não possui consulta agendada para hoje.';
            }
        }
    }
} catch (PDOException $e) {
    $status = 'danger';
    $message = 'Erro na conexão com o banco de dados.';
}

// Armazena resultado na sessão
$_SESSION['checkin_status'] = $status;
$_SESSION['checkin_message'] = $message;

// Redireciona para página de exibição
header('Location: checkin_result.php');
exit;
?>
