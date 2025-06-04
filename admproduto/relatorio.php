<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brutus";

// Conexão com o banco
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Carrega categorias para o select
$categorias = [];
$cat_sql = "SELECT cod_categoria, nome FROM categoria";
$cat_result = $conn->query($cat_sql);
if ($cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// Verifica se foi selecionada uma categoria
$filtro_categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;

// Monta a consulta SQL
$sql = "SELECT 
            i.nome AS nome_produto,
            REPLACE(i.preco, ',', '.') AS preco_unitario,
            SUM(ip.quantidade) AS total_vendido
        FROM 
            itens_pedido AS ip
        JOIN 
            itens AS i ON ip.cod_item = i.cod_item ";

if ($filtro_categoria > 0) {
    $sql .= "WHERE i.fk_Categoria_cod_categoria = $filtro_categoria ";
}

$sql .= "GROUP BY i.nome, i.preco
         ORDER BY total_vendido DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Produtos Mais Vendidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: white;
            margin: 20px;
            line-height: 1.6;
        }

        h1, h2 {
            font-weight: normal;
            color: rgb(211, 108, 35);
            text-align: center;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        select, input[type="submit"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-left: 5px;
        }

        table {
            width: 90%;
            margin: 0 auto;
            background: white;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: rgb(211, 108, 35);
            color: white;
        }

        tr:last-child td {
            border-bottom: none;
        }

        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: rgb(211, 108, 35);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: saddlebrown;
        }
    </style>
</head>
<body>

<h2>Relatório de Produtos Mais Vendidos</h2>

<!-- Formulário de filtro -->
<form method="GET">
    <label for="categoria">Filtrar por Categoria:</label>
    <select name="categoria" id="categoria">
        <option value="0">Todas</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['cod_categoria'] ?>" <?= ($cat['cod_categoria'] == $filtro_categoria) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Filtrar">
</form>

<!-- Tabela de resultados -->
<table>
    <tr>
        <th>Produto</th>
        <th>Quantidade Vendida</th>
        <th>Preço Unitário (R$)</th>
        <th>Valor Total (R$)</th>
    </tr>

    <?php
    $cor = "white";
    if ($result && $result instanceof mysqli_result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $preco_unitario = floatval($row['preco_unitario']);
                $total_vendido = intval($row['total_vendido']);
                $valor_total = number_format($preco_unitario * $total_vendido, 2, ',', '.');
                $preco_formatado = number_format($preco_unitario, 2, ',', '.');

            echo "<tr bgcolor='$cor'>
                    <td>{$row['nome_produto']}</td>
                    <td>{$total_vendido}</td>
                    <td>R$ {$preco_formatado}</td>
                    <td>R$ {$valor_total}</td>
                  </tr>";

            $cor = ($cor == "white") ? "navajowhite" : "white";
        }
    } else {
        echo "<tr><td colspan='4'>Nenhum registro encontrado.</td></tr>";
    } }
    ?>
</table>

<button onclick="window.print()">Imprimir / Salvar em PDF</button>

<canvas id="grafico" width="800" height="400"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafico').getContext('2d');
    const grafico = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                <?php
                mysqli_data_seek($result, 0);
                while ($row = $result->fetch_assoc()) {
                    echo "'" . addslashes($row['nome_produto']) . "',";
                }
                ?>
            ],
            datasets: [{
                label: 'Quantidade Vendida',
                data: [
                    <?php
                    mysqli_data_seek($result, 0);
                    while ($row = $result->fetch_assoc()) {
                        echo "{$row['total_vendido']},";
                    }
                    ?>
                ],
                backgroundColor: 'rgba(211, 108, 35)',
                borderColor: 'rgba(211, 108, 35)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
