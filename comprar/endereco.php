<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
  <link rel="stylesheet" type="text/css" href="../cabecalho/pag_inicial.css" />
  <link rel="stylesheet" type="text/css" href="../cabecalho/rodape.css" />
  <link rel="stylesheet" type="text/css" href="endereco.css" />
  <link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
  <title>Iluminatta - Otica e Joalheria</title>
</head>
<body>
<?php 
    include_once "../cabecalho/index.php";
    include_once "conecta.php";
    if(!isset ($_SESSION['id_logado']) == true){ 
        header ('location: ../login/index.php');
    } 

    else{
        $cliente= $_SESSION['id_logado'];
    
    $query_dados="SELECT*FROM tb_cliente WHERE CODIGO=$cliente";
    $dados = mysqli_query( $conn, $query_dados);
    $row_dados = mysqli_fetch_assoc($dados);

    $cep= $row_dados['CEP'];

    function mask($val, $mask)
    {
    $maskared = '';
    $k = 0;
    for($i = 0; $i<=strlen($mask)-1; $i++)
    {
    if($mask[$i] == '#')
    {
        if(isset($val[$k]))
        $maskared .= $val[$k++];
    }
    else
    {
        if(isset($mask[$i]))
        $maskared .= $mask[$i];
        }
    }
    return $maskared;
    }

?>

    <div class="fin_comp">
        <h2 class="titu">Finalizar Compra</h2>
        <div class="endereco">
            <p class="ende"><img src="../img/icone/entrega.png">Entrega<p>
            <form action="end_update.php" method="POST">
                <div class="entrega">
                    <label class="end_label">CEP</label>
                    <input type="text" value="<?=$cep ?>" name="CEP" id="CEP" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Cidade</label>
                    <input type="text" value="<?=$row_dados['CIDADE'] ?>" id="cidade" name="cidade" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Bairro</label>
                    <input type="text" value="<?=$row_dados['BAIRRO'] ?>" id="bairro" name="bairro" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Rua</label>
                    <input type="text" value="<?=$row_dados['RUA'] ?>" id="rua" name="rua" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Número</label>
                    <input type="number" value="<?=$row_dados['NUMERO'] ?>" name="numero" class="insere_dados" required>
                </div>
                <div class="confirma">
                    <button type="submit" name="btn_end" class="salva_v">Ir para pagamento</button>
                </div>
            </form>
        </div>
        <div class="etapas">
            <a href="identificacao.php" class="link"> 
                <div class="identificacao">
                    <p class="etap"><img src="../img/icone/perfil.png">Identificação<p>
                    <p class="dad_ver" >Dados confirmados</p>
                    <img class="verificado" src="../img/icone/verificacao.png">
                </div>
            </a>
            <div class="pagamento">
                <p class="etap"><img src="../img/icone/pagamento.png">Pagamento<p>
                <p>Aguardando preenchimento dos dados</p>
            </div>
        </div>
        <div class="pedido">
            <p class="resum">Resumo do pedido<p>
            <?php
                $total_carrinho=0;
                foreach ( $_SESSION['carrinho'] as $id => $qtd)
                {
                    $sql = "SELECT COD_PRODUTO, NOME, PRECO, CHAVE_IMG_PROD, CAMINHO_IMG 
                    FROM tb_produto, tb_varias_img 
                    WHERE COD_PRODUTO = '$id' AND COD_PRODUTO = CHAVE_IMG_PROD 
                    GROUP BY COD_PRODUTO, NOME, PRECO, CHAVE_IMG_PROD";
                    $resultado = mysqli_query($conn,$sql) or die (mysqli_error());
                    $linha = mysqli_fetch_array($resultado);
                    
                    //0 id, 1 nome, 2 preco e 3 imagem
                    $nome = $linha[1];
                    $preco = str_replace("," , "" , $linha[2] );
                    $subtotal = $preco * $qtd;
                    $subtota= substr_replace($subtotal, '.', -2, 0);
                    $subtotal= number_format($subtota,2,",",".");
                    $valor = substr_replace($preco, '.', -2, 0);
                    $valor= number_format($valor,2,",",".");
                    $total_carrinho += $subtota;
                    
                   echo "<div class='produto'>
                            <div class='quantidade'>
                                <p class='quant'> $qtd<p/>
                            </div>
                            <div class='imagem'>
                                <img src='.$linha[4]' class='imagem_prod'>
                            </div>
                            <div class='inf'>
                                <div class='linha_1'>
                                    <p class='nome'>$linha[1]</p>
                                </div>
                                <div class= 'linha_2'> 
                                    <p class='valor'>R$ $valor</p>
                                    <p class='subtotal'>Total: R$ $subtotal</p>";?>
                                </div>
                            </div>
                        </div>	</br>
                          <?php			 
                }		
            ?></br>
            <hr class="linha">
            <div class="preco">
                <div class="sub">
                    <p class="subt">Subtotal</p>
                    <p class="subtot">R$ <?=$total_carrinho = number_format($total_carrinho,2,",",".") ?></p>
                </div>
                <div class="ent">
                    <p class="entr">Entrega</p>
                    <p class="gratis">Grátis</p>
                </div>
                <div class="tot">
                    <p class="tota">Total</p>
                    <p class="total">R$<?=$total_carrinho?></p>
                </div>
                <div class="vol">
                    <a class="voltar" href="../carrinho/carrinho.php">Voltar a sacola</a>
                </div>
            </div>
        </div> 
    </div>  


    <?php 
    }
        include_once "../cabecalho/rodape.html";
    ?>
</body>
</html>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.0/jquery.mask.js"></script>

<script type="text/javascript">
$(document).ready(function(){
  var $CEP = $("#CEP");
	$("#CEP").mask("99999-999");
});
</script>

<script type="text/javascript" src="cep.js"></script>