<?php include ("../usuario.php");?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Clientes</title>
</head>
<body>
    <div class="container">
        <h1>Clientes</h1>
        <a href="../index.php" class="btn">Volver al inicio</a>
        <table>
            <thead>
                <h1>crear usuario </h1>
                <a href="crear_cliente.php" class="btn">Crear cliente</a>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                    </tr>
                </table>
            </thead>
            <tbody>
                <?php
                $connect = connection();
                $query = "SELECT * FROM clientes";
                $result = mysqli_query($connect, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['nombre'] . "</td>";
                    echo "<td>" . $row['apellido'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['telefono'] . "</td>";
                    echo "</tr>";
                }

                mysqli_close($connect);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>