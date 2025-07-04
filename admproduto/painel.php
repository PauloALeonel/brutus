<?php
include_once "conexao.php";

// CADASTRAR
if (isset($_POST["btn_cadastrar"])) {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];
    $erros = [];

    // Validações básicas
    if (empty($nome)) $erros[] = "Nome do produto é obrigatório";
    if (!is_numeric($preco) || $preco <= 0) $erros[] = "Preço inválido";
    // Adicione outras validações conforme necessário

    // Processamento da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $diretorio = '../produtos/';
        $nomeImagem = uniqid() . '.' . $extensao;

        if (!is_dir($diretorio)) {
            if (!mkdir($diretorio, 0777, true)) {
                $erros[] = "Falha ao criar diretório para imagens";
            }
        }

        if (in_array($extensao, $extensoesPermitidas)) {
            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio . $nomeImagem)) {
                $erros[] = "Erro ao mover o arquivo para o diretório de destino";
            } else {
                $caminhoimagem = $nomeImagem;
            }
        } else {
            $erros[] = "Formato de imagem não permitido. Use JPG, JPEG, PNG ou GIF";
        }
    } else {
        $erros[] = "Erro no envio da imagem ou imagem não selecionada";
    }

    // Se não houver erros, procede com o cadastro
    if (empty($erros)) {
        $sqlProduto = $conn->prepare("INSERT INTO itens (nome, preco, descricao, imagem, fk_Categoria_cod_categoria) VALUES (?, ?, ?, ?, ?)");
        
        if ($sqlProduto) {
            $sqlProduto->bind_param('sdsss', $nome, $preco, $descricao, $caminhoimagem, $categoria);
            
            if ($sqlProduto->execute()) {
                echo "<div class='alert alert-success text-center'>Produto cadastrado com sucesso!</div>";
                // Limpar os campos do formulário ou redirecionar
                $nome = $preco = $descricao = '';
            } else {
                $erros[] = "Erro ao cadastrar o produto: " . $sqlProduto->error;
            }
            
            $sqlProduto->close();
        } else {
            $erros[] = "Erro ao preparar a consulta: " . $conn->error;
        }
    }
    
    // Exibir erros se houver
    if (!empty($erros)) {
        foreach ($erros as $erro) {
            echo "<div class='alert alert-danger text-center'>$erro</div>";
        }
    }
}

// EDITAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btn_editar"])) {
    $id_item = $_POST["id_item"];
    $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
    $preco = mysqli_real_escape_string($conn, str_replace(',', '.', $_POST['preco']) );
    $descricao = mysqli_real_escape_string($conn, $_POST["descricao"]);
    $categoria = $_POST["categoria"];
    $pedido = isset($_POST["pedido"]) ? $_POST["pedido"] : null;

    $caminhoimagem = "";
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $imagem_nome = $_FILES["imagem"]["name"];
        $imagem_temp = $_FILES["imagem"]["tmp_name"];
        $extensao = strtolower(pathinfo($imagem_nome, PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extensao, $extensoesPermitidas)) {
            $nomeImagem = uniqid() . '.' . $extensao;
            $diretorio = '../produtos/';

            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            if (move_uploaded_file($imagem_temp, $diretorio . $nomeImagem)) {
                $caminhoimagem = $nomeImagem;
            } else {
                die("<div class='alert alert-danger text-center'>Erro ao salvar a imagem.</div>");
            }
        } else {
            die("<div class='alert alert-danger text-center'>Formato de imagem não permitido.</div>");
        }
    }

    if (!empty($caminhoimagem)) {
        $sqlProduto = $conn->prepare("UPDATE itens SET nome = ?, preco = ?, descricao = ?, imagem = ?, fk_Categoria_cod_categoria = ?, fk_Pedidos_cod_pedido = ? WHERE cod_item = ?");
        $sqlProduto->bind_param('sdsssii', $nome, $preco, $descricao, $caminhoimagem, $categoria, $pedido, $id_item);
    } else {
        $sqlProduto = $conn->prepare("UPDATE itens SET nome = ?, preco = ?, descricao = ?, fk_Categoria_cod_categoria = ?, fk_Pedidos_cod_pedido = ? WHERE cod_item = ?");
        $sqlProduto->bind_param('sdsssi', $nome, $preco, $descricao, $categoria, $pedido, $id_item);
    }

    if ($sqlProduto->execute()) {
        echo "<div class='alert alert-success text-center'>Produto editado com sucesso!</div>";
        header("Location: painel.php");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>Erro ao editar o produto: " . $sqlProduto->error . "</div>";
    }
}

