<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - SCIM | UNIA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-primary: #1a472a;
            --color-secondary: #2d5a3d;
            --color-accent: #4a9f62;
            --color-light: #f8fffe;
            --color-white: #ffffff;
            --color-gray-100: #f5f5f5;
            --color-gray-200: #e5e5e5;
            --color-gray-300: #d4d4d4;
            --color-gray-600: #525252;
            --color-gray-800: #1f2937;
            --color-success: #10b981;
            --color-warning: #f59e0b;
            --color-danger: #ef4444;

            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);

            --border-radius-sm: 0.375rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 0.75rem;

            --font-family-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
        }

        body {
            font-family: var(--font-family-sans);
            line-height: 1.6;
            color: var(--color-gray-800);
            background-color: var(--color-gray-100);
            min-height: 100vh;
        }

        .ayuda-page {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--color-white);
            min-height: 100vh;
            box-shadow: var(--shadow-lg);
        }

        /* Header */
        .ayuda-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
            color: var(--color-white);
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ayuda-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="80" cy="30" r="1.5" fill="rgba(255,255,255,0.08)"/></svg>') repeat;
            animation: float 20s ease-in-out infinite;
            z-index: 1;
        }

        .ayuda-header h1 {
            font-size: var(--font-size-4xl);
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .ayuda-header p {
            font-size: var(--font-size-lg);
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        /* Sections */
        .ayuda-section {
            padding: 2.5rem;
            border-bottom: 1px solid var(--color-gray-200);
        }

        .ayuda-section:last-of-type {
            border-bottom: none;
        }

        .ayuda-section h2 {
            font-size: var(--font-size-2xl);
            color: var(--color-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .ayuda-section h2::before {
            content: '';
            width: 4px;
            height: 2rem;
            background: linear-gradient(to bottom, var(--color-accent), var(--color-primary));
            border-radius: 2px;
        }

        /* Manual de usuario */
        .ayuda-manual p {
            margin-bottom: 1.5rem;
            color: var(--color-gray-600);
            font-size: var(--font-size-lg);
        }

        .btn-descargar {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-primary) 100%);
            color: var(--color-white);
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: var(--border-radius-lg);
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .btn-descargar::before {
            content: '📄';
            font-size: 1.2em;
        }

        .btn-descargar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-descargar:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-descargar:hover::after {
            left: 100%;
        }

        /* FAQ */
        .faq-item {
            border: 1px solid var(--color-gray-200);
            border-radius: var(--border-radius-md);
            margin-bottom: 1rem;
            background-color: var(--color-white);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--color-accent);
        }

        .faq-pregunta {
            padding: 1.25rem;
            font-weight: 600;
            color: var(--color-primary);
            cursor: pointer;
            list-style: none;
            position: relative;
            transition: all 0.3s ease;
        }

        .faq-pregunta::-webkit-details-marker {
            display: none;
        }

        .faq-pregunta::after {
            content: '+';
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            background-color: var(--color-accent);
            color: var(--color-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .faq-item[open] .faq-pregunta::after {
            content: '−';
            transform: translateY(-50%) rotate(180deg);
            background-color: var(--color-primary);
        }

        .faq-respuesta {
            padding: 0 1.25rem 1.25rem;
            color: var(--color-gray-600);
            border-top: 1px solid var(--color-gray-200);
            background-color: var(--color-gray-100);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }

            to {
                opacity: 1;
                max-height: 200px;
            }
        }

        /* Contacto */
        .lista-contacto {
            list-style: none;
            margin-bottom: 2rem;
            display: grid;
            gap: 1rem;
        }

        .lista-contacto li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background-color: var(--color-gray-100);
            border-radius: var(--border-radius-md);
            border-left: 4px solid var(--color-accent);
        }

        .lista-contacto li::before {
            font-size: 1.25rem;
        }

        .lista-contacto li:first-child::before {
            content: '📧';
        }

        .lista-contacto li:last-child::before {
            content: '📱';
        }

        .lista-contacto a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .lista-contacto a:hover {
            color: var(--color-accent);
        }

        /* Formulario */
        .form-contacto {
            background-color: var(--color-gray-100);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            border: 1px solid var(--color-gray-200);
        }

        .form-contacto label {
            display: block;
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: var(--color-primary);
        }

        .form-contacto input,
        .form-contacto textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-gray-300);
            border-radius: var(--border-radius-md);
            font-size: var(--font-size-base);
            margin-top: 0.5rem;
            transition: all 0.3s ease;
            background-color: var(--color-white);
        }

        .form-contacto input:focus,
        .form-contacto textarea:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(74, 159, 98, 0.1);
        }

        .form-contacto button {
            background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-primary) 100%);
            color: var(--color-white);
            padding: 0.875rem 2rem;
            border: none;
            border-radius: var(--border-radius-md);
            font-size: var(--font-size-base);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
        }

        .form-contacto button:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        /* Footer */
        .ayuda-footer {
            background-color: var(--color-primary);
            color: var(--color-white);
            text-align: center;
            padding: 2rem;
        }

        .ayuda-footer small {
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .ayuda-header {
                padding: 2rem 1rem;
            }

            .ayuda-header h1 {
                font-size: var(--font-size-3xl);
            }

            .ayuda-section {
                padding: 1.5rem;
            }

            .ayuda-section h2 {
                font-size: var(--font-size-xl);
            }

            .btn-descargar {
                padding: 0.875rem 1.5rem;
                width: 100%;
                justify-content: center;
            }

            .faq-pregunta {
                padding: 1rem;
                padding-right: 3rem;
            }

            .faq-pregunta::after {
                right: 1rem;
            }

            .faq-respuesta {
                padding: 0 1rem 1rem;
            }

            .form-contacto {
                padding: 1.5rem;
            }

            .form-contacto button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .ayuda-header h1 {
                font-size: var(--font-size-2xl);
            }

            .ayuda-section {
                padding: 1rem;
            }

            .lista-contacto li {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }

        /* Animaciones adicionales */
        .ayuda-section {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .ayuda-section:nth-child(1) {
            animation-delay: 0.1s;
        }

        .ayuda-section:nth-child(2) {
            animation-delay: 0.2s;
        }

        .ayuda-section:nth-child(3) {
            animation-delay: 0.3s;
        }

        .ayuda-section:nth-child(4) {
            animation-delay: 0.4s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estados de accesibilidad */
        .btn-descargar:focus,
        .faq-pregunta:focus,
        .form-contacto button:focus {
            outline: 2px solid var(--color-accent);
            outline-offset: 2px;
        }

        /* Mejoras de UX */
        .ayuda-section h2 {
            scroll-margin-top: 2rem;
        }

        .faq-item[open] {
            border-color: var(--color-accent);
        }
    </style>
</head>

<body>
    <div class="ayuda-page">
        <header class="ayuda-header">
            <h1>Ayuda - SCIM</h1>
            <p>Sistema de Control de Incidencias | Universidad Nacional Intercultural de la Amazonía</p>
        </header>

        <!-- MANUAL DE USUARIO -->
        <section class="ayuda-section ayuda-manual" aria-labelledby="titulo-manual">
            <h2 id="titulo-manual">Manual de usuario</h2>
            <p>Descarga el manual completo para conocer todas las funcionalidades del sistema SCIM y aprovechar al
                máximo todas sus características.</p>
            <a class="btn-descargar" href="{{ asset('archivos/manual-de-usuario.pdf') }}" 
            download="Manual-SCIM-UNIA.pdf"
                target="_blank" rel="noopener">
                Descargar manual (PDF)
            </a>
        </section>

        <!-- PREGUNTAS FRECUENTES -->
        <section class="ayuda-section ayuda-faq" aria-labelledby="titulo-faq">
            <h2 id="titulo-faq">Preguntas frecuentes</h2>

            <details class="faq-item">
                <summary class="faq-pregunta">¿Cómo creo mi cuenta en el sistema?</summary>
                <div class="faq-respuesta">
                    <p>Para crear tu cuenta, dirígete a la sección "Registro", completa todos los campos requeridos con
                        tus datos personales y universitarios, y confirma tu registro desde el correo electrónico
                        institucional que recibirás.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-pregunta">Olvidé mi contraseña, ¿qué debo hacer?</summary>
                <div class="faq-respuesta">
                    <p>En la página de "Iniciar sesión", selecciona la opción "¿Olvidaste tu contraseña?", ingresa tu
                        correo electrónico registrado y sigue las instrucciones que lleguen a tu bandeja de entrada para
                        restablecer tu contraseña.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-pregunta">¿Cómo puedo cambiar el idioma y el tema visual?</summary>
                <div class="faq-respuesta">
                    <p>Ve a "Configuración > Preferencias", donde podrás seleccionar tu idioma preferido y el tema
                        visual (claro u oscuro). No olvides guardar los cambios para que se apliquen correctamente.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-pregunta">¿Puedo actualizar mis datos de perfil?</summary>
                <div class="faq-respuesta">
                    <p>Sí, en "Configuración > Perfil" puedes editar tu información personal como nombre, foto de
                        perfil, datos de contacto y otra información relevante. Los cambios se guardan automáticamente.
                    </p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-pregunta">¿Cómo reporto un error o envío sugerencias?</summary>
                <div class="faq-respuesta">
                    <p>Puedes reportar problemas desde "Ayuda > Reportar un problema" dentro del sistema, o contactarnos
                        directamente por correo electrónico. Incluye capturas de pantalla y describe los pasos que
                        seguiste para ayudarnos a resolver el problema más rápidamente.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-pregunta">¿Dónde puedo ver mi historial de actividades?</summary>
                <div class="faq-respuesta">
                    <p>En "Mi cuenta > Actividad" encontrarás un registro detallado de todas tus acciones recientes en
                        el sistema, incluyendo incidencias reportadas, cambios realizados y fechas de acceso.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-pregunta">¿Qué navegadores son compatibles con el sistema?</summary>
                <div class="faq-respuesta">
                    <p>El sistema SCIM es compatible con las versiones actuales de Chrome, Firefox, Safari y Edge.
                        Recomendamos mantener tu navegador actualizado para una mejor experiencia de uso.</p>
                </div>
            </details>
        </section>

        <!-- CONTACTO -->
        <section class="ayuda-section ayuda-contacto" aria-labelledby="titulo-contacto">
            <h2 id="titulo-contacto">Contacto</h2>
            <ul class="lista-contacto">
                <li>
                    <strong>Correo institucional:</strong>
                    <a href="mailto:albertdannavarro@gmail.com">albertdannavarro@gmail.com</a>
                </li>
                <li>
                    <strong>Teléfono/WhatsApp:</strong>
                    <a href="tel:+52936159542">+52 936 159 542</a>
                </li>
            </ul>

            
        </section>

        <footer class="ayuda-footer">
            <small>
                &copy; <span id="anio-actual"></span> Universidad Nacional Intercultural de la Amazonía (UNIA) -
                Centro Universitario de Conectividad (CUC). Todos los derechos reservados.
            </small>
        </footer>
    </div>

    <script>
        // Año actual
        document.getElementById('anio-actual').textContent = new Date().getFullYear();

        // Animación suave para los elementos que aparecen
        const sections = document.querySelectorAll('.ayuda-section');
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        sections.forEach(section => {
            observer.observe(section);
        });
    </script>
</body>

</html>
