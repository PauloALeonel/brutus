<?php
	$servername = "localhost";
	$database = "brutus";
	$username = "root";
	$password = ""; 

	$conn = new mysqli($servername,$username,$password,$database);
   
    if ( isset($_POST["btnCadastrar"]) ){
        $nome=$_POST['nome'];
        $email=$_POST['email'];
        $cpf=$_POST['cpf'];
        $telefone=$_POST['telefone'];
        $senha=$_POST['senha'];


        $result = mysqli_query($conn, "INSERT INTO usuario (nome, email, cpf, telefone, senha, fk_tipos_usuario_codigo )
		VALUES ('$nome', '$email', '$cpf', '$telefone', md5($senha), '2') ") ;

        $bairro=$_POST['bairro'];
        $rua=$_POST['rua'];
        $numero=$_POST['numero'];
        $cep=$_POST['cep'];
        $complemento=$_POST['complemento'];
        $ult = mysqli_insert_id($conn);
    
        $result = mysqli_query($conn, "INSERT INTO endereco (cep, rua, bairro, numero, complemento, cidade, fk_Usuario_codigo )
		VALUES ('$cep', '$rua', '$bairro', '$numero', '$complemento', 'Ourinhos', '$ult') ") ;

        $codigo = mysqli_insert_id($conn);
        $_SESSION['id_logado'] = $codigo;
        header('Location: /brutus/index.php'); // Redireciona para a página inicial
    }

?>