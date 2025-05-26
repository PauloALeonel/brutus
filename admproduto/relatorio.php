<?php
include_once "conexao.php";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Conexão falhou: ' . $conn->connect_error);
}

$sql = "SELECT p.id, p.nome, SUM(v.quantidade) AS total_vendido
        FROM produtos p
        INNER JOIN vendas v ON p.id = v.produto_id
        GROUP BY p.id, p.nome
        ORDER BY total_vendido DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Relatório de Itens Mais Vendidos</h2>";
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Total Vendido</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>" . $row['total_vendido'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Nenhuma venda registrada.";
}

$conn->close();

?>
