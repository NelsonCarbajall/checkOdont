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

$status = '';
$message = '';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $cpf = $_POST['cpf'] ?? '';

    if (empty($cpf)) {
        $status = 'danger';
        $message = 'CPF não enviado.';
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
                $status = 'success';
                $message = 'Olá, <strong>' . $paciente['nome'] . '</strong>! Seu check-in foi registrado para hoje às <strong>' . substr($consulta['hora_consulta'], 0, 5) . '</strong>.';
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Check-in</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    .background {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background-color: #fff;
      padding: 40px 60px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
      text-align: center;
      max-width: 500px;
      width: 100%;
      animation: fadeIn 1.5s ease-in-out;
    }

    h1 {
      font-size: 32px;
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
    }

    .message {
      font-size: 16px;
      padding: 20px;
      margin: 20px 0;
      border-radius: 6px;
      line-height: 1.5;
    }

    .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

    .btn-voltar {
      margin-top: 10px;
      padding: 12px 20px;
      background-color: #2575fc;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 600;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .btn-voltar:hover {
      background-color: #6a11cb;
    }

    @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      .container {
        padding: 30px 40px;
      }
      h1 {
        font-size: 26px;
      }
    }
  </style>
</head>
<body>
  <div class="background">
    <div class="container">
      <h1>Resultado do Check-in</h1>
      <div class="message <?= $status ?>">
        <?= $message ?>
      </div>
      <a href="index.html" class="btn-voltar">Cancelar</a>
      <a href="index.html" class="btn-voltar">Voltar</a>
    </div>
  </div>
</body>
</html>
