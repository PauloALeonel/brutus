<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brutus";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_pedido = $_POST['cod_pedido'];
    $novo_status = $_POST['novo_status'];
    
    $sql = "UPDATE pedidos SET fk_Status_Pedidos_cod_status_pedidos = ? WHERE cod_pedido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $novo_status, $cod_pedido);

    if ($stmt->execute()) {
        echo "<p>Status do pedido atualizado com sucesso!</p>";
    } else {
        echo "<p>Erro ao atualizar status: " . $conn->error . "</p>";
    }

    $stmt->close();
}

// Obter lista de pedidos
$sqlPedidos = "SELECT p.cod_pedido, p.datahora_pedido, p.total_pedidos, s.status_pedidos 
               FROM pedidos p
               LEFT JOIN status_pedidos s ON p.fk_Status_Pedidos_cod_status_pedidos = s.cod_status_pedidos";
$resultPedidos = $conn->query($sqlPedidos);

// Obter lista de status disponíveis
$sqlStatus = "SELECT cod_status_pedidos, status_pedidos FROM status_pedidos";
$resultStatus = $conn->query($sqlStatus);
$statusOpcoes = [];
while ($row = $resultStatus->fetch_assoc()) {
    $statusOpcoes[$row['cod_status_pedidos']] = $row['status_pedidos'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STATUS | BRUTUS</title>
    <link rel="icon" href="img\favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="custom.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="custom.css"> 
</head>
<body>
<?php include_once "../cabecalho.html"; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhar e Alterar Status de Pedidos</title>
</head>
<body>
    <h1>Acompanhar e Alterar Status de Pedidos</h1>

    <table border="1">
        <tr>
            <th>Código do Pedido</th>
            <th>Data/Hora</th>
            <th>Total</th>
            <th>Status Atual</th>
            <th>Mudar Status</th>
        </tr>
        <?php
        if ($resultPedidos->num_rows > 0) {
            while ($row = $resultPedidos->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['cod_pedido'] . "</td>";
                echo "<td>" . $row['datahora_pedido'] . "</td>";
                echo "<td>R$ " . number_format($row['total_pedidos'], 2, ',', '.') . "</td>";
                echo "<td>" . $row['status_pedidos'] . "</td>";
                echo "<td>
                    <form method='POST'>
                        <input type='hidden' name='cod_pedido' value='" . $row['cod_pedido'] . "'>
                        <select name='novo_status'>";
                            foreach ($statusOpcoes as $cod => $status) {
                                echo "<option value='$cod'>$status</option>";
                            }
                        echo "</select>
                        <button type='submit'>Atualizar</button>
                    </form>
                </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Nenhum pedido encontrado</td></tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
$conn->close();
?>

