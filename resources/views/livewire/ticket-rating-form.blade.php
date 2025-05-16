<div class="p-6 space-y-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        Califica tu experiencia con este ticket
    </h2>
    
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Tu opinión es importante para nosotros. Ayúdanos a mejorar nuestro servicio.
    </p>
    
    <div class="space-y-4">
        <!-- Rating Stars -->
        <div class="flex items-center justify-center space-x-1">
            @for($i = 1; $i <= 5; $i++)
                <button type="button" 
                    class="text-{{ $rating >= $i ? 'yellow-500' : 'gray-300' }} hover:text-yellow-500 transition-colors" 
                    wire:click="$set('rating', {{ $i }})">
                    <svg class="w-8 h-8 md:w-10 md:h-10" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </button>
            @endfor
        </div>
        
        <div class="text-center text-lg font-medium">
            {{ $rating }} / 5
        </div>
        
        <!-- Feedback Input -->
        <div>
            <label for="feedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Comentarios adicionales (opcional)
            </label>
            <textarea 
                id="feedback" 
                rows="3" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm"
                placeholder="¿Qué te gustó? ¿Qué podríamos mejorar?"
                wire:model="feedback"
            ></textarea>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button
                type="button"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                wire:click="saveRating"
            >
                Enviar calificación
            </button>
        </div>
    </div>
</div>
