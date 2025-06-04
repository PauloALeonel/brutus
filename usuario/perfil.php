<?php
session_start();

// Inicializa variáveis para evitar erros
$mensagem_sucesso = '';
$erros = [];
$nome = "";
$email = "";
$telefone = "";
$cpf = "";

// Conexão com o banco de dados
$host = "localhost"; 
$database = "brutus"; 
$username = "root"; 
$password = ""; 

try {
    // Usando PDO para conexão com o banco de dados
    $conn = new PDO("mysql:host=$host;dbname=" . $database, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verifica se o usuário está logado
    if (!isset($_SESSION['id_logado']) || empty($_SESSION['id_logado'])) {
        // Redireciona para a página de login se não estiver logado
        header("Location: login.php");
        exit;
    }
    
    // Obtém o ID do usuário logado
    $usuario_id = $_SESSION['id_logado'];
    
    // Processamento do formulário quando enviado (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Captura os dados do formulário
        $novo_nome = isset($_POST['nome']) ? $_POST['nome'] : '';
        $novo_email = isset($_POST['email']) ? $_POST['email'] : '';
        $novo_telefone = isset($_POST['telefone']) ? $_POST['telefone'] : '';
        $novo_cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
        $senha_atual = isset($_POST['senha_atual']) ? $_POST['senha_atual'] : '';
        $nova_senha = isset($_POST['nova_senha']) ? $_POST['nova_senha'] : '';
        $confirmar_nova_senha = isset($_POST['confirmar_nova_senha']) ? $_POST['confirmar_nova_senha'] : '';
        
        // Flag para verificar se a senha deve ser alterada
        $alterar_senha = false;
        
        // Verifica se quer alterar a senha
        if (!empty($senha_atual)) {
            $alterar_senha = true;
            
            // Verifica se a senha atual está correta
            $query_senha = "SELECT senha FROM usuario WHERE codigo = :id";
            $stmt_senha = $conn->prepare($query_senha);
            $stmt_senha->bindParam(':id', $usuario_id, PDO::PARAM_INT);
            $stmt_senha->execute();
            
            if ($stmt_senha->rowCount() > 0) {
                $usuario_senha = $stmt_senha->fetch(PDO::FETCH_ASSOC);
                
                // Verificação da senha usando MD5
                // Compara o MD5 da senha digitada com o hash armazenado no banco
                if (md5($senha_atual) != $usuario_senha['senha']) {
                    $erros[] = "Senha atual incorreta";
                    
                }
                
                if (empty($nova_senha)) {
                    $erros[] = "Nova senha é obrigatória";
                } elseif ($nova_senha != $confirmar_nova_senha) {
                    $erros[] = "As senhas não coincidem";
                }
            } else {
                $erros[] = "Usuário não encontrado";
            }
        }
        
        // Se não houver erros, atualiza os dados
        if (empty($erros)) {
            try {
                // Prepara a query de atualização
                if ($alterar_senha) {
                    // Aplica MD5 na nova senha antes de salvar no banco
                    $senha_hash = md5($nova_senha);
                    
                    // Atualiza dados e senha
                    $query_update = "UPDATE usuario SET nome = :nome, email = :email, telefone = :telefone, cpf = :cpf, senha = :senha WHERE codigo = :id";
                    $stmt_update = $conn->prepare($query_update);
                    $stmt_update->bindParam(':senha', $senha_hash); // Usa o hash MD5
                } else {
                    // Atualiza apenas os dados, sem a senha
                    $query_update = "UPDATE usuario SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone WHERE codigo = :id";
                    $stmt_update = $conn->prepare($query_update);
                }
                
                $stmt_update->bindParam(':nome', $novo_nome);
                $stmt_update->bindParam(':email', $novo_email);
                $stmt_update->bindParam(':telefone', $novo_telefone);
                $stmt_update->bindParam(':cpf', $novo_cpf);
                $stmt_update->bindParam(':id', $usuario_id, PDO::PARAM_INT);
                
                if ($stmt_update->execute()) {
                    $mensagem_sucesso = "Dados atualizados com sucesso!";
                    
                    // Atualiza as variáveis para exibir os novos valores no formulário
                    $nome = $novo_nome;
                    $email = $novo_email;
                    $telefone = $novo_telefone;
                    $cpf = $novo_cpf;
                } else {
                    $erros[] = "Erro ao atualizar os dados. Tente novamente.";
                }
            } catch (PDOException $e) {
                $erros[] = "Erro no banco de dados: " . $e->getMessage();
            }
        }
    }
    
    // Busca os dados do usuário SEMPRE, independente se houve POST ou não
    // Se houve POST sem erros, os dados já foram atualizados acima
    if (empty($nome) || empty($email)) { // Só busca do banco se os dados não foram definidos pelo POST
        $query = "SELECT nome, email, telefone, cpf FROM usuario WHERE codigo = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Verifica se encontrou o usuário
        if ($stmt->rowCount() > 0) {
            // Obtém os dados do usuário
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Atualiza as variáveis com os dados do banco
            $nome = $usuario['nome'] ?? '';
            $email = $usuario['email'] ?? '';
            $telefone = $usuario['telefone'] ?? '';
            $cpf = $usuario['cpf'] ?? '';
        } else {
            $erros[] = "Usuário não encontrado no banco de dados.";
        }
    }
    
    // Buscar os endereços do usuário logado para a aba de endereços
    $query_enderecos = "SELECT * FROM endereco WHERE fk_Usuario_codigo = :usuario_id";
    $stmt_enderecos = $conn->prepare($query_enderecos);
    $stmt_enderecos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_enderecos->execute();
    
    // Busca todos os pedidos do usuário para a aba de histórico
// Busca todos os pedidos do usuário para a aba de histórico
$sql = "SELECT p.cod_pedido, p.datahora_pedido, p.total_pedidos, 
               p.tipo_pagamento, p.quant_itens, 
               (SELECT s.status_pedidos 
                FROM hist_status_ped h 
                JOIN status_pedidos s ON h.cod_status = s.cod_status_pedidos
                WHERE h.cod_pedido = p.cod_pedido
                ORDER BY h.data_hora DESC LIMIT 1) AS status_atual
        FROM pedidos p
        WHERE p.fk_Usuario_codigo = :usuario_id
        ORDER BY p.datahora_pedido DESC";

$stmt_pedidos = $conn->prepare($sql);
$stmt_pedidos->bindParam(':usuario_id', $_SESSION['id_logado'], PDO::PARAM_INT);
$stmt_pedidos->execute();
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

// Para cada pedido, buscar o histórico de status
foreach ($pedidos as &$pedido) {
    $sql_historico = "SELECT h.data_hora, s.status_pedidos
                      FROM hist_status_ped h
                      JOIN status_pedidos s ON h.cod_status = s.cod_status_pedidos
                      WHERE h.cod_pedido = :pedido_id
                      ORDER BY h.data_hora";
    
    $stmt_historico = $conn->prepare($sql_historico);
    $stmt_historico->bindParam(':pedido_id', $pedido['cod_pedido'], PDO::PARAM_INT);
    $stmt_historico->execute();
    $pedido['historico'] = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);
}
} catch (PDOException $e) {
    $erros[] = "Erro de conexão: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | BRUTUS</title>
    <link rel="icon" href="../img/favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../geral.css"> 
    <link rel="stylesheet" href="perfil.css"> 
</head>
<body>

<?php include_once "../cabecalho.html"; ?>

<div class="container my-3">
    <div class="row">
        <!-- Menu Lateral -->
        <div class="col-md-3">
            <div class="list-group profile-menu">
                <a href="#editar-dados" class="list-group-item list-group-item-action active" data-bs-toggle="tab"><i class="fas fa-user-edit me-2"></i>Editar Dados</a>
                <a href="#meus-enderecos" class="list-group-item list-group-item-action" data-bs-toggle="tab"><i class="fas fa-map-marker-alt me-2"></i>Meus Endereços</a>
                <a href="#historico-pedidos" class="list-group-item list-group-item-action" data-bs-toggle="tab"><i class="fas fa-history me-2"></i>Histórico de Pedidos</a>
                <a href="logout.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-out-alt me-2"></i>Sair</a>
                <a href="#" class="list-group-item list-group-item-action text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fas fa-trash-alt me-2"></i>Excluir Conta</a>
            </div>
        </div>

        <!-- Conteúdo das Seções -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Editar Dados -->
                <div class="tab-pane fade show active" id="editar-dados">
                    <h2 class="mb-4 profile-title">Editar Dados Pessoais</h2>
                    <?php if (!empty($mensagem_sucesso)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $mensagem_sucesso; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                <?php foreach ($erros as $erro): ?>
                                    <li><?php echo $erro; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" placeholder="Seu nome completo">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="seuemail@exemplo.com">
                        </div>
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo htmlspecialchars($cpf); ?>" placeholder="XXX.XXX.XXX-XX" maxlength="14">
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>" placeholder="(XX) XXXXX-XXXX">
                        </div>
                        <div class="mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual (se quiser alterar)">
                        </div>
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Digite a nova senha">
                        </div>
                        <div class="mb-3">
                            <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="confirmar_nova_senha" name="confirmar_nova_senha" placeholder="Confirme a nova senha">
                        </div>
                        <button type="submit" class="btn btn-primary profile-btn">Salvar Alterações</button>
                    </form>
                </div>

                <!-- Meus Endereços -->
                <div class="tab-pane fade" id="meus-enderecos">
                    <h2 class="mb-4 profile-title">Meus Endereços</h2>

                    <?php if ($stmt_enderecos->rowCount() > 0): ?>
                        <?php while ($endereco = $stmt_enderecos->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="card mb-3 address-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title">
                                            <?php echo htmlspecialchars($endereco['identificacao']); ?>
                                        </h5>
                                    </div>
                                    <p class="card-text mb-1">
                                        <?php echo htmlspecialchars($endereco['rua']) . ', ' . htmlspecialchars($endereco['numero']); ?>
                                        <?php if (!empty($endereco['complemento'])): ?>
                                            , <?php echo htmlspecialchars($endereco['complemento']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="card-text mb-1"><?php echo htmlspecialchars($endereco['bairro']); ?></p>
                                    <p class="card-text mb-2">
                                        <?php echo htmlspecialchars($endereco['cidade']) . ' - SP'; ?>, 
                                        CEP <?php echo htmlspecialchars($endereco['cep']); ?>
                                    </p>
                                    <?php if (!empty($endereco['referencia'])): ?>
                                        <p class="card-text mb-1">
                                            <small class="text-muted">Referência: <?php echo htmlspecialchars($endereco['referencia']); ?></small>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($endereco['telefone_contato'])): ?>
                                        <p class="card-text mb-2">
                                            <small class="text-muted">Telefone: <?php echo htmlspecialchars($endereco['telefone_contato']); ?></small>
                                        </p>
                                    <?php endif; ?>
                                    <div class="mt-2">
                                        <a href="cadastro-endereco.php?id=<?php echo $endereco['cod_endereco']; ?>" class="btn btn-sm btn-warning me-2">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="excluir-endereco.php?id=<?php echo $endereco['cod_endereco']; ?>" class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja excluir este endereço?');">
                                            <i class="fas fa-trash"></i> Excluir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Você ainda não possui endereços cadastrados.
                        </div>
                    <?php endif; ?>

                    <!-- Botão para adicionar novo endereço -->
                    <a href="cadastro-endereco.php" class="btn btn-success mb-3 profile-btn-alt">
                        <i class="fas fa-plus me-2"></i> Adicionar Novo Endereço
                    </a>
                </div>

                <!-- Histórico de Pedidos -->
                <div class="tab-pane fade" id="historico-pedidos">
                    <h2 class="mb-4 profile-title">Histórico de Pedidos</h2>

    <?php if (!empty($pedidos)): ?>
        <?php foreach ($pedidos as $pedido): ?>
            <?php
            // Formata a data e hora
            $data_pedido = date('d/m/Y', strtotime($pedido['datahora_pedido']));
            $hora_pedido = date('H:i', strtotime($pedido['datahora_pedido']));
            
            // Define a classe de cor com base no status
            $status_class = '';
            switch (strtolower($pedido['status_atual'])) {
                case 'entregue':
                    $status_class = 'text-success';
                    break;
                case 'em preparo':
                case 'preparando':
                    $status_class = 'text-warning';
                    break;
                case 'em entrega':
                case 'saiu para entrega':
                    $status_class = 'text-info';
                    break;
                case 'cancelado':
                    $status_class = 'text-danger';
                    break;
                default:
                    $status_class = 'text-secondary';
            }
            
            // Busca os itens do pedido (limitado a 3 para exibição resumida)
            $query_itens = "SELECT i.nome, ip.quantidade
                           FROM itens_pedido ip
                           INNER JOIN itens i ON ip.cod_item = i.cod_item
                           WHERE ip.codigo_pedido = :pedido_id
                           LIMIT 3";
            $stmt_itens = $conn->prepare($query_itens);
            $stmt_itens->bindParam(':pedido_id', $pedido['cod_pedido'], PDO::PARAM_INT);
            $stmt_itens->execute();
            
            $itens_texto = '';
            $contador = 0;
            
            while ($item = $stmt_itens->fetch(PDO::FETCH_ASSOC)) {
                if ($contador > 0) {
                    $itens_texto .= ', ';
                }
                $itens_texto .= $item['quantidade'] . 'x ' . $item['nome'];
                $contador++;
            }
            
            // Se houver mais itens além dos 3 primeiros
            if ($pedido['quant_itens'] > $contador) {
                $itens_texto .= ' e mais ' . ($pedido['quant_itens'] - $contador) . ' item(ns)';
            }
            ?>
            <div class="card mb-3 order-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Pedido #<?php echo $pedido['cod_pedido']; ?> - <?php echo $data_pedido; ?> às <?php echo $hora_pedido; ?></span>
                    <span class="badge bg-secondary"><?php echo ucfirst($pedido['tipo_pagamento']); ?></span>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <strong>Status:</strong> 
                        <span class="<?php echo $status_class; ?>">
                            <?php echo ucfirst($pedido['status_atual']); ?>
                        </span>
                    </p>
                    <p class="card-text"><strong>Total:</strong> R$ <?php echo number_format($pedido['total_pedidos'], 2, ',', '.'); ?></p>
                    <p class="card-text"><strong>Itens:</strong> <?php echo $itens_texto; ?></p>
                    <a href="detalhe_pedido.php?id=<?php echo $pedido['cod_pedido']; ?>" class="btn btn-sm btn-info">
                        <i class="fas fa-receipt"></i> Ver Detalhes
                    </a>
                    <?php if (strtolower($pedido['status_atual']) == 'entregue'): ?>
                    <a href="repetir_pedido.php?id=<?php echo $pedido['cod_pedido']; ?>" class="btn btn-sm btn-outline-primary ms-2">
                        <i class="fas fa-redo"></i> Pedir Novamente
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Você ainda não realizou nenhum pedido.
        </div>
    <?php endif; ?>
</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação Excluir Conta -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão de Conta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Tem certeza de que deseja excluir sua conta permanentemente? Esta ação não pode ser desfeita.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger">Confirmar Exclusão</button> 
      </div>
    </div>
  </div>
</div>

<?php include_once "../rodape.html"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Ativa a funcionalidade das abas do Bootstrap
    var triggerTabList = [].slice.call(document.querySelectorAll('.list-group a'))
    triggerTabList.forEach(function (triggerEl) {
      var tabTrigger = new bootstrap.Tab(triggerEl)

      triggerEl.addEventListener('click', function (event) {
        if (!this.getAttribute('href').startsWith('http') && !this.getAttribute('href').startsWith('logout.php')) {
          event.preventDefault()
          tabTrigger.show()
        }
      })
    })

    // Garante que a primeira aba esteja ativa ao carregar
    var firstTabEl = document.querySelector('.list-group a:first-child')
    if (firstTabEl) {
      var firstTab = new bootstrap.Tab(firstTabEl)
      firstTab.show()
    }
    
    // Formatação do CPF
    function formatCPF(cpf) {
        if (!cpf) return '';
        cpf = cpf.replace(/\D/g, ''); // Remove tudo que não for número
        cpf = cpf.slice(0, 11); // Garante no máximo 11 dígitos
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        return cpf;
    }

    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        // Formata valor existente carregado do PHP
        if (cpfInput.value) {
            cpfInput.value = formatCPF(cpfInput.value);
        }

        // Aplica formatação enquanto digita
        cpfInput.addEventListener('input', function() {
            this.value = formatCPF(this.value);
        });
    }
    
    // Auto-fechamento dos alertas após 5 segundos
    window.addEventListener('DOMContentLoaded', (event) => {
        // Seleciona todos os alertas
        const alerts = document.querySelectorAll('.alert');
        
        // Para cada alerta, configura um timer para fechá-lo após 5 segundos
        alerts.forEach(alert => {
            setTimeout(() => {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }, 5000);
        });
    });
</script>
</body>
</html>
