<?php
// Parâmetros de conexão com o banco
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

// Inicializa mensagem
$status = '';
$message = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Recebe dados do formulário
        $cpf = $_POST['cpf'] ?? '';
        $data_consulta = $_POST['data_consulta'] ?? '';
        $hora_consulta = $_POST['hora_consulta'] ?? '';

        if (empty($cpf) || empty($data_consulta) || empty($hora_consulta)) {
            $status = 'danger';
            $message = 'Por favor, preencha todos os campos.';
        } else {
            // Verifica se o paciente existe
            $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE cpf = ?");
            $stmt->execute([$cpf]);
            $paciente = $stmt->fetch();

            if (!$paciente) {
                $status = 'danger';
                $message = 'Paciente não encontrado.';
            } else {
                // Insere consulta no banco
                $stmt = $pdo->prepare("INSERT INTO consultas (paciente_id, data_consulta, hora_consulta, status) VALUES (?, ?, ?, 'agendada')");
                $stmt->execute([$paciente['id'], $data_consulta, $hora_consulta]);

                $status = 'success';
                $message = 'Consulta cadastrada com sucesso!';
            }
        }
    } catch (PDOException $e) {
        $status = 'danger';
        $message = 'Erro na conexão com o banco de dados.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Consulta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
      opacity: 0; /* Começa invisível */
      animation: fadeIn 1.5s ease-in-out forwards; /* Aplica a animação */
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
    input:focus {
    border-color: #2575fc;
    outline: none;
    box-shadow: 0 0 5px rgba(37, 117, 252, 0.5);
}

    .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    .btn-voltar {
      margin-top: 10px;
      padding: 12px 20px;
      background-color: #2575fc;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 600;
      display: inline-block;
    }

    .btn-voltar:hover {
      background-color: #6a11cb;
    }
    input{
        border: 2px solid #ddd;
    }

    label {
      font-size: 14px;
      font-weight: 600;
      color: #444;
      margin-bottom: 10px;
      text-align: left;
      display: block;
    }

    input, button {
      width: 100%;
      padding: 14px;
      margin-bottom: 15px;
      border-radius: 6px;
      font-size: 16px;
    }

    button {
      background-color: #2575fc;
      color: white;
      border: none;
      font-weight: 600;
    }

    button:hover {
      background-color: #6a11cb;
    }

    /* Animação de fade-in */
    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="background">
    <div class="container">
      <h1>Cadastro de Consulta</h1>
      
      <?php if ($message): ?>
        <div class="message <?= $status ?>">
          <?= $message ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="cadastro_consulta.php">
        <label for="cpf">CPF do Paciente</label>
        <input type="text" name="cpf" id="cpf" required placeholder="Digite o CPF" />

        <label for="data_consulta">Data da Consulta</label>
        <input type="date" name="data_consulta" id="data_consulta" required />

        <label for="hora_consulta">Hora da Consulta</label>
        <input type="time" name="hora_consulta" id="hora_consulta" required />

        <button type="submit">Cadastrar Consulta</button>
      </form>
      
      <a href="index.html" class="btn-voltar">Voltar</a>
    </div>
  </div>
</body>
</html>
