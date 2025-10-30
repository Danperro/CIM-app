<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Incidencias</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <style>
        :root {
            --primary-green: #1e6b3a;
            --primary-green-dark: #155529;
            --primary-green-light: #2d8f57;
            --sidebar-bg: #1a1a1a;
            --sidebar-hover: rgba(30, 107, 58, 0.2);
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            display: flex;
            height: 100vh;
            position: relative;
        }

        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            transition: transform 0.3s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1050;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar .nav-link {
            color: #f8f9fa;
            padding: 0.875rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: var(--sidebar-hover);
            transform: translateX(2px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--primary-green);
            box-shadow: 0 2px 4px rgba(30, 107, 58, 0.3);
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Estilo específico para botones de colapso */
        .collapse-btn {
            color: #f8f9fa;
            padding: 0.875rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            border: none;
            background: none;
            display: flex;
            align-items: center;
            width: 100%;
            text-align: left;
        }

        .collapse-btn:hover {
            color: #fff;
            background-color: var(--sidebar-hover);
            transform: translateX(2px);
        }

        .collapse-btn i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .collapse-btn .chevron {
            transition: transform 0.3s ease;
        }

        .collapse-btn[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1rem 1rem 0.5rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .brand-link {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .brand-link i {
            color: var(--primary-green);
        }

        .content-area {
            flex: 1;
            margin-left: 280px;
            padding: 1.5rem;
            overflow-y: auto;
            transition: margin-left 0.3s ease-in-out;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .content-area.expanded {
            margin-left: 0;
        }

        .navbar-toggler {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1060;
            background-color: var(--primary-green);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .navbar-toggler:hover,
        .navbar-toggler:focus {
            background-color: var(--primary-green-dark);
            transform: scale(1.05);
            outline: none;
        }

        .navbar-toggler i {
            font-size: 1.25rem;
        }

        .dropdown-menu-dark {
            background-color: #2a2a2a;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-dropdown {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        .user-dropdown .dropdown-toggle {
            padding: 0.75rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }

        .user-dropdown .dropdown-toggle:hover {
            background-color: var(--sidebar-hover);
        }

        .overlay {
            display: none;
            pointer-events: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .overlay.show {
            display: block;
            pointer-events: auto;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0;
                padding: 4rem 1rem 1rem;
            }

            .navbar-toggler {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
            }

            .content-area {
                padding: 4rem 0.75rem 1rem;
            }
        }

        @media (max-width: 576px) {
            .brand-link .fs-4 {
                font-size: 1.1rem !important;
            }

            .sidebar .nav-link {
                padding: 0.75rem;
                font-size: 0.9rem;
            }

            .content-area {
                padding: 4rem 0.5rem 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Mobile Toggle Button -->
    <button class="navbar-toggler" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <div class="main-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="d-flex flex-column h-100">
                <!-- Brand -->
                <div class="brand-link text-center">
                    <a href="/Control" class="d-flex flex-column align-items-center text-white text-decoration-none">
                        <span class="fs-3 fw-bold" data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="Sistema de control de incidencias y mantenimientos a los equipos de cómputo">
                            <i class="bi bi-pc-display-horizontal fs-1 me-2"></i>
                            SCIM
                        </span>

                    </a>
                </div>


                <!-- Navigation -->
                <div class="flex-grow-1">
                    <ul class="nav nav-pills flex-column px-3" id="sidebarAccordion">
                        <!-- OPERACIONES -->
                        <li class="nav-item">
                            <button type="button" class="collapse-btn" data-bs-toggle="collapse"
                                data-bs-target="#grp-operaciones" aria-expanded="false" aria-controls="grp-operaciones">
                                <i class="bi bi-gear"></i> Operaciones
                                <i class="bi bi-chevron-down ms-auto chevron"></i>
                            </button>
                            <ul class="collapse list-unstyled ps-3" id="grp-operaciones">
                                <li>
                                    <a href="/Control" class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-speedometer2"></i> Realizar de Mantenimientos y Incidencias
                                    </a>
                                </li>
                                <li>
                                    <a href="/ConectividadIncidencia"
                                        class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-speedometer2"></i> Realizar Conectividad e Incidencias
                                    </a>
                                </li>
                                <li>
                                    <a href="/Mantenimientos"
                                        class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-tools"></i> Gestión de Mantenimientos
                                    </a>
                                </li>
                                <li>
                                    <a href="/Incidencias" class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-activity"></i> Gestión de Incidencias
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- REPORTES -->
                        <li class="nav-item">
                            <button type="button" class="collapse-btn" data-bs-toggle="collapse"
                                data-bs-target="#grp-reportes" aria-expanded="false" aria-controls="grp-reportes">
                                <i class="bi bi-gear"></i> Reportes
                                <i class="bi bi-chevron-down ms-auto chevron"></i>
                            </button>
                            <ul class="collapse list-unstyled ps-3" id="grp-reportes">
                                <li>
                                    <a href="/Reportes" class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Reportes de M. Preventivos
                                    </a>
                                </li>
                                <li>
                                    <a href="/ReportesConectividadIncidencia"
                                        class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Reportes de Conectividad e
                                        Incidencia
                                    </a>
                                </li>
                                <li>
                                    <a href="/ReportesIncidencias"
                                        class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Reportes de Incidencias
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- INVENTARIO -->
                        <li class="nav-item">
                            <button type="button" class="collapse-btn" data-bs-toggle="collapse"
                                data-bs-target="#grp-inventario" aria-expanded="false" aria-controls="grp-inventario">
                                <i class="bi bi-box-seam"></i> Inventario
                                <i class="bi bi-chevron-down ms-auto chevron"></i>
                            </button>
                            <ul class="collapse list-unstyled ps-3" id="grp-inventario">
                                <li>
                                    <a href="/Equipos" class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-laptop"></i> Gestión de Equipos
                                    </a>
                                </li>
                                <li>
                                    <a href="/Perifericos" class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-keyboard"></i> Gestión de Periféricos
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- ESPACIOS -->
                        <li class="nav-item">
                            <button type="button" class="collapse-btn" data-bs-toggle="collapse"
                                data-bs-target="#grp-espacios" aria-expanded="false" aria-controls="grp-espacios">
                                <i class="bi bi-building"></i> Espacios
                                <i class="bi bi-chevron-down ms-auto chevron"></i>
                            </button>
                            <ul class="collapse list-unstyled ps-3" id="grp-espacios">
                                <li>
                                    <a href="/Laboratorios"
                                        class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-building"></i> Gestión de Laboratorios
                                    </a>
                                </li>
                                <li>
                                    <a href="/Areas" class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-diagram-3"></i> Gestión de Áreas
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- ADMINISTRACIÓN -->
                        <li class="nav-item">
                            <button type="button" class="collapse-btn" data-bs-toggle="collapse"
                                data-bs-target="#grp-admin" aria-expanded="false" aria-controls="grp-admin">
                                <i class="bi bi-shield-lock"></i> Administración
                                <i class="bi bi-chevron-down ms-auto chevron"></i>
                            </button>
                            <ul class="collapse list-unstyled ps-3" id="grp-admin">
                                <li>
                                    <a href="/Usuarios" class="nav-link text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-people"></i> Gestión de Usuarios
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <div class="sidebar-heading">
                            Otras opciones
                        </div>

                        <li class="nav-item">
                            <a href="/Ayuda" class="nav-link text-white">
                                <i class="bi bi-question-circle"></i>
                                Ayuda
                            </a>
                        </li>


                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="user-dropdown">
                    <div class="dropdown">
                        <a href="#"
                            class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5 me-2"></i>
                            <div class="d-flex flex-column">
                                <strong class="small">Usuario Admin</strong>
                                <small class="text-white-50">Administrador</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark shadow">
                            <li><a class="dropdown-item" href="/Perfil"><i class="bi bi-person me-2"></i>Mi
                                    perfil</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content area -->
        <div class="content-area">
            {{ $slot }}
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const contentArea = document.querySelector('.content-area');

            function openMobile() {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            }

            function closeMobile() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }

            function toggleMobile() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

            function openDesktop() {
                sidebar.classList.remove('collapsed');
                contentArea?.classList.remove('expanded');
            }

            function closeDesktop() {
                sidebar.classList.add('collapsed');
                contentArea?.classList.add('expanded');
            }

            function toggleDesktop() {
                sidebar.classList.toggle('collapsed');
                contentArea?.classList.toggle('expanded');
            }

            // Click del botón de toggle del sidebar
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.innerWidth >= 992) {
                        toggleDesktop();
                    } else {
                        toggleMobile();
                    }
                });
            }

            // Cerrar al tocar overlay en móvil
            overlay?.addEventListener('click', closeMobile);

            // Al redimensionar, limpia estados móviles
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    closeMobile();
                    openDesktop();
                } else {
                    sidebar.classList.remove('collapsed');
                    contentArea?.classList.remove('expanded');
                    closeMobile();
                }
            });

            // Estado inicial
            if (window.innerWidth >= 992) {
                openDesktop();
            } else {
                closeMobile();
            }

            // === Activar link actual (match más específico) ===
            const current = location.pathname.replace(/\/+$/, '') || '/';
            const links = Array.from(document.querySelectorAll('#sidebar a.nav-link[href]'));

            // Limpia estados previos
            links.forEach(a => a.classList.remove('active'));

            function normalize(p) {
                if (!p) return '/';
                p = p.replace(/\/+$/, '');
                return p === '' ? '/' : p;
            }

            function pathMatches(currentPath, href) {
                currentPath = normalize(currentPath);
                href = normalize(href);
                if (currentPath === href) return true;
                // Debe coincidir por segmento completo: /Reportes vs /ReportesIncidencias NO
                // pero /Reportes/Algo SÍ coincide con /Reportes
                return currentPath.startsWith(href + '/');
            }

            // Elige el mejor match por:
            // 1) exacto; si hay varios, el más largo
            // 2) si no hay exacto, el match por segmento con href más largo
            let best = null;
            for (const a of links) {
                const href = a.getAttribute('href');
                if (!href || href === '#') continue;

                if (pathMatches(current, href)) {
                    const exact = normalize(current) === normalize(href);
                    const score = (exact ? 1 : 0) * 1000 + normalize(href).length; // exacto gana
                    if (!best || score > best.score) {
                        best = {
                            el: a,
                            score,
                            href: normalize(href)
                        };
                    }
                }
            }

            // Si no hay match, usa /Control como fallback
            if (!best) {
                best = {
                    el: document.querySelector('#sidebar a.nav-link[href="/Control"]') || null,
                    score: 0
                };
            }

            if (best && best.el) {
                best.el.classList.add('active');

                // Abre su grupo colapsable padre sin crear instancias duplicadas
                const grp = best.el.closest('.collapse');
                if (grp && !grp.classList.contains('show')) {
                    grp.classList.add('show');
                    const triggerBtn = document.querySelector(
                        `[data-bs-toggle="collapse"][data-bs-target="#${grp.id}"]`);
                    if (triggerBtn) triggerBtn.setAttribute('aria-expanded', 'true');
                }
            }

            // Fallback: si no hubo match y estás en raíz, activa Control
            if (!matched && (current === '' || current === '/')) {
                const ctrl = document.querySelector('#sidebar a.nav-link[href="/Control"]');
                if (ctrl) {
                    ctrl.classList.add('active');
                    const grp = ctrl.closest('.collapse');
                    if (grp && !grp.classList.contains('show')) {
                        const triggerBtn = document.querySelector(
                            `[data-bs-toggle="collapse"][data-bs-target="#${grp.id}"]`
                        );
                        if (triggerBtn) {
                            // Mostrar el collapse sin crear una nueva instancia
                            grp.classList.add('show');
                            triggerBtn.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            }
        });
    </script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

</body>

</html>
