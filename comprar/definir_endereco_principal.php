<?php
// Script para definir um endereço como principal
session_start();

include_once "conecta.php";

// Verifica se o usuário está logado
if(!isset($_SESSION['id_logado']) == true) { 
    header('location: ../login/login.php');
    exit;
}

$cliente = $_SESSION['id_logado'];
$endereco_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($endereco_id > 0) {
    // Verifica se o endereço pertence ao usuário
    $query_check = "SELECT codigo FROM endereco WHERE codigo = $endereco_id AND fk_Usuario_codigo = $cliente";
    $result_check = mysqli_query($conn, $query_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        // Primeiro, remove o status de principal de todos os endereços do usuário
        $query_reset = "UPDATE endereco SET principal = 0 WHERE fk_Usuario_codigo = $cliente";
        mysqli_query($conn, $query_reset);
        
        // Define o endereço selecionado como principal
        $query_update = "UPDATE endereco SET principal = 1 WHERE codigo = $endereco_id";
        mysqli_query($conn, $query_update);
        
        $_SESSION['sucesso'] = "Endereço definido como principal com sucesso!";
    } else {
        $_SESSION['erro'] = "Endereço não encontrado ou não pertence ao usuário.";
    }
} else {
    $_SESSION['erro'] = "ID de endereço inválido.";
}

// Redireciona de volta para a página de endereços
header('location: endereco.php?modo=selecionar');
exit;
?>
