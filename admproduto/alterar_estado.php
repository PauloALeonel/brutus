<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_pedido = intval($_POST['cod_pedido']);
    $novo_status = intval($_POST['novo_status']);

    $sql = "UPDATE pedidos SET cod_status_pedidos = ? WHERE cod_pedido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $novo_status, $cod_pedido);

    if ($stmt->execute()) {
        header("Location: painel.php");
        exit();
    } else {
        echo "Erro ao atualizar status: " . $conn->error;
    }
} else {
    echo "Requisição inválida.";
}
?>