// EXCLUIR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_exclusao']) && isset($_POST['cod_item']) && is_numeric($_POST['cod_item'])) {
    $id_item = $_POST['cod_item'];

    $sqlExcluir = $conn->prepare("DELETE FROM itens WHERE cod_item = ?");
    $sqlExcluir->bind_param('i', $id_item);

    if ($sqlExcluir->execute()) {
        header("Location: painel.php?mensagem=Produto excluído com sucesso");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>Erro ao excluir o produto: " . $sqlExcluir->error . "</div>";
    }

    $sqlExcluir->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Administrador | BRUTUS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="painel.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background: white;
        }

        h1 {
            font-weight: normal;
        }
        
        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        ul li a {
            text-decoration: none;
            color: rgb(211, 108, 35);
            font-size: 20px;
        }

        ul li a:hover {
            color: saddlebrown; 
        }

        .section {
            display: none; 
        }

        .section.active {
            display: block; 
        }
     table {
        width: 100%;
        border-spacing: 0; /* permite arredondamento */
        margin-top: 20px;
        border-radius: 30px;
        overflow: hidden;
        background-color: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    select {
        padding: 5px;
        font-size: 16px;
    }
    </style>
    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));

            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.add('active');
            }
        }

        function hideSections() {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));
        }

        function previewImagem() {
            var imagem = document.querySelector('input[name=imagem]').files[0];
            var preview = document.querySelector('img#imgPreview');
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.src = reader.result;
            }

            if (imagem) {
                reader.readAsDataURL(imagem);
            } else {
                preview.src = "";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('dados-pessoais').classList.add('active');
        });
    </script>
</head>
<body>
    <h1>Olá, administrador!</h1>
    <ul class="font">
        <li><a href="#" onclick="showSection('cadastro_pro')">Cadastro de Produto</a></li>
        <li><a href="#" onclick="showSection('produtos')">Produtos</a></li>
        <li><a href="relatorio.php">Relatorio Itens</a></li>
        <li><a href="#" onclick="showSection('estado')">Rastreio</a></li>
        <a href="logout.php">
        <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    <div id="cadastro_pro" class="section container">
        <h2>Cadastro de Produto</h2>
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nome do Produto</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Preço</label>
                <input type="number" name="preco" step="0.01" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3" required></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Imagem</label>
                <input type="file" name="imagem" class="form-control" accept="image/*" onchange="previewImagem()" required>
                <img id="imgPreview" class="img-preview" src="">
            </div>
   <?php         
// Buscar categorias
$sql = "SELECT cod_categoria, nome FROM categoria";
$result = $conn->query($sql);
?>

<div class="col-md-6">
    <label class="form-label">Categoria</label>
    <select name="categoria" class="form-select">
        <option value="">Selecione...</option>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['cod_categoria'] . '">' . htmlspecialchars($row['nome']) . '</option>';
            }
        } else {
            echo '<option value="">Nenhuma categoria encontrada</option>';
        }
        ?>
    </select>
</div>

            <div class="col-12">
                <button type="submit" name="btn_cadastrar" class="btn btn-warning btn-sm">Cadastrar Produto</button>
            </div>
        </form>
    </div>
    <div id="produtos" class="section container">
        <h2>Produtos Cadastrados</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
            <?php
            $sql = "SELECT * FROM itens";
            $res = $conn->query($sql);

            if ($res && $res->num_rows > 0):
                while ($item = $res->fetch_assoc()):
            ?>
            <div class="col">
                <div class="card h-100">
                <img src="../produtos/<?php echo htmlspecialchars($item['imagem']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['nome']); ?>">
                <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['nome']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($item['descricao']); ?></p>
                        <p class="card-text text-success fw-bold">R$ <?php echo $item['preco']; ?></p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-warning btn-sm" onclick="showSection('editar_produto_<?php echo $item['cod_item']; ?>')">
                            <i class="fas fa-edit"></i> 
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showSection('excluir_produto_<?php echo $item['cod_item']; ?>')">
                            <i class="fas fa-trash-alt"></i> 
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div class="alert alert-info">Nenhum produto cadastrado.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $sql = "SELECT * FROM itens";
    $res = $conn->query($sql);
    while ($item = $res->fetch_assoc()):
    ?>
    <div id="editar_produto_<?php echo $item['cod_item']; ?>" class="section container">
    <h2>Editar Produto</h2>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id_item" value="<?php echo $item['cod_item']; ?>">

        <div class="col-md-6">
            <label class="form-label">Nome do Produto</label>
            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($item['nome']); ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Preço</label>
            <input type="number" name="preco" step="0.01" class="form-control" value="<?php echo number_format((float)$item['preco'], 2, '.', ''); ?>" required>
        </div>

        <div class="col-12">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3" required><?php echo htmlspecialchars($item['descricao']); ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Imagem</label>
            <input type="file" name="imagem" class="form-control" accept="image/*" onchange="previewImagem()">
            <img id="imgPreview" class="img-preview mt-2" src="../produtos/<?php echo htmlspecialchars($item['imagem']); ?>" alt="Imagem atual do produto" style="max-width: 100px;">
        </div>

        <div class="col-md-6">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select">
                <option value="1" <?php echo ($item['fk_Categoria_cod_categoria'] == 1) ? 'selected' : ''; ?>>Hamburguer</option>
                <option value="2" <?php echo ($item['fk_Categoria_cod_categoria'] == 2) ? 'selected' : ''; ?>>Kids</option>
                <option value="3" <?php echo ($item['fk_Categoria_cod_categoria'] == 3) ? 'selected' : ''; ?>>Combos</option>
                <option value="4" <?php echo ($item['fk_Categoria_cod_categoria'] == 4) ? 'selected' : ''; ?>>Acompanhamentos</option>
                <option value="5" <?php echo ($item['fk_Categoria_cod_categoria'] == 5) ? 'selected' : ''; ?>>Bebidas</option>
                <option value="6" <?php echo ($item['fk_Categoria_cod_categoria'] == 6) ? 'selected' : ''; ?>>Sobremesa</option>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" name="btn_editar" class="btn btn-warning w-100">Salvar Alterações</button>
        </div>
    </form>
