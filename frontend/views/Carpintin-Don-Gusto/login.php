<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="img/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="../../css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Carpintería Don Gusto</title>
</head>
<body>
    <div class="login-container">
        <h1>🪵 Carpintería Don Gusto</h1>
        <p>Inicia sesión para continuar</p>
        
        <div class="error" id="errorMsg"></div>
        
        <form id="loginForm" method="POST" action="../../../backend/php/usuario/login.php">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Iniciar Sesión</button>
        </form>
        
        <div class="nav-links">
            <a href="index.php">Volver al inicio</a> | 
            <a href="sobre-nosotros.php">Sobre Nosotros</a>
        </div>
        
        <div class="demo-users">
            <h3>👤 Usuarios de Prueba:</h3>
            
            <div class="demo-user">
                <strong>ADMINISTRADOR</strong><br>
                Email: admin@carpinteria.com<br>
                Contraseña: admin123
            </div>
            
            <div class="demo-user">
                <strong>USUARIO</strong><br>
                Email: usuario@correo.com<br>
                Contraseña: usuario123
            </div>
    </div>
</body>
</html>
