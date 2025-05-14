<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
  <link rel="stylesheet" type="text/css" href="../cabecalho/pag_inicial.css" />
  <link rel="stylesheet" type="text/css" href="../cabecalho/rodape.css" />
  <link rel="stylesheet" type="text/css" href="pagamento.css" />
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
    
?>
    <div class="fin_comp">
        <h2 class="titu">Finalizar Compra</h2>
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
                    <p class="subtot">R$ <?=$total_carrinh = number_format($total_carrinho,2,",",".") ?></p>
                </div>
                <div class="ent">
                    <p class="entr">Entrega</p>
                    <p class="gratis">Grátis</p>
                </div>
                <div class="tot">
                    <p class="tota">Total</p>
                    <p class="total">R$<?=$total_carrinh?></p>
                </div>
                <div class="vol">
                    <a class="voltar" href="../carrinho/carrinho.php">Voltar a sacola</a>
                </div>
            </div>
        </div>
<?php
        //parcelas
        $par1=  number_format($total_carrinho,2,",",".");
        $par2=  number_format($total_carrinho/2,2,",",".");
        $par3=  number_format($total_carrinho/3,2,",",".");
        $par4=  number_format($total_carrinho/4,2,",",".");
        $par5=  number_format($total_carrinho/5,2,",",".");
        $par6=  number_format($total_carrinho/6,2,",",".");
        $par7=  number_format($total_carrinho/7,2,",",".");
        $par8=  number_format($total_carrinho/8,2,",",".");
        $par9=  number_format($total_carrinho/9,2,",",".");
        $par10=  number_format($total_carrinho/10,2,",",".");
    
?>

    
        <div class="endereco">
            <p class="ende"><img src="../img/icone/pagamento.png">Pagamento<p>
            <form action="pag_compra.php" method="POST">
                <div class="tipo_cartao">
                    <div class="credito">
                        <input type="radio" class="rad cred" value="1" id="1" name="cartao">
                        <label class="lab credi" for="1">
                            <img src="../img/icone/credito.png"></br>
                            Cartão de crédito
                        </label>
                    </div>
                    <div class="debito">
                        <input type="radio" class="rad deb" value="2" id="2" name="cartao">
                        <label class="lab debi" for="2">
                            <img src="../img/icone/debito.png"></br>
                            Cartão de débito
                        </label>
                    </div>
                </div>
                <div class="num_parc">
                    <label class="lab_parc" for="parcela">Número de Parcelas</label>
                    <select name="parcela" id="parcela" required>
                        <option value="" disabled selected>Selecione</option>
                        <option value="1">1x R$ <?=$par1?></option>
                        <option value="2">2x R$ <?=$par2?></option>
                        <option value="3">3x R$ <?=$par3?></option>
                        <option value="4">4x R$ <?=$par4?></option>
                        <option value="5">5x R$ <?=$par5?></option>
                        <option value="6">6x R$ <?=$par6?></option>
                        <option value="7">7x R$ <?=$par7?></option>
                        <option value="8">8x R$ <?=$par8?></option>
                        <option value="9">9x R$ <?=$par9?></option>
                        <option value="10">10x R$ <?=$par10?></option>
                    </select>
                </div>
                <div class="num_cartao">
                    <label class="num_cart" >Número do cartão</label></br>
                    <input type="text" name="cc" class="num_carta" id="cc" maxlength="19" required>
                </div>
                <div class="nome_cartao">
                    <label class="nom_cart" >Nome impresso no cartão</label></br>
                    <input type="text" class="no_carta" name="nome" required>
                </div>
                <div class="validad_cartao">
                    <label class="valida_cart" >Validade</label>
                    <input type="month" name="valida" class="vali_carta" min="2022-10" max="2050-12" required>
                </div>
                <div class="cvv_cartao">
                    <label class="cv_cart" >CVV</label>
                    <input class="c_carta" name="cvv" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type = "number" maxlength = "3" required>
                </div>
                <div class="cpf_cartao">
                    <label class="cp_cart" >CPF do titular</label></br>
                    <input type="text" name="CPF" class="cpf_carta" id="CPF" required>
                </div>
                <div class="confirma">
                    <button type="submit" name="btn_pag" class="salva_v">Finalizar compra</button>
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
            <a href="endereco.php" class="link">
                <div class="pagamento">
                    <p class="etap"><img src="../img/icone/entrega.png">Endereco<p>
                    <p class="dad_ver" >Dados confirmados</p>
                    <img class="verificado" src="../img/icone/verificacao.png">
                </div>
            </a>
        </div>
    </div>  

    <?php
    }
    include_once "../cabecalho/rodape.html";
?>
</body>
</html>

<script type="text/javascript">
/* Máscaras ER */
function mascara(o,f){
    v_obj=o
    v_fun=f
    setTimeout("execmascara()",1)
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value)
}
function mcc(v){
    v=v.replace(/\D/g,"");
    v=v.replace(/^(\d{4})(\d)/g,"$1 $2");
    v=v.replace(/^(\d{4})\s(\d{4})(\d)/g,"$1 $2 $3");
    v=v.replace(/^(\d{4})\s(\d{4})\s(\d{4})(\d)/g,"$1 $2 $3 $4");
    return v;
}
function id( el ){
	return document.getElementById( el );
}
window.onload = function(){
	id('cc').onkeypress = function(){
		mascara( this, mcc );
	}
}
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.0/jquery.mask.js"></script>
<script>
 $(document).ready(function () { 
        var $CPF = $("#CPF");
        $CPF.mask('000.000.000-00', {reverse: true});
});

</script>