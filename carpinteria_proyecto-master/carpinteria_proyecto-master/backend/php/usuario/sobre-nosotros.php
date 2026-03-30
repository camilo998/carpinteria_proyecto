<?php
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="../../../frontend/css/sobre-nosotros.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros - Carpintería Don Gusto</title>
</head>
<body>
    
 <header>
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">
                <img class="foto" src="../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg" alt="Logotipo de Carpintín Don Gusto" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar" aria-controls="mynavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mynavbar">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Productos</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="sobre-nosotros.php">Sobre Nosotros</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">Admin: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
                </div>
            </div> <!-- cierre collapse navbar -->
        </div> <!-- cierre container-fluid -->
    </nav>
</header>

<div class="container mt-1">
    <h1 class="h12">Bienvenido a Carpintín Don Gusto</h1>
    <p>En Carpintín Don Gusto, entendemos que cada hogar es único y que cada pieza de mobiliario debe reflejar la personalidad y las necesidades de nuestros clientes.</p>
    <p>Nos especializamos en la creación de muebles artesanales de alta calidad, diseñados específicamente para satisfacer los gustos y requisitos individuales de cada cliente.</p>
</div>

<div class="container mt-1">
    <div class="accordion" id="accordionExample">

        <!-- Item 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    ¿Quiénes Somos?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>En Carpintín Don Gusto, somos apasionados por la carpintería artesanal...</p>
                            <p>Nuestra misión es ayudarte a transformar tus ideas en realidad...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Descubre la Variedad de Nuestros Muebles
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="h12">Mesas Personalizadas</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/mesa2.jpg" alt="Mesas personalizadas" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Ya sea que busques una mesa de comedor robusta...</p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h12">Estanterías y Almacenamiento</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/mueble.jpg" alt="Estanterías" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Optimiza tu espacio con estanterías...</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="h12">Decoraciones para el Hogar</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/espejo.jpg" alt="Decoraciones" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Añade un toque especial a tu hogar...</p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h12">Escritorios y Muebles de Oficina</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/escritorio.webp" alt="Escritorios" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Desde escritorios funcionales para tu oficina...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    ¿Cómo Trabajamos?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="h12">Consulta Personalizada</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/Comedor-Rustico.jpg" alt="Consulta personalizada" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Nos reunimos contigo para comprender tus necesidades...</p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h12">Fabricación Artesanal</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/mueble2.jpg" alt="Fabricación artesanal" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Nuestros artesanos altamente cualificados...</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="h12">Diseño a Medida</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/mesa3.png" alt="Diseño a medida" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Creamos diseños personalizados basados en tus especificaciones...</p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h12">Entrega y Montaje</h3>
                            <img src="../../../frontend/views/Carpintin-Don-Gusto/img/mesa_de.webp" alt="Entrega y montaje" class="img-fluid rounded mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                            <p>Nos aseguramos de que tus muebles lleguen en perfecto estado...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- cierre accordion -->
</div> <!-- cierre container -->   
    <p class="piramide">
        En Carpintín Don Gusto,<br>
        no solo fabricamos muebles;<br>
        creamos piezas que cuentan historias<br>
        y se convierten en parte de tu hogar.<br>
        ¡Déjanos ayudarte a transformar tus ideas en realidad!
    </p>

</body>
</html>
