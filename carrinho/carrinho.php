<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" type="text/css" href="../geral.css" />
  <link rel="stylesheet" type="text/css" href="carrinho.css" />
</head>
<body>
<?php
session_start(); // abre a sessão
	

	if(!isset ($_SESSION['id_logado']) == true){ 
		header ('location: ../login/login.php');
	} 

	else{
		include_once "../cabecalho.html";
		$cliente= $_SESSION['id_logado'];
		
?>


		<div class="sacola">
				<hr class="linha">
			<?php
			   $servername = "localhost";
			   $database = "brutus";
			   $username = "root";
			   $password = ""; 
			
			   $conn = mysqli_connect($servername,
										  $username,
										  $password,
										  $database);
			
				   
				   
			
				// verifica se não existe a sessão responsável por guardar nossos valores
				if($cliente == 41){
					echo "<p class='n_pedo'>Entre como cliente!</p>";
				}
				else{
				if ( !isset($_SESSION['carrinho']) ) 
				{
					$_SESSION['carrinho'] = array(); 
				}
					
				// verifica a ação
				if ( isset($_GET['acao']) )
				{
					// se é adicionar produto	 
					if ($_GET['acao'] == 'add')
					{
						$id = $_GET['id'];
						$esto= "SELECT*FROM itens WHERE cod_item = $id";
							$esto_consul = mysqli_query($conn,$esto);
							$esto_result = mysqli_fetch_array($esto_consul);
						// se não existir o produto no carrinho
						if (!isset($_SESSION['carrinho'][$id]) )
						{
							$_SESSION['carrinho'][$id] = 1; // quantidade receberá 1 inicialmente  
							$quanti=$_SESSION['carrinho'][$id] ;
							
						}
						else // se caso já existir adiciona +1 para o que já tem
						{
							
							$quanti=$_SESSION['carrinho'][$id] ;
							
								$_SESSION['carrinho'][$id] += 1;  
							
						}
					}
				
					// REMOVER O PRODUTO DO ARRAY
					if ($_GET['acao'] == 'del')
					{
						$id = $_GET['id'];
						if (isset($_SESSION['carrinho'][$id]))
						{
							/// limpa o produto no carrinho...   
							unset($_SESSION['carrinho'][$id]);  
						}	  
							
					}

					if ($_GET['acao'] == 'up')
					{
						$id = $_GET['id'];
						
						
						if ($_SESSION['carrinho'][$id]==1) 
						{
							unset($_SESSION['carrinho'][$id]);    
						}
						else 
						{
							$_SESSION['carrinho'][$id] -= 1;  
						}
					}
					
				}
			?>
				<h1 class="titulo">Meu carrinho</h1>
				<div class="carrinho">
					<form action="carrinho.php?acao=up" method="post">
						<?php
							// Usa função count para verificar se o carrinho está vazio
							if ( count($_SESSION['carrinho'])==0 )
							{  
								echo "<p class='n_ped'>Não há produto no carrinho!</p>";
							}
							else
							{
								include "conecta.php";  
								// percorre o array
								$total_carrinho=0;
								foreach ( $_SESSION['carrinho'] as $id => $qtd)
								{
									$sql = "SELECT * FROM itens
									WHERE cod_item = '$id'";
									$resultado = mysqli_query($conn,$sql) or die (mysqli_error());
									$linha = mysqli_fetch_array($resultado);
									
									//0 id, 1 nome, 2 preco e 3 imagem
									$nome = $linha[1];
									$preco = str_replace("," , "" , $linha[3] );
									$subtotal = $preco * $qtd;
									$subtota = substr_replace($subtotal, '.', -2, 0);
									$subtotal = number_format($subtota,2,",",".");
									$valor = substr_replace($preco, '.', -2, 0);
									$valor = number_format($valor,2,",",".");
									$total_carrinho += $subtota;
									
									echo "<div class='produto'>
											<div class='imagem'>
												<img src='../produtos/$linha[4]' class='imagem_prod'>
											</div>
											<div class='inf'>
												<div class='linha_1'>
													<p class='nome'>$linha[1]</p>
													<a href='carrinho.php?acao=del&id=$id'><img class='rem' src='../img/lixeira.png' align='right'></a>
												</div>
												<hr class='linha_nome'>
												<div class= 'linha_2'>
													<div class='quantidade'> "; ?> 			
													<a <?php echo "href='carrinho.php?id=$id&acao=up'"?>><button type="button" class="add_prod">-</button></a>
										<?php echo "<input type='text' size='3' id='total' name='prod[$id]' value='$qtd'  class='quant' disabled/>";?>
										<a <?php echo "href='carrinho.php?id=$id&acao=add'"?>><button type="button" class="add_prod">+</button></a>
										<?php  echo"</div> 
													<p class='valor'>R$ $valor</p>
													<p class='subtotal' align='right'>Total: R$ $subtotal</p>";?>
												</div>
											</div>
										</div>	
										<?php			 
								}		
								echo "</table>";  
								
						?>  
					</form>
				</div>
				<div class="pedido">
					<div class="resum_comp">
						<p class="resumo">Resumo do pedido</p>
						<p class="sub">Subtotal</p><p class="sub_val">R$ <?=$total_carrinho = number_format($total_carrinho,2,",","."); ?></p>
						<div class="entrega"><p class="sub">Entrega</p><p class="sub_val">Grátis</p></div>
						<p class="sub">Total</p><p class="sub_val">R$ <?=$total_carrinho?></p>
					</div>
					<?php
						if ( count($_SESSION['carrinho'])<>0 )
						{
							echo 	"<div class='fim_compra'>
										<a class='finaliza' href='../comprar/identificacao.php'>Finalizar Pedido</a>
									</div>";
						}
					?>
					<div class="cont">
						<a class="continuar" href="../cardapio/cardapio.php">Continuar comprando...</a>  
					</div>
				</div>
			 <?php 
							}
			}
			?>
	</div>
	<hr class="linha_hr">
	<?php
		}
		include_once "../rodape.html";
	?> 

	<script>
	var btn = document.getElementById('btn');
	var container = document.querySelector('.retorna');
	btn.addEventListener('click', function() {
		
	if(container.style.display == 'block') {
		container.style.display = 'block';
	} else {
		container.style.display = 'block';
	}
	});
	</script>
</body>
</html>


