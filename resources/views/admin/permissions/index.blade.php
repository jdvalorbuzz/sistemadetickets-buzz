@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestión de Permisos</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                        Crear Nuevo Permiso
                    </button>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <h4>Asignar Permisos por Rol</h4>
                    <ul class="nav nav-tabs mb-4" id="permissionsTabs" role="tablist">
                        @foreach($roles as $index => $role)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                        id="{{ $role }}-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#{{ $role }}-tab-pane" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="{{ $role }}-tab-pane" 
                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                    {{ ucfirst($role) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="tab-content" id="permissionsTabsContent">
                        @foreach($roles as $index => $role)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                id="{{ $role }}-tab-pane" 
                                role="tabpanel" 
                                aria-labelledby="{{ $role }}-tab" 
                                tabindex="0">
                                
                                <form action="{{ route('admin.permissions.update-role') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $role }}">
                                    
                                    <div class="row">
                                        @foreach($permissionsByCategory as $category => $permissions)
                                            <div class="col-md-6 mb-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>{{ $category }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input category-checkbox" 
                                                                type="checkbox" 
                                                                id="category-{{ Str::slug($category) }}-{{ $role }}"
                                                                data-category="{{ Str::slug($category) }}-{{ $role }}">
                                                            <label class="form-check-label fw-bold" for="category-{{ Str::slug($category) }}-{{ $role }}">
                                                                Seleccionar Todos
                                                            </label>
                                                        </div>
                                                        <hr>
                                                        
                                                        @foreach($permissions as $permission)
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox {{ Str::slug($category) }}-{{ $role }}" 
                                                                    type="checkbox" 
                                                                    name="permissions[]" 
                                                                    value="{{ $permission->id }}" 
                                                                    id="permission-{{ $permission->id }}-{{ $role }}"
                                                                    {{ in_array($permission->id, $rolePermissions[$role]) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="permission-{{ $permission->id }}-{{ $role }}">
                                                                    {{ $permission->display_name }}
                                                                    <small class="text-muted d-block">{{ $permission->description ?? $permission->name }}</small>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Guardar Permisos para {{ ucfirst($role) }}</button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nuevo permiso -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPermissionModalLabel">Crear Nuevo Permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Técnico</label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               placeholder="Ejemplo: view_reports">
                        <div class="form-text">Identificador único para el permiso (sin espacios ni caracteres especiales)</div>
                    </div>
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Nombre de Visualización</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" required
                               placeholder="Ejemplo: Ver Reportes">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Descripción detallada de lo que permite este permiso"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="category" name="category" required
                               list="existing-categories" placeholder="Ejemplo: Reportes">
                        <datalist id="existing-categories">
                            @foreach($permissionsByCategory->keys() as $category)
                                <option value="{{ $category }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestionar checkboxes de categoría
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const category = this.dataset.category;
                const checkboxes = document.querySelectorAll(`.permission-checkbox.${category}`);
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        });
        
        // Actualizar checkboxes de categoría si todos los permisos están seleccionados
        function updateCategoryCheckboxes() {
            categoryCheckboxes.forEach(categoryCheckbox => {
                const category = categoryCheckbox.dataset.category;
                const permissionCheckboxes = document.querySelectorAll(`.permission-checkbox.${category}`);
                const allChecked = Array.from(permissionCheckboxes).every(cb => cb.checked);
                categoryCheckbox.checked = allChecked;
            });
        }
        
        // Agregar listener a todos los checkboxes de permisos
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCategoryCheckboxes);
        });
        
        // Ejecutar al cargar para actualizar el estado inicial
        updateCategoryCheckboxes();
    });
</script>
@endpush
@endsection
