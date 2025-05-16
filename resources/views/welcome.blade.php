<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Tickets - Buzz Costa Rica</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        buzzOrange: '#fb4719',
                        buzzDark: '#333333',
                        buzzLight: '#f5f5f5',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .bg-gradient {
            background: linear-gradient(135deg, #fb4719 0%, #ff7a54 100%);
        }
        .shadow-custom {
            box-shadow: 0 10px 40px rgba(251, 71, 25, 0.2);
        }
        .hero-pattern {
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23fb4719' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="antialiased bg-white hero-pattern">
    <header class="fixed w-full bg-white bg-opacity-95 shadow z-50">
        <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center">
                <img src="https://buzzcostarica.com/wp-content/uploads/2021/11/Buzz-logo-rojo-horizontal-150.png" alt="Buzz Costa Rica" class="h-10">
            </div>
            <nav>
                @if (Route::has('login'))
                    <div class="space-x-4">
                        @auth
                            <a href="{{ url('/admin') }}" class="font-medium text-buzzDark hover:text-buzzOrange transition-colors">Panel de Control</a>
                        @else
                            <a href="{{ url('/admin/login') }}" class="px-6 py-2 bg-buzzOrange text-white rounded-full font-medium hover:bg-opacity-90 transition-colors">Iniciar Sesión</a>
                        @endauth
                    </div>
                @endif
            </nav>
        </div>
    </header>
    <main>
        <!-- Hero Section -->
        <section class="pt-32 pb-24 md:pt-48 md:pb-40">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-12 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-bold text-buzzDark leading-tight mb-6">
                            Sistema de Soporte <span class="text-buzzOrange">Ticket</span> para Clientes
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Plataforma avanzada para gestionar sus solicitudes de soporte de manera eficiente y ordenada. Reciba atención rápida y personalizada para todas sus consultas.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            @auth
                                <a href="{{ url('/admin/tickets/create') }}" class="inline-block px-8 py-4 bg-buzzOrange text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors text-center">Crear Nuevo Ticket</a>
                                <a href="{{ url('/admin/tickets') }}" class="inline-block px-8 py-4 border-2 border-buzzOrange text-buzzOrange rounded-lg font-semibold hover:bg-buzzOrange hover:text-white transition-colors text-center">Ver Mis Tickets</a>
                            @else
                                <a href="{{ url('/admin/login') }}" class="inline-block px-8 py-4 bg-buzzOrange text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors text-center">Iniciar Sesión</a>
                            @endauth
                        </div>
                    </div>
                    <div class="md:w-1/2">
                        <img src="https://cdn.pixabay.com/photo/2017/07/31/11/44/laptop-2557576_1280.jpg" alt="Sistema de Tickets" class="rounded-xl shadow-custom w-full">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-buzzLight">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-buzzDark mb-4">Características Principales</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        Nuestro sistema está diseñado para brindar la mejor experiencia posible tanto para clientes como para el equipo de soporte.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                        <div class="w-14 h-14 bg-buzzOrange bg-opacity-10 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-buzzOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-buzzDark mb-3">Respuesta Rápida</h3>
                        <p class="text-gray-600">
                            Sistema optimizado para garantizar respuestas rápidas y eficientes a todas sus solicitudes de soporte.
                        </p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                        <div class="w-14 h-14 bg-buzzOrange bg-opacity-10 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-buzzOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-buzzDark mb-3">Seguimiento Detallado</h3>
                        <p class="text-gray-600">
                            Mantenga un registro completo de todas sus solicitudes y respuestas para un mejor seguimiento de sus casos.
                        </p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                        <div class="w-14 h-14 bg-buzzOrange bg-opacity-10 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-buzzOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-buzzDark mb-3">Seguridad Garantizada</h3>
                        <p class="text-gray-600">
                            Todos sus datos y comunicaciones están protegidos con los más altos estándares de seguridad.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-buzzDark mb-4">¿Cómo Funciona?</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        Nuestro sistema de tickets está diseñado para ser intuitivo y fácil de usar. Siga estos sencillos pasos:
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-buzzOrange rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">1</div>
                        <h3 class="text-xl font-semibold text-buzzDark mb-2">Inicie Sesión</h3>
                        <p class="text-gray-600">
                            Acceda a su cuenta con las credenciales proporcionadas por nuestro equipo.
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-buzzOrange rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">2</div>
                        <h3 class="text-xl font-semibold text-buzzDark mb-2">Cree un Ticket</h3>
                        <p class="text-gray-600">
                            Describa su problema o consulta con el máximo detalle posible.
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-buzzOrange rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">3</div>
                        <h3 class="text-xl font-semibold text-buzzDark mb-2">Reciba Respuesta</h3>
                        <p class="text-gray-600">
                            Nuestro equipo revisará su ticket y le responderá a la brevedad.
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-buzzOrange rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">4</div>
                        <h3 class="text-xl font-semibold text-buzzDark mb-2">Problema Resuelto</h3>
                        <p class="text-gray-600">
                            Una vez solucionado, podrá calificar la atención recibida.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient text-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold mb-6">¿Listo para comenzar?</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    Acceda ahora a nuestro sistema de tickets y experimente una atención de soporte optimizada y eficiente.
                </p>
                @auth
                    <a href="{{ url('/admin/tickets/create') }}" class="inline-block px-8 py-4 bg-white text-buzzOrange rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Crear Nuevo Ticket</a>
                @else
                    <a href="{{ url('/admin/login') }}" class="inline-block px-8 py-4 bg-white text-buzzOrange rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Iniciar Sesión</a>
                @endauth
            </div>
        </section>
    </main>
    <!-- Footer -->
    <footer class="bg-buzzDark text-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-8 md:mb-0">
                    <img src="https://buzzcostarica.com/wp-content/uploads/2021/11/Buzz-logo-rojo-horizontal-150.png" alt="Buzz Costa Rica" class="h-12" />
                    <p class="mt-4 text-gray-400 max-w-md">
                        Sistema de tickets profesional para nuestros clientes, diseñado para ofrecer soporte eficiente y soluciones rápidas.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                        <ul class="space-y-2">
                            @auth
                                <li><a href="{{ url('/admin/tickets') }}" class="text-gray-400 hover:text-white transition-colors">Mis Tickets</a></li>
                                <li><a href="{{ url('/admin/tickets/create') }}" class="text-gray-400 hover:text-white transition-colors">Crear Ticket</a></li>
                                <li><a href="{{ url('/admin/profile') }}" class="text-gray-400 hover:text-white transition-colors">Mi Perfil</a></li>
                            @else
                                <li><a href="{{ url('/admin/login') }}" class="text-gray-400 hover:text-white transition-colors">Iniciar Sesión</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                        <ul class="space-y-2">
                            <li class="text-gray-400">Teléfono: (506) 2222-3333</li>
                            <li class="text-gray-400">Email: soporte@buzzcostarica.com</li>
                            <li class="text-gray-400">Horario: Lunes a Viernes 8am - 5pm</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-800 text-center text-gray-500">
                <p>&copy; {{ date('Y') }} Buzz Costa Rica. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