</div>
    <?php endwhile; ?>
    <?php
    $sql = "SELECT * FROM itens";
    $res = $conn->query($sql);
    while ($item = $res->fetch_assoc()):
    ?>
        <div id="excluir_produto_<?php echo $item['cod_item']; ?>" class="section container">
            <h2>Excluir Produto</h2>
            <p>Tem certeza que deseja excluir o produto "<?php echo htmlspecialchars($item['nome']); ?>"?</p>
            <form method="POST" action="painel.php">
                <input type="hidden" name="cod_item" value="<?php echo $item['cod_item']; ?>">
                <button type="submit" name="confirmar_exclusao" class="btn btn-danger">Excluir</button>
                <button type="button" class="btn btn-secondary" onclick="hideSections()">Cancelar</button>
            </form>
        </div>
    <?php endwhile; ?>
<!-- RASTREIO -->

<div id="estado" class="section container">
<?php

$sql = "SELECT 
    p.cod_pedido, 
    p.datahora_pedido, 
    p.total_pedidos, 
    p.quant_itens, 
    u.nome AS cliente, 
    sp.status_pedidos AS status_atual, 
    hs.cod_status AS cod_status_pedidos
FROM 
    pedidos p
JOIN 
    usuario u ON u.codigo = p.fk_Usuario_codigo
JOIN 
    (
        SELECT 
            h1.cod_pedido, 
            h1.cod_status
        FROM 
            hist_status_ped h1
        INNER JOIN (
            SELECT 
                cod_pedido, 
                MAX(data_hora) AS max_data
            FROM 
                hist_status_ped
            GROUP BY 
                cod_pedido
        ) h2 
        ON h1.cod_pedido = h2.cod_pedido AND h1.data_hora = h2.max_data
    ) hs 
    ON hs.cod_pedido = p.cod_pedido
JOIN 
    status_pedidos sp 
    ON sp.cod_status_pedidos = hs.cod_status
ORDER BY 
    p.cod_pedido DESC;
";

$result = $conn->query($sql);
?>

<h2>Lista de Pedidos</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Data</th>
            <th>Total</th>
            <th>Itens</th>
            <th>Status Atual</th>
            <th>Alterar Status</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['cod_pedido'] ?></td>
            <td><?= htmlspecialchars($row['cliente']) ?></td>
            <td><?= $row['datahora_pedido'] ?></td>
            <td>R$ <?= number_format($row['total_pedidos'], 2, ',', '.') ?></td>
            <td><?= $row['quant_itens'] ?></td>
            <td><?= $row['status_atual'] ?></td>
            <td>
                <form method="POST" action="alterar_estado.php">
                    <input type="hidden" name="cod_pedido" value="<?= $row['cod_pedido'] ?>">
                    <select name="cod_status">
    <?php
    $status_sql = "SELECT cod_status_pedidos, status_pedidos FROM status_pedidos";
    $status_result = $conn->query($status_sql);
    while ($status = $status_result->fetch_assoc()):
    ?>
        <option value="<?= $status['cod_status_pedidos'] ?>"
            <?= ($status['cod_status_pedidos'] == $row['cod_status_pedidos']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($status['status_pedidos']) ?>
        </option>
    <?php endwhile; ?>
</select>
                    <button type="submit" class="btn-warning">Atualizar</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

