<div class="container">
    <h1 class="mb-4">Gestión de Equipos</h1>

    <!-- Formulario de registro -->
    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <label for="nombreEquipo" class="form-label fw-bold">Ingrese equipo</label>
            <input type="text" id="nombreEquipo" class="form-control" placeholder="Nombre del equipo">
        </div>
        <div class="col-md-5">
            <label for="idLab" class="form-label fw-bold">Laboratorios</label>
            <select id="idLab" wire:model.live="idLab" class="form-select">
                <option value="" hidden>Seleccionar un laboratorio</option>
                @foreach ($laboratorios as $lab)
                    <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_equipo"
                wire:click="limpiar()">Registrar</a>
        </div>
    </div>

    <!-- Tabla de equipos -->
    <div class="mb-4">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Laboratorio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipos as $eqo)
                    <tr>
                        <td>{{ $eqo->NombreEqo }}</td>
                        <td>{{ $eqo->laboratorio->NombreLab }}</td>
                        <td>
                            @if ($eqo->EstadoEqo == 1)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm me-1" wire:click="selectInfo({{ $eqo->IdEqo }})"
                                href="#" data-bs-toggle="modal" data-bs-target="#kt_modal_mostrar_periferico">
                                <i class="bi bi-eye"></i> Ver periféricos</button>

                            <button class="btn btn-warning btn-sm" wire:click="selectEditarEquipo({{ $eqo->IdEqo }})">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>

                            <button wire:click="selectInfo({{ $eqo->IdEqo }})" class="btn btn-danger btn-sm">
                                <span href="#" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_eliminar_equipo">Eliminar</span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div wire:ignore.self class="modal fade" id="kt_modal_editar_equipo" tabindex="-1"
        aria-labelledby="modalLabelEditar" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form wire:submit.prevent="actualizarEquipo" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelEditar">Editar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- COLUMNA IZQUIERDA -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Nombre</label>
                                <input type="text" wire:model.live="nombreEqo" class="form-control">
                                @error('nombreEqo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label>Código</label>
                                <input type="text" wire:model.live="codigoEqo" class="form-control">
                                @error('codigoEqo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="idLab">Laboratorio</label>
                                <select id="idLab" wire:model.live="idLab" class="form-select">
                                    <option value="">Selecciona Laboratorio</option>
                                    @foreach ($laboratorios as $lab)
                                        <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                                    @endforeach
                                </select>
                                @error('idLab')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label>Periféricos seleccionados</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Código CIU</th>
                                                <th>Marca</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($perifericosSeleccionados as $p)
                                                <tr>
                                                    <td>{{ $p['tipoperiferico']['NombreTpf'] ?? '---' }}</td>
                                                    <td>{{ $p['CiuPef'] }}</td>
                                                    <td>{{ $p['MarcaPef'] }}</td>
                                                    <td>
                                                        <button type="button"
                                                            wire:click="quitarPeriferico({{ $p['IdPef'] }})"
                                                            class="btn btn-danger btn-sm">
                                                            <i class="bi bi-x-circle"></i> Quitar
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="idTpf">Filtrar por tipo de periférico</label>
                                <select id="idTpf" wire:model.live="idTpf" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    @foreach ($tiposperifericos as $tpf)
                                        <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf }}</option>
                                    @endforeach
                                </select>
                                @error('idTpf')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Código CIU</th>
                                            <th>Marca</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mostrarPerifericosNoAsignados as $mpf)
                                            @if (empty($idTpf) || $mpf->IdTpf == $idTpf)
                                                <tr>
                                                    <td>{{ $mpf->tipoperiferico->NombreTpf }}</td>
                                                    <td>{{ $mpf->CiuPef }}</td>
                                                    <td>{{ $mpf->MarcaPef }}</td>
                                                    <td>
                                                        <button type="button"
                                                            wire:click="agregarPeriferico({{ $mpf->IdPef }})"
                                                            class="btn btn-success btn-sm">
                                                            <i class="bi bi-plus-circle"></i> Agregar
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>



    <div wire:ignore.self class="modal fade" id="kt_modal_create_equipo" tabindex="-1" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl"> <!-- Modal más grande -->
            <form wire:submit.prevent="registrarEquipo" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- COLUMNA IZQUIERDA: DATOS DEL EQUIPO -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Nombre</label>
                                <input type="text" wire:model.live="nombreEqo" class="form-control">
                                @error('nombreEqo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label>Código</label>
                                <input type="text" wire:model.live="codigoEqo" class="form-control">
                                @error('codigoEqo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="idLab" class="form-label">Laboratorios</label>
                                <select id="idLab" wire:model.live="idLab" class="form-select">
                                    <option value="">Selecciona Laboratorio</option>
                                    @foreach ($laboratorios as $lab)
                                        <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                                    @endforeach
                                </select>
                                @error('idLab')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <!-- Tabla de periféricos -->
                                <label for="idPef" class="form-label">Perifericos seleccionados</label>
                                <div class="table">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Código CIU</th>
                                                <th>Marca</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($perifericosSeleccionados as $p)
                                                <tr>
                                                    <td>{{ $p['tipoperiferico']['NombreTpf'] ?? '---' }}</td>
                                                    <td>{{ $p['CiuPef'] }}</td>
                                                    <td>{{ $p['MarcaPef'] }}</td>
                                                    <td>
                                                        <button type="button"
                                                            wire:click="quitarPeriferico({{ $p['IdPef'] }})"
                                                            class="btn btn-danger btn-sm">
                                                            <i class="bi bi-x-circle"></i> Quitar
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA: FILTRO Y TABLA -->
                        <div class="col-md-6">
                            <!-- Filtro por tipo -->
                            <div class="mb-3">
                                <label for="idTpf" class="form-label">Filtrar por tipo de periférico</label>
                                <select id="idTpf" wire:model.live="idTpf" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    @foreach ($tiposperifericos as $tpf)
                                        <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf }}</option>
                                    @endforeach
                                </select>
                                @error('idTpf')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tabla de periféricos -->
                            <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Código CIU</th>
                                            <th>Marca</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mostrarPerifericosNoAsignados as $mpf)
                                            @if (empty($idTpf) || $mpf->IdTpf == $idTpf)
                                                <tr>
                                                    <td>{{ $mpf->tipoperiferico->NombreTpf }}</td>
                                                    <td>{{ $mpf->CiuPef }}</td>
                                                    <td>{{ $mpf->MarcaPef }}</td>
                                                    <td>
                                                        <button type="button"
                                                            wire:click="agregarPeriferico({{ $mpf->IdPef }})"
                                                            class="btn btn-success btn-sm">
                                                            <i class="bi bi-plus-circle"></i> Agregar
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="kt_modal_eliminar_equipo" tabindex="-1"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="eliminarEquipo" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Eliminar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <p>¿Estás seguro que deseas eliminar el equipo <strong>{{ $nombreEqo }}</strong>?</p>
                            <p class="text-muted">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <span wire:loading.remove wire:target="eliminarEquipo">Eliminar</span>
                        <span wire:loading wire:target="eliminarEquipo">
                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                            Eliminando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="kt_modal_mostrar_periferico" tabindex="-1"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Mostrar Periféricos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Código CIU</th>
                                    <th>Código de Inventario</th>
                                    <th>Marca</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mostrarPerifericos as $mpf)
                                    <tr>
                                        <td>{{ $mpf->periferico->tipoperiferico->NombreTpf }}</td>
                                        <td>{{ $mpf->periferico->CiuPef }}</td>
                                        <td>{{ $mpf->periferico->CodigoInventarioPef }}</td>
                                        <td>{{ $mpf->periferico->MarcaPef }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Sin periféricos
                                            asociados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast-success" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <span id="toast-success-message"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>

        <div id="toast-danger" class="toast align-items-center text-white bg-danger border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <span id="toast-danger-message"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

</div>
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
<script>
    window.addEventListener('abrir-modal', event => {
        const modalId = event.detail.modalId;
        const modalElement = document.getElementById(modalId);
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    });

    window.addEventListener('cerrar-modal', event => {
        const modalId = event.detail.modalId;
        const modalElement = document.getElementById(modalId);
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();
    });
</script>
