<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta charset="utf-8" />
        <link rel="shortcut icon" type="imagex/png" href="../img/logo.png">
        <title>Iluminatta - Otica e Joalheria</title>
        <link rel="stylesheet" type="text/css" href="pag_inicial.css" />
        <link rel="stylesheet" type="text/css" href="../usu_comum/cabecalho/cabecalho_adm.css" />
    </head>
    <body>
        <div id="containe-menu">
            <div id="topo">
                <div id="imagem1">
                    <img src="../img/logo/logo.svg" id="img">
                </div>
                <div id="icones">
                <?php
                  
                    include_once "conecta.php";
                    if(!isset ($_SESSION['id_logado']) == true){ ?>
                        <div id="icone_cad">
                            <a href="../cadastro_usuario/cadastro_usu.php">
                                <img src="../img/icone/usuario.svg">
                            </a>
                            <div id="icon_cadas">
                                <br/>
                                <div id="icon_login">
                                    <a href="../login/index.php">Entrar</a><br/>
                                </div>
                                <p>ou</p>
                                <div id="icon_cadastrar">
                                    <a href="../cadastro_usuario/cadastro_usu.php">Cadastrar</a>
                                </div>
                            </div>
                        </div>
                    <?php  } 

                    else{
                        $cliente= $_SESSION['id_logado'];
                        $sql_log="SELECT * FROM tb_usuario WHERE COD_CLIENTE = '$cliente' ";
                        $log = mysqli_query( $conn, $sql_log);
                        $linha_log = mysqli_fetch_assoc($log);

                        if($linha_log['USER'] == 'A'){ ?>
                            <div id="icone_cad">
                                <a href="../dados_adm/dados_pessoais.php">
                                    <img src="../img/icone/usuario.svg">
                                </a>
                                <div id="icon_adm">
                                    <br/>
                                    <div id="icon_conta">
                                        <a href="../administrador/dados_adm/dados_pessoais.php">Administrador</a><br/>
                                    </div>
                                    <hr>
                                    <div id="icon_sair">
                                        <a onclick="javascript: if (confirm('Você realmente deseja sair?'))location.href=<?php echo "'../sair/sair.php'"; ?>">Sair</a>
                                    </div>
                                </div>
                            </div>
                        <?php  } 
                        if($linha_log['USER'] == 'C'){ ?>
                            <div id="icone_cad">
                                <a href="../usu_comum/dados_usu/dados_pessoais.php">
                                    <img src="../img/icone/usuario.svg">
                                </a>
                                <div id="icon_adm">
                                    <br/>
                                    <div id="icon_conta">
                                        <a href="../usu_comum/dados_usu/dados_pessoais.php">Minha Conta</a><br/>
                                    </div>
                                    <hr>
                                    <div id="icon_sair">
                                        <a onclick="javascript: if (confirm('Você realmente deseja sair?'))location.href=<?php echo "'../sair/sair.php'"; ?>">Sair</a>
                                    </div>
                                </div>
                            </div>
                <?php  } 
                    }
                    ?>
                   
                                    
                    <a href="../carrinho/carrinho.php">
                        <div id="icone_sac">
                            <img src="../img/icone/sacola.svg">
                        </div>
                    </a>
                </div> 
            
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="../home/vitrine.php">Home</a></li>
                    <li><a href="../categorias/marca.php?categ=3,4">Óculos de Sol</a>
                        <ul>
                            <li><a href="../categorias/marca.php?categ=4">Feminino</a></li>
                            <li><a href="../categorias/marca.php?categ=3">Masculino</a></li>
                        </ul>
                    </li>
                    <li><a href="../categorias/marca.php?categ=1,2">Óculos de Grau</a>
                        <ul>
                            <li><a href="../categorias/marca.php?categ=2">Feminino</a></li>
                            <li><a href="../categorias/marca.php?categ=1">Masculino</a></li>
                        </ul>
                    </li>
                    <li><a href="../categorias/marca.php?categ=7,8,9,10,11,12,13">Joias</a>
                        <ul>
                            <li><a href="../categorias/marca.php?categ=9">Anéis</a></li>
                            <li><a href="../categorias/marca.php?categ=13">Bracelete</a></li>
                            <li><a href="../categorias/marca.php?categ=8">Brincos</a></li>
                            <li><a href="../categorias/marca.php?categ=11">Colares</a></li>
                            <li><a href="../categorias/marca.php?categ=7">Pendentes</a></li>
                            <li><a href="../categorias/marca.php?categ=12">Pingentes</a></li>
                            <li><a href="../categorias/marca.php?categ=10">Pulseiras</a></li>
                        </ul>
                    </li>
                    <li><a href="../categorias/marca.php?categ=5,6">Relógio</a>
                        <ul>
                            <li><a href="../categorias/marca.php?categ=6">Feminino</a></li>
                            <li><a href="../categorias/marca.php?categ=5">Marculino</a></li>
                        </ul>
                    </li>
                    <li><a href="../categorias/marca.php" >Marcas</a>
                        <ul id="marca" >
							<li><a href="../categorias/marca.php?codmarca=1">Ana Hickmann</a></li>  
                          <!--<li><a href="../categorias/ana.php?codmarca=1">Ana Hickmann</a></li>-->
                            <li><a href="../categorias/marca.php?codmarca=2">Carolina Herrera</a></li>
                            <li><a href="../categorias/marca.php?codmarca=3">Chilli Beans</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=4">Colcci</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=5">Dolce&Gabbana</a></li>
                            <li><a href="../categorias/marca.php?codmarca=6">Gucci</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=7">Guess</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=8">Lacoste</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=9">Olivia Burtonby</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=10">Polaroid</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=11">Tommy Hilfiger</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=12">Versace</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=13">Vivara</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=14">Cartier</a></li> 
							<li><a href="../categorias/marca.php?codmarca=15">Pandora</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=16">Monte Carlo</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=17">Tiffany&Go</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=18">Cassio</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=19">Puma</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=20">Diesel</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=21">Nike</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=22">Oakley</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=23">Prada</a></li> 
                            <li><a href="../categorias/marca.php?codmarca=24">Ray Ban</a></li> 
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
        
    </body>
</html>