<?php
include_once "conecta.php";
session_start();

$cliente = $_SESSION['id_logado'];

if (isset($_POST["btn_pag"])) {
    $tipo_pag = $_POST['pagamento'];
    $quant = 0;
    $total_carrinho = 0;
    $cod_status = 1; // Status inicial (ex: PREPARANDO)

    // Calcular total do carrinho
    foreach ($_SESSION['carrinho'] as $id => $qtd) {
        $sql = "SELECT preco FROM itens WHERE cod_item = '$id'";
        $resultado = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $linha = mysqli_fetch_assoc($resultado);

        $preco = str_replace(",", "", $linha['preco']);
        $subtotal = $preco * $qtd;
        $total_carrinho += $subtotal;
        $quant += $qtd;
    }

    // Inserir pedido
    $sql_pedido = "INSERT INTO pedidos 
        (quant_itens, fk_Usuario_codigo, total_pedidos, cod_status_pedidos, tipo_pagamento)
        VALUES ('$quant', '$cliente', '$total_carrinho', '$cod_status', '$tipo_pag')";
    mysqli_query($conn, $sql_pedido) or die(mysqli_error($conn));

    $COD = mysqli_insert_id($conn); // ID do pedido recém-criado

    // Inserir itens no pedido
    foreach ($_SESSION['carrinho'] as $id => $qtd) {
        $sql_item = "INSERT INTO itens_pedido (cod_item, cod_pedido, quantidade)
                     VALUES ('$id', '$COD', '$qtd')";
        mysqli_query($conn, $sql_item) or die(mysqli_error($conn));

        // Limpar carrinho
        unset($_SESSION['carrinho'][$id]);
    }

    // Redirecionar
    header("Location: ../usu_comum/pedidos/pedidos.php");
    exit;
}
?>
