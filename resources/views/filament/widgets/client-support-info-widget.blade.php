<x-filament::section>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">¿Cómo funciona el sistema de tickets?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="text-center">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-800 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 text-xl font-bold mx-auto mb-3">1</div>
                    <h3 class="text-base font-medium mb-2">Cree un ticket</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Describa su solicitud con el mayor detalle posible.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-800 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 text-xl font-bold mx-auto mb-3">2</div>
                    <h3 class="text-base font-medium mb-2">Reciba respuesta</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Nuestro equipo revisará su consulta y responderá a la brevedad.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-800 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 text-xl font-bold mx-auto mb-3">3</div>
                    <h3 class="text-base font-medium mb-2">Comunicación</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Recibirá notificaciones por email de cada actualización.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-800 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 text-xl font-bold mx-auto mb-3">4</div>
                    <h3 class="text-base font-medium mb-2">Solución</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Una vez resuelto, podrá calificar la atención recibida.
                    </p>
                </div>
            </div>
            
            <div class="flex justify-center">
                <a 
                    href="{{ route('filament.admin.resources.tickets.create') }}" 
                    class="inline-flex items-center justify-center py-2 px-4 rounded-lg font-medium text-white bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 dark:focus:ring-offset-0 transition"
                >
                    <x-heroicon-o-plus-circle class="w-5 h-5 mr-1" />
                    Crear Nuevo Ticket
                </a>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Horario de Soporte</h2>
            
            <div class="space-y-2 text-gray-500 dark:text-gray-400">
                <p><span class="font-medium">Lunes a Viernes:</span> 8:00 AM - 6:00 PM</p>
                <p><span class="font-medium">Sábados:</span> 9:00 AM - 1:00 PM</p>
                <p><span class="font-medium">Domingos y Feriados:</span> Cerrado</p>
                
                <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm"><strong>Email de Soporte:</strong> soporte@buzzcostarica.com</p>
                    <p class="text-sm"><strong>Teléfono:</strong> (506) 2222-3333</p>
                </div>
            </div>
        </div>
    </div>
</x-filament::section>
