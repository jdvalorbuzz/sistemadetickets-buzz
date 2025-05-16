@extends('layouts.app')

@section('styles')
<style>
    :root {
        --bs-primary: #fa4619;
        --bs-primary-rgb: 250, 70, 25;
    }
    
    .bg-primary {
        background-color: #fa4619 !important;
    }
    
    .btn-primary {
        background-color: #fa4619;
        border-color: #fa4619;
    }
    
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        background-color: #e13d12;
        border-color: #e13d12;
    }
    
    .text-primary {
        color: #fa4619 !important;
    }
    
    a {
        color: #fa4619;
    }
    
    a:hover {
        color: #e13d12;
    }
    
    .card-stats {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
    }
    
    .card-stats:hover {
        transform: translateY(-5px);
    }
    
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <!-- Tickets Abiertos -->
        <div class="col-md-4 mb-4">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success text-white">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Tickets Abiertos</h5>
                            <h3 class="mb-0">{{ $openTicketsCount }}</h3>
                            <a href="{{ route('support.dashboard', ['status' => 'open']) }}" class="text-sm">En espera de atención</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tickets En Progreso -->
        <div class="col-md-4 mb-4">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary text-white">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Tickets En Progreso</h5>
                            <h3 class="mb-0">{{ $inProgressTicketsCount }}</h3>
                            <a href="{{ route('support.dashboard', ['status' => 'in_progress']) }}" class="text-sm">En proceso de resolución</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mis Tickets Cerrados -->
        <div class="col-md-4 mb-4">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-secondary text-white">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Mis Tickets Cerrados</h5>
                            <h3 class="mb-0">{{ $closedTicketsCount }}</h3>
                            <a href="{{ route('support.dashboard', ['my_closed' => 'true']) }}" class="text-sm">Últimos 30 días</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestión de Tickets</h5>
                    <div>
                        @if(request()->has('my_closed'))
                            <span class="badge bg-light text-dark">Mostrando: Mis tickets cerrados</span>
                        @elseif(request()->has('mine'))
                            <span class="badge bg-light text-dark">Mostrando: Mis tickets asignados</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form action="{{ route('support.dashboard') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Estado</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos</option>
                                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Abiertos</option>
                                    <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Cerrados</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="department_id" class="form-label">Departamento</label>
                                <select name="department_id" id="department_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="priority" class="form-label">Prioridad</label>
                                <select name="priority" id="priority" class="form-select">
                                    <option value="">Todas</option>
                                    <option value="low" {{ $priority === 'low' ? 'selected' : '' }}>Baja</option>
                                    <option value="medium" {{ $priority === 'medium' ? 'selected' : '' }}>Media</option>
                                    <option value="high" {{ $priority === 'high' ? 'selected' : '' }}>Alta</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                                <a href="{{ route('support.dashboard') }}" class="btn btn-secondary">Reiniciar</a>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Buscar por título, ID o cliente" 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="mine" name="mine" 
                                          {{ request()->has('mine') ? 'checked' : '' }}
                                          onchange="this.form.submit()">
                                    <label class="form-check-label" for="mine">
                                        Solo mostrar mis tickets asignados
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="my_closed" name="my_closed" 
                                          {{ request()->has('my_closed') ? 'checked' : '' }}
                                          onchange="this.form.submit()">
                                    <label class="form-check-label" for="my_closed">
                                        Solo mostrar mis tickets cerrados
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Lista de Tickets -->
                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Título</th>
                                        <th>Cliente</th>
                                        <th>Departamento</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Asignado</th>
                                        <th>Creado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>{{ Str::limit($ticket->title, 30) }}</td>
                                            <td>{{ $ticket->user->name }}</td>
                                            <td>{{ $ticket->department->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'success') }}">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                                    {{ $ticket->status === 'open' ? 'Abierto' : ($ticket->status === 'in_progress' ? 'En Progreso' : 'Cerrado') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($ticket->assigned_to)
                                                    {{ $ticket->assignedTo->name }}
                                                @else
                                                    <span class="badge bg-light text-dark">Sin asignar</span>
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('support.ticket.preview', $ticket) }}" class="btn btn-sm btn-primary">
                                                        Ver
                                                    </a>
                                                    @if($ticket->assigned_to === null)
                                                        <form action="{{ route('support.ticket.take', $ticket) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                Tomar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación -->
                        <div class="d-flex justify-content-center">
                            {{ $tickets->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            No se encontraron tickets que coincidan con los criterios de búsqueda.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
