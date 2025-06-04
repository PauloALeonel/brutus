<?php

session_start();

// Dados de conexão
$host = 'localhost'; // Ou o IP do servidor de banco
$dbname = 'brutus'; // Nome do seu banco de dados
$username = 'root'; // Usuário do banco
$password = '';   // Senha do banco

try {
    // Criando conexão usando PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configura o PDO para lançar exceções em caso de erro
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Exibe o erro e encerra
    echo 'Erro na conexão: ' . $e->getMessage();
    exit;
}



// Verifica se o usuário está logado
if (!isset($_SESSION['id_logado'])) {
    header('Location: ../login/login.php');
    exit;
}

$usuario_id = $_SESSION['id_logado'];

try {
    // Inicia a transação
    $conn->beginTransaction();

    // 1️⃣ Exclui os endereços vinculados ao usuário
    $sql_enderecos = "DELETE FROM endereco WHERE fk_Usuario_codigo = :usuario_id";
    $stmt_endereco = $conn->prepare($sql_enderecos);
    $stmt_endereco->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_endereco->execute();

    // 2️⃣ Exclui o usuário
    $sql_usuario = "DELETE FROM usuario WHERE codigo = :usuario_id";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_usuario->execute();

    // Confirma a transação
    $conn->commit();

    // Destrói a sessão e redireciona para a página inicial ou de login
    session_destroy();
    header('Location: ../login/login.php');

} catch (Exception $e) {
    // Em caso de erro, desfaz a transação
    $conn->rollBack();
    echo "Erro ao excluir o usuário: " . $e->getMessage();
}
?>
