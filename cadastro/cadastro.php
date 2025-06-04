<?php
// Inicie a sessão no início do script
session_start();

$servername = "localhost";
$database = "brutus";
$username = "root";
$password = ""; 

$conn = new mysqli($servername, $username, $password, $database);

// Verifique a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["btnCadastrar"])) {
    // Coletar dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $senha = md5($_POST['senha']); // Criar o hash MD5 antes da query
    
    $bairro = $_POST['bairro'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $cep = $_POST['cep'];
    $complemento = $_POST['complemento'];
    
    // Usar prepared statements para evitar SQL injection
    // Inserir usuário
    $stmt = $conn->prepare("INSERT INTO usuario (nome, email, cpf, telefone, senha, fk_tipos_usuario_codigo) 
                           VALUES (?, ?, ?, ?, ?, '2')");
    $stmt->bind_param("sssss", $nome, $email, $cpf, $telefone, $senha);
    
    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;
        $stmt->close();
        
        // Inserir endereço
        $stmt2 = $conn->prepare("INSERT INTO endereco (cep, rua, bairro, numero, complemento, cidade, fk_Usuario_codigo) 
                                VALUES (?, ?, ?, ?, ?, 'Ourinhos', ?)");
        $stmt2->bind_param("sssssi", $cep, $rua, $bairro, $numero, $complemento, $id_usuario);
        
        if ($stmt2->execute()) {
            // Salvar o ID do usuário na sessão (não do endereço)
            $_SESSION['id_logado'] = $id_usuario;
            
            // Redirecionar
            header('Location: /brutus/index.php');
            exit(); // Importante para garantir que o script pare aqui
        } else {
            echo "Erro ao cadastrar endereço: " . $stmt2->error;
        }
        $stmt2->close();
    } else {
        echo "Erro ao cadastrar usuário: " . $stmt->error;
    }
}

$conn->close();
?>