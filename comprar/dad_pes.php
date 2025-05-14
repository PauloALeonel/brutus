<?php
    session_start(); // abre a sessão
    include_once "conecta.php"; 
    $cliente= $_SESSION['id_logado'];

    if ( isset($_POST["btn_dados"]) )
    {
        $nome= $_POST['nome'];
        $sobrenome= $_POST['sobrenome'];
        $cpf= $_POST['cpf'];
        $email= $_POST['email'];

        $cpf = str_replace("." , "" , $cpf );
        $cpf = str_replace("-" , "" , $cpf );

        $sqldados = "UPDATE tb_cliente
                      SET NOME='$nome', SOBRENOME='$sobrenome', CPF='$cpf'
                      WHERE CODIGO=$cliente";
                    
        mysqli_query($conn, $sqldados)or die( mysqli_error($conn) );
    

    $sqldado = "UPDATE tb_usuario
                      SET LOGINUSU ='$email'
                      WHERE CODIGO=$cliente";
                    
        mysqli_query($conn, $sqldado)or die( mysqli_error($conn) );
    }
?>

<?php header("Location: endereco.php"); ?>
?>