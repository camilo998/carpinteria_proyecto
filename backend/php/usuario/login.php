<?php
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Por favor complete todos los campos';
    } else {
        // Buscar usuario en la base de datos
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Iniciar sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            // Redirigir a productos
            header('Location: index.php');
            exit;
        } elseif ($usuario && !password_verify($password, $usuario['password'])) {
            // Verificar contraseña sin encriptar (para pruebas)
            if ($password === $usuario['password']) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Contraseña incorrecta';
            }
        } else {
            $error = 'Usuario no encontrado o inactivo';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Carpintería Don Gusto</title>
    <link rel="icon" href="../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="../../../frontend/css/producto_estile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #FAF0E6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background-color: #FFF8DC;
            border: 2px solid #D2B48C;
            border-radius: 10px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .login-container h1 {
            color: #8B4513;
            text-align: center;
            font-family: 'Arial', sans-serif;
            margin-bottom: 10px;
        }
        
        .login-container p {
            color: #A0522D;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-group label {
            color: #8B4513;
            font-weight: bold;
        }
        
        .form-control {
            border: 1px solid #D2B48C;
            background-color: #FAF0E6;
        }
        
        .form-control:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }
        
        .login-btn {
            background-color: #F4A460;
            color: #8B4513;
            border: 1px solid #8B4513;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
            width: 100%;
            font-weight: bold;
        }
        
        .login-btn:hover {
            background-color: #CD853F;
            color: white;
        }
        
        .demo-users {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #D2B48C;
        }
        
        .demo-users h3 {
            color: #8B4513;
            font-size: 16px;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .demo-user {
            background-color: #FFF8DC;
            border: 1px solid #D2B48C;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .demo-user:hover {
            background-color: #D2B48C;
        }
        
        .demo-user strong {
            color: #8B4513;
        }
        
        .error {
            color: #c0392b;
            background: #fadbd8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            color: #1e8449;
            background: #d5f5e3;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🪵 Carpintería Don Gusto</h1>
        <p>Inicia sesión para comprar productos</p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Iniciar Sesión</button>
        </form>
        
        <div class="demo-users">
            <h3>👤 Usuarios de Prueba:</h3>
            
            <div class="demo-user" onclick="autoLogin('usuario@correo.com', 'usuario123')">
                <strong>USUARIO</strong><br>
                Email: usuario@correo.com<br>
                Contraseña: usuario123
            </div>
            
            <div class="demo-user" onclick="autoLogin('juan@correo.com', 'juan123')">
                <strong>JUAN PÉREZ</strong><br>
                Email: juan@correo.com<br>
                Contraseña: juan123
            </div>

            <div class="demo-user" onclick="autoLogin('maria@correo.com', 'maria123')">
                <strong>MARÍA GONZÁLEZ</strong><br>
                Email: maria@correo.com<br>
                Contraseña: maria123
            </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="../admin/login.php" style="color: #8B4513; text-decoration: none;">🔧 Ir a Login de Administrador</a>
        </div>
    
    <script>
        function autoLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>
