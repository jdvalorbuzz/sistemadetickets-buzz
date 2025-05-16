<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Ticket #{{ $ticket->id }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        
        .rating > input {
            display: none;
        }
        
        .rating > label {
            position: relative;
            width: 1.1em;
            font-size: 3rem;
            color: #ccc;
            cursor: pointer;
        }
        
        .rating > label::before {
            content: "\2605";
            position: absolute;
            opacity: 0;
        }
        
        .rating > label:hover:before,
        .rating > label:hover ~ label:before {
            opacity: 1 !important;
        }
        
        .rating > input:checked ~ label:before {
            opacity: 1;
        }
        
        .rating:hover > input:checked ~ label:before {
            opacity: 0.4;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 text-white p-4">
            <h1 class="text-xl font-bold">Califica tu experiencia</h1>
            <p class="text-sm opacity-80">Ticket #{{ $ticket->id }}: {{ $ticket->title }}</p>
        </div>
        
        <div class="p-6">
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('tickets.rate.store', $ticket->id) }}" method="POST">
                @csrf
                
                <div class="mb-6 text-center">
                    <p class="mb-3 text-gray-700">¿Cómo calificarías el servicio recibido?</p>
                    
                    <div class="rating">
                        <input type="radio" name="rating" value="5" id="star5" {{ optional($rating)->rating == 5 ? 'checked' : '' }}>
                        <label for="star5" title="Excelente">&#9733;</label>
                        
                        <input type="radio" name="rating" value="4" id="star4" {{ optional($rating)->rating == 4 ? 'checked' : '' }}>
                        <label for="star4" title="Muy Bueno">&#9733;</label>
                        
                        <input type="radio" name="rating" value="3" id="star3" {{ optional($rating)->rating == 3 ? 'checked' : '' }}>
                        <label for="star3" title="Bueno">&#9733;</label>
                        
                        <input type="radio" name="rating" value="2" id="star2" {{ optional($rating)->rating == 2 ? 'checked' : '' }}>
                        <label for="star2" title="Regular">&#9733;</label>
                        
                        <input type="radio" name="rating" value="1" id="star1" {{ optional($rating)->rating == 1 ? 'checked' : '' }}>
                        <label for="star1" title="Malo">&#9733;</label>
                    </div>
                    
                    @error('rating')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="feedback" class="block text-gray-700 mb-2">Comentarios adicionales (opcional)</label>
                    <textarea 
                        id="feedback" 
                        name="feedback" 
                        rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder="¿Qué te gustó? ¿Qué podríamos mejorar?"
                    >{{ optional($rating)->feedback }}</textarea>
                </div>
                
                <div class="flex justify-between">
                    <a 
                        href="{{ route('filament.admin.resources.tickets.index') }}" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                    >
                        Cancelar
                    </a>
                    
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        {{ $rating ? 'Actualizar calificación' : 'Enviar calificación' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
