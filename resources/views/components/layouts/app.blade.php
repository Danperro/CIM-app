<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Matrícula</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        
        .sidebar {
            width: 280px;
            background-color: #212529;
        }
        
        .sidebar .nav-link {
            color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 0.2rem;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        
        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 1rem;
        }
        
        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
    </style>
</head>
<body style="height:100vh">
    <div class="h-100" style="display: flex; flex-direction: row;">
        <!-- Sidebar -->
        <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark sidebar h-100">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="bi bi-mortarboard-fill fs-4 me-2"></i>
                <span class="fs-4 fw-bold">CIM</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="/Control" class="nav-link text-white" aria-current="page">
                        <i class="bi bi-people-fill"></i>
                        Control
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/Equipos" class="nav-link text-white">
                        <i class="bi bi-book-fill"></i>
                        Gestion de Equipos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/Usuarios" class="nav-link text-white">
                        <i class="bi bi-book-fill"></i>
                        Gestion de Usuarios
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="/Reportes" class="nav-link text-white">
                        <i class="bi bi-book-fill"></i>
                        Reportes
                    </a>
                </li>
                <li>
                    <div class="sidebar-heading">
                        Otras opciones
                    </div>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-gear-fill"></i>
                        Configuración
                    </a>
                </li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <strong>Usuario</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                    <li><a class="dropdown-item" href="#">Preferencias</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Content area -->
        <div class="content-area">
            {{$slot}}
        </div>
    </div>

    @livewireScripts
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para marcar como activo el enlace de la página actual
        document.addEventListener('DOMContentLoaded', function() {
            const currentLocation = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href !== '#' && currentLocation.includes(href)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>