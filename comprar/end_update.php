<?php 
    session_start(); // abre a sessão
        include_once "conecta.php"; 
        $cliente= $_SESSION['id_logado'];

        if ( isset($_POST["btn_end"]) )
        {
            $cep= $_POST['CEP'];
            $cidade= $_POST['cidade'];
            $bairro= $_POST['bairro'];
            $rua= $_POST['rua'];
            $numero= $_POST['numero'];

            $cep = str_replace("-" , "" , $cep );

            $sqldados = "UPDATE tb_cliente
                          SET CEP='$cep', CIDADE='$cidade', BAIRRO='$bairro', RUA='$rua', NUMERO='$numero'
                          WHERE CODIGO=$cliente";
                        
            mysqli_query($conn, $sqldados)or die( mysqli_error($conn) );
        }
    ?>

<?php header("Location: pagamento.php"); ?>