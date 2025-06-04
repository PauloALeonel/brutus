<?php
// Script para excluir um endereço
session_start();

// Inicializa variáveis para evitar erros
$erros = [];

// Conexão com o banco de dados
$host = "localhost"; 
$database = "brutus"; 
$username = "root"; 
$password = ""; 

try {
    // Usando PDO para conexão com o banco de dados
    $conn = new PDO("mysql:host=$host;dbname=" . $database, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verifica se o usuário está logado
    if (!isset($_SESSION['id_logado']) || empty($_SESSION['id_logado'])) {
        // Redireciona para a página de login se não estiver logado
        header("Location: login.php");
        exit;
    }
    
    // Obtém o ID do usuário logado
    $usuario_id = $_SESSION['id_logado'];
    
    // Verifica se o ID do endereço foi passado
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['erro'] = "ID do endereço não informado.";
        header("Location: perfil.php#meus-enderecos");
        exit;
    }
    
    $endereco_id = intval($_GET['id']);
    
    // Verifica se o endereço pertence ao usuário
    $query_check = "SELECT cod_endereco FROM endereco WHERE cod_endereco = :id AND fk_Usuario_codigo  = :usuario_id";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bindParam(':id', $endereco_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_check->execute();
    
    if ($stmt_check->rowCount() == 0) {
        $_SESSION['erro'] = "Endereço não encontrado ou não pertence ao usuário.";
        header("Location: perfil.php#meus-enderecos");
        exit;
    }
    
    $endereco = $stmt_check->fetch(PDO::FETCH_ASSOC);
    $era_principal = $endereco['principal'] == 1;
    
    // Exclui o endereço
    $query_delete = "DELETE FROM endereco WHERE cod_endereco = :id AND fk_Usuario_codigo  = :usuario_id";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bindParam(':id', $endereco_id, PDO::PARAM_INT);
    $stmt_delete->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    
    if ($stmt_delete->execute()) {
        
        $_SESSION['sucesso'] = "Endereço excluído com sucesso.";
    } else {
        $_SESSION['erro'] = "Erro ao excluir o endereço.";
    }
    
    header("Location: perfil.php#meus-enderecos");
    exit;
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro de conexão: " . $e->getMessage();
    header("Location: perfil.php#meus-enderecos");
    exit;
}
?>