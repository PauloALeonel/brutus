<?php

include_once "conecta.php"; 
session_start();
$cliente= $_SESSION['id_logado'];
    if ( isset($_POST["btn_pag"]) )
    {
        $cartao=$_POST['cartao'];
        $parcelas=$_POST['parcela'];
        $num_cartao=$_POST['cc'];
        $nome=$_POST['nome'];
        $validad=$_POST['valida'];
        $cvv=$_POST['cvv'];
        $cpf=$_POST['CPF'];

        $validad = str_replace("-" , "" , $validad );

        $num_cartao = str_replace(" " , "" , $num_cartao );
        $cpf = str_replace("." , "" , $cpf );
        $cpf = str_replace("-" , "" , $cpf );
        $quant=0;
        $total_carrinho=0;
        foreach ( $_SESSION['carrinho'] as $id => $qtd)
        {
            $sql = "SELECT COD_PRODUTO, PRECO, ESTOQUE 
                    FROM tb_produto
                    WHERE COD_PRODUTO = '$id'";
                    $resultado = mysqli_query($conn,$sql) or die (mysqli_error());
                    $linha = mysqli_fetch_array($resultado);
                    $preco = str_replace("," , "" , $linha[1] );
                    $subtotal = $preco * $qtd;
                    $total_carrinho += $subtotal;

            $quant+=$qtd;
            $est= $linha[2]-$qtd;
            $sql_estoque = "UPDATE tb_produto
                          SET ESTOQUE='$est' WHERE COD_PRODUTO = $id";
                        
            mysqli_query($conn, $sql_estoque)or die( mysqli_error($conn) );
        }
        
        $sql_car= "INSERT INTO tb_carrinho
        (QUANT_ITENS, COD_CLIENTE, PARCELAS, VALOR_TOTAL, STATU)
       VALUES
        ('$quant', '$cliente', '$parcelas', '$total_carrinho', 'SEPARANDO')";
        mysqli_query($conn, $sql_car)or die( mysqli_error($conn) );
        $COD = mysqli_insert_id($conn);

        foreach ( $_SESSION['carrinho'] as $id => $qtd)
        {
            $quant+=$qtd;
            $sql_item = "INSERT INTO tb_item_carrinho
        (CODIGO_CARRINHO, COD_ITEM, QUANT_ITEM)
       VALUES
        ('$COD', '$id', '$qtd')";
        mysqli_query($conn, $sql_item)or die( mysqli_error($conn) );
        
        unset($_SESSION['carrinho'][$id]);  


        }

        $sql_cartao="INSERT INTO tb_cartao
        (COD_CLIENTE, COD_CARRINHO, TIPO, NUMERO, NOME, VALIDADE, CVV, CPF)
       VALUES
        ('$cliente', '$COD', '$cartao', '$num_cartao', '$nome', '$validad', '$cvv', '$cpf')";
        mysqli_query($conn, $sql_cartao)or die( mysqli_error($conn) );

        header("Location: ../usu_comum/pedidos/pedidos.php");

        
    }

?>