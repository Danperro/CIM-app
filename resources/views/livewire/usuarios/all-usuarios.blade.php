<div>
    <div class="container">
        <h1 class="mb-4">Gestión de Usuarios</h1>

        <!-- Formulario de registro -->
        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="" class="form-label fw-bold">Ingrese usuario</label>
                <input wire:model.live.debounce.500ms="query" type="text" class="form-control"
                    placeholder="Nombre del usuario">
            </div>
            <div class="col-md-5">
                <label for="idRol" class="form-label fw-bold">Rol</label>
                <select id="idRol" wire:model.live="idRol" class="form-select">
                    <option value="" hidden>Seleccionar un Rol</option>
                    @foreach ($roles as $rls)
                        <option value="{{ $rls->IdRol }}">{{ $rls->NombreRol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#kt_modal_create_usuario" wire:click="limpiar()">Registrar</a>
            </div>
        </div>

        <!-- Tabla de equipos -->
        <div class="mb-4">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Username</th>
                        <th>Nombres y Apellidos</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usa)
                        <tr>
                            <td>{{ $usa->UsernameUsa }}</td>
                            <td>{{ $usa->persona->NombrePer . ' ' . $usa->persona->ApellidoPaternoPer . ' ' . $usa->persona->ApellidoMaternoPer }}
                            </td>
                            <td>{{ $usa->rol->NombreRol }}</td>
                            <td>
                                @if ($usa->EstadoUsa == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="cargarDatosParaEditar({{ $usa->IdUsa }})"
                                    class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_editar_usuario">
                                    Editar
                                </button>
                                <button wire:click="selectInfo({{ $usa->IdUsa }})" class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_eliminar_usuario">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Editar Usuario -->
        <div wire:ignore.self class="modal fade" id="kt_modal_editar_usuario" tabindex="-1"
            aria-labelledby="modalLabelEditar" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form wire:submit.prevent="actualizarUsuario" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabelEditar">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"
                            wire:click="limpiar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Columna 1 -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" wire:model="usernameUsa"
                                        class="form-control @error('usernameUsa') is-invalid @enderror">
                                    @error('usernameUsa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" wire:model="nombrePer"
                                        class="form-control @error('nombrePer') is-invalid @enderror">
                                    @error('nombrePer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Correo</label>
                                    <input type="email" wire:model="correoPer"
                                        class="form-control @error('correoPer') is-invalid @enderror">
                                    @error('correoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Columna 2 -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Nueva Contraseña (opcional)</label>
                                    <input type="password" wire:model="passwordUsa"
                                        class="form-control @error('passwordUsa') is-invalid @enderror">
                                    @error('passwordUsa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apellido Paterno</label>
                                    <input type="text" wire:model="apellidoPaternoPer"
                                        class="form-control @error('apellidoPaternoPer') is-invalid @enderror">
                                    @error('apellidoPaternoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">DNI</label>
                                    <input type="text" wire:model="dniPer"
                                        class="form-control @error('dniPer') is-invalid @enderror">
                                    @error('dniPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Columna 3 -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Rol</label>
                                    <select wire:model="idRol" class="form-select @error('idRol') is-invalid @enderror">
                                        <option value="">Seleccionar rol</option>
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->IdRol }}">{{ $rol->NombreRol }}</option>
                                        @endforeach
                                    </select>
                                    @error('idRol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apellido Materno</label>
                                    <input type="text" wire:model="apellidoMaternoPer"
                                        class="form-control @error('apellidoMaternoPer') is-invalid @enderror">
                                    @error('apellidoMaternoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" wire:model="telefonoPer"
                                        class="form-control @error('telefonoPer') is-invalid @enderror">
                                    @error('telefonoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Columna 4 -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Área</label>
                                    <select wire:model="idAre" wire:change="cargarLaboratorios"
                                        class="form-select @error('idAre') is-invalid @enderror">
                                        <option value="">Seleccionar área</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->IdAre }}">{{ $area->NombreAre }}</option>
                                        @endforeach
                                    </select>
                                    @error('idAre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Laboratorios</label>
                                    <div class="table-responsive border rounded @error('laboratoriosSeleccionados') border-danger @enderror"
                                        style="max-height: 150px;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Laboratorio</th>
                                                    <th class="text-center">Seleccionar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($laboratoriosPorArea as $lab)
                                                    <tr>
                                                        <td>{{ $lab->NombreLab }}</td>
                                                        <td class="text-center">
                                                            <input type="checkbox"
                                                                wire:model="laboratoriosSeleccionados"
                                                                value="{{ $lab->IdLab }}">
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">Selecciona
                                                            un área primero</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted">Puedes seleccionar varios laboratorios</small>
                                    @error('laboratoriosSeleccionados')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fecha Nacimiento</label>
                                    <input type="date" wire:model="fechaNacimientoPer"
                                        class="form-control @error('fechaNacimientoPer') is-invalid @enderror">
                                    @error('fechaNacimientoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">
                            <span wire:loading.remove wire:target="actualizarUsuario">Actualizar</span>
                            <span wire:loading wire:target="actualizarUsuario">
                                <i class="spinner-border spinner-border-sm me-2"></i>
                                Guardando...
                            </span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="limpiar">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>


        <div wire:ignore.self class="modal fade" id="kt_modal_eliminar_usuario" tabindex="-1"
            aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form wire:submit.prevent="eliminarUsuario" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Eliminar usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <p>¿Estás seguro que deseas eliminar este usuario <strong>{{ $usernameUsa }}</strong>?
                                </p>
                                <p class="text-muted">Esta acción no se puede deshacer.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <span wire:loading.remove wire:target="eliminarUsuario">Eliminar</span>
                            <span wire:loading wire:target="eliminarUsuario">
                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                Eliminando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!--sirve para evitar que el modal parapede a cada rato-->
        <!-- Modal corregido -->
        <div wire:ignore.self class="modal fade" id="kt_modal_create_usuario" tabindex="-1"
            aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form wire:submit.prevent="registrarUsuario" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Registrar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"
                            wire:click="limpiar"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Mostrar error general si existe -->
                        @error('general')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="row g-3">
                            <!-- Primera columna -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" wire:model.live="usernameUsa"
                                        class="form-control @error('usernameUsa') is-invalid @enderror">
                                    @error('usernameUsa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" wire:model.live="nombrePer"
                                        class="form-control @error('nombrePer') is-invalid @enderror">
                                    @error('nombrePer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Correo</label>
                                    <input type="email" wire:model.live="correoPer"
                                        class="form-control @error('correoPer') is-invalid @enderror">
                                    @error('correoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Segunda columna -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" wire:model.live="passwordUsa"
                                        class="form-control @error('passwordUsa') is-invalid @enderror">
                                    @error('passwordUsa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Apellido Paterno</label>
                                    <input type="text" wire:model.live="apellidoPaternoPer"
                                        class="form-control @error('apellidoPaternoPer') is-invalid @enderror">
                                    @error('apellidoPaternoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">DNI</label>
                                    <input type="text" wire:model.live="dniPer"
                                        class="form-control @error('dniPer') is-invalid @enderror">
                                    @error('dniPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tercera columna -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Rol</label>
                                    <select wire:model.live="idRol"
                                        class="form-select @error('idRol') is-invalid @enderror">
                                        <option value="">Seleccionar rol</option>
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->IdRol }}">{{ $rol->NombreRol }}</option>
                                        @endforeach
                                    </select>
                                    @error('idRol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Apellido Materno</label>
                                    <input type="text" wire:model.live="apellidoMaternoPer"
                                        class="form-control @error('apellidoMaternoPer') is-invalid @enderror">
                                    @error('apellidoMaternoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" wire:model.live="telefonoPer"
                                        class="form-control @error('telefonoPer') is-invalid @enderror">
                                    @error('telefonoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Cuarta columna -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Área</label>
                                    <select wire:model.live="idAre" wire:change="cargarLaboratorios"
                                        class="form-select @error('idAre') is-invalid @enderror">
                                        <option value="">Seleccionar área</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->IdAre }}">{{ $area->NombreAre }}</option>
                                        @endforeach
                                    </select>
                                    @error('idAre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Laboratorios</label>
                                    <div class="table-responsive border rounded @error('laboratoriosSeleccionados') border-danger @enderror"
                                        style="max-height: 150px;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Laboratorio</th>
                                                    <th class="text-center">Seleccionar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($laboratoriosPorArea as $lab)
                                                    <tr>
                                                        <td>{{ $lab->NombreLab }}</td>
                                                        <td class="text-center">
                                                            <input type="checkbox"
                                                                wire:model="laboratoriosSeleccionados"
                                                                value="{{ $lab->IdLab }}">
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">
                                                            Selecciona un área primero
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted">Puedes seleccionar varios laboratorios</small>
                                    @error('laboratoriosSeleccionados')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fecha Nacimiento</label>
                                    <input type="date" wire:model.live="fechaNacimientoPer"
                                        class="form-control @error('fechaNacimientoPer') is-invalid @enderror">
                                    @error('fechaNacimientoPer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            <span wire:loading.remove wire:target="registrarUsuario">Registrar</span>
                            <span wire:loading wire:target="registrarUsuario">
                                <i class="spinner-border spinner-border-sm me-2"></i>
                                Registrando...
                            </span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="limpiar">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de confirmación de éxito -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-success" id="successModalTitle">¡Éxito!</h4>
                        <p class="text-muted" id="successModalMessage">Operación realizada correctamente.</p>
                    </div>
                </div>
            </div>
        </div>


    </div>




</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('toast-success', (event) => {
            // Cerrar posibles modales abiertos
            const modalRegistro = bootstrap.Modal.getInstance(document.getElementById(
                'kt_modal_create_usuario'));
            if (modalRegistro) modalRegistro.hide();

            const modalEditar = bootstrap.Modal.getInstance(document.getElementById(
                'kt_modal_editar_usuario'));
            if (modalEditar) modalEditar.hide();

            const modalEliminar = bootstrap.Modal.getInstance(document.getElementById(
                'kt_modal_eliminar_usuario'));
            if (modalEliminar) modalEliminar.hide();

            // Mostrar modal de éxito
            const title = event?.title || '¡Éxito!';
            const message = event?.message || 'Operación completada correctamente.';

            document.getElementById('successModalTitle').textContent = title;
            document.getElementById('successModalMessage').textContent = message;

            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            setTimeout(() => {
                successModal.hide();
            }, 2000);
        });
    });
</script>
<script>
    // Agregar este script para manejar las notificaciones toast
    document.addEventListener('DOMContentLoaded', function() {
        // Escuchar eventos de toast de éxito
        Livewire.on('toast-success', (event) => {
            const message = event.message;
            const toastElement = document.getElementById('toast-success');
            const messageElement = document.getElementById('toast-success-message');

            messageElement.textContent = message;

            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        });

        // Escuchar eventos de toast de error
        Livewire.on('toast-danger', (event) => {
            const message = event.message;
            const toastElement = document.getElementById('toast-danger');
            const messageElement = document.getElementById('toast-danger-message');

            messageElement.textContent = message;

            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Escuchar el evento personalizado para cerrar modal
        Livewire.on('cerrar-modal', (event) => {
            const modalId = event.modalId;
            const modalElement = document.getElementById(modalId);

            if (modalElement) {
                // Usar Bootstrap para cerrar el modal
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                } else {
                    // Si no hay instancia, crear una nueva y cerrarla
                    const newModal = new bootstrap.Modal(modalElement);
                    newModal.hide();
                }
            }
        });
    });
</script>
