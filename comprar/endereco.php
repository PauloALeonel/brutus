<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
  <link rel="stylesheet" type="text/css" href="../geral.css" />
  <link rel="stylesheet" type="text/css" href="endereco.css" />
  <title>Brutus - Comprar</title>
</head>
<body>
<?php 
    session_start(); // abre a sessão

    include_once "../cabecalho.html";
    include_once "conecta.php";
    if(!isset ($_SESSION['id_logado']) == true){ 
        header ('location: ../login/index.php');
    } 

    else{
        $cliente= $_SESSION['id_logado'];
    
    $query_dados="SELECT*FROM endereco WHERE fk_Usuario_codigo=$cliente";
    $dados = mysqli_query( $conn, $query_dados);
    $row_dados = mysqli_fetch_assoc($dados);

    $cep= $row_dados['cep'];

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
            <p class="ende"><img src="icone/entrega.png">Entrega<p>
            <form action="end_update.php" method="POST">
                <div class="entrega">
                    <label class="end_label">CEP</label>
                    <input type="text" value="<?=$cep ?>" name="CEP" id="CEP" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Cidade</label>
                    <input type="text" value="<?=$row_dados['cidade'] ?>" id="cidade" name="cidade" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Bairro</label>
                    <input type="text" value="<?=$row_dados['bairro'] ?>" id="bairro" name="bairro" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Rua</label>
                    <input type="text" value="<?=$row_dados['rua'] ?>" id="rua" name="rua" class="insere_dados" required>
                </div>
                <div class="entrega">
                    <label class="end_label">Número</label>
                    <input type="number" value="<?=$row_dados['numero'] ?>" name="numero" class="insere_dados" required>
                </div>
                <div class="confirma">
                    <button type="submit" name="btn_end" class="salva_v">Ir para pagamento</button>
                </div>
            </form>
        </div>
        <div class="etapas">
            <a href="identificacao.php" class="link"> 
                <div class="identificacao">
                    <p class="etap"><img src="/icone/perfil.png">Identificação<p>
                    <p class="dad_ver" >Dados confirmados</p>
                    <img class="verificado" src="icone/verificacao.png">
                </div>
            </a>
            <div class="pagamento">
                <p class="etap"><img src="icone/pagamento.png">Pagamento<p>
                <p>Aguardando preenchimento dos dados</p>
            </div>
        </div>
        <div class="pedido">
            <p class="resum">Resumo do pedido<p>
            <?php
                $total_carrinho=0;
                foreach ( $_SESSION['carrinho'] as $id => $qtd)
                {
                    $sql = "SELECT cod_item, nome, preco, imagem
                    FROM itens
                    WHERE cod_item = '$id'";
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
                     $caminhoImagem = "../produtos/" . $linha[3]; 
                    
                   echo "<div class='produto'>
                            <div class='quantidade'>
                                <p class='quant'> $qtd<p/>
                            </div>
                            <div class='imagem'>";
                                 echo "<img src='$caminhoImagem' class='imagem_prod'>
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
        include_once "../rodape.html";
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