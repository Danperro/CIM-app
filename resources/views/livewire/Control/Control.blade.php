<div class="container">
    <h1 class="mb-4">Control de equipos</h1>

    <!-- FILA PRINCIPAL DE FILTROS -->
    <div class="row g-3">
        <!-- Combo: Laboratorio -->
        <div class="col-md-3">
            <label for="idLab" class="form-label fw-bold">Seleccionar laboratorio</label>
            <select id="idLab" wire:model.live="idLab" class="form-select">
                <option value="" hidden>Selecciona un laboratorio</option>
                @foreach ($labc as $lab)
                    <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                @endforeach
            </select>
        </div>

        <!-- Buscar equipo -->
        <div class="col-md-3">
            <label for="busquedaEquipo" class="form-label fw-bold">Buscar equipo</label>
            <input type="text" id="busquedaEquipo" class="form-control" wire:model.debounce.10ms="busquedaEquipo"
                placeholder="Buscar equipo...">
        </div>

        <!-- Combo: Equipo -->
        <div class="col-md-3">
            <label for="idEqo" class="form-label fw-bold">Seleccionar equipo</label>
            <select id="idEqo" wire:model.live="idEqo" class="form-select">
                <option value="" hidden>Seleccionar equipo</option>
                @foreach ($equiposcombo as $eq)
                    <option value="{{ $eq->IdEqo }}">{{ $eq->NombreEqo }}</option>
                @endforeach
            </select>
        </div>

        <!-- Combo: Tipo mantenimiento -->
        <div class="col-md-3">
            <label for="idTpm" class="form-label fw-bold">Tipo</label>
            <select id="idTpm" wire:model.live="idTpm" class="form-select">
                <option value="" hidden>Tipo</option>
                @foreach ($tipoman as $tip)
                    <option value="{{ $tip->IdTpm }}">{{ $tip->NombreTpm }}</option>
                @endforeach
            </select>
        </div>


    </div>

    <!-- SECCIÓN DE MANTENIMIENTO -->
    <form wire:submit.prevent="realizarmantenimiento" class="mt-4">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold fs-5">Tareas de mantenimiento</label>
            </div>

            <!-- Tabla 1 -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Mantenimiento para Software</label>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mantenimiento</th>
                            <th>Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mansoft as $man)
                            <tr>
                                <td>{{ $man->NombreMan }}</td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            wire:click="actualizarSeleccion({{ $man->IdMan }}, $event.target.checked)"
                                            @checked(in_array($man->IdMan, $idMan))>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tabla 2 -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Mantenimiento para Hardware</label>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mantenimiento</th>
                            <th>Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($manhard as $man)
                            <tr>
                                <td>{{ $man->NombreMan }}</td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            wire:click="actualizarSeleccion({{ $man->IdMan }}, $event.target.checked)"
                                            @checked(in_array($man->IdMan, $idMan))>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón Guardar -->
        <div class="col-12 text-end mt-3">
            <button class="btn btn-success fw-bold" type="submit">
                <i class="bi bi-gear me-1"></i> Registrar mantenimiento
            </button>
        </div>
    </form>


    <!-- Botón Finalizar -->
    <div class="col-12 text-end mt-2">
        <button class="btn fw-bold {{ $mantenimientoRealizado ? 'btn-warning' : 'btn-secondary' }}"
            wire:click="mostrarObservacion" data-bs-toggle="modal" data-bs-target="#modalObservacion"
            @if (!$mantenimientoRealizado) disabled @endif>
            <i class="bi bi-check-circle me-1"></i> Observaciones
        </button>

    </div>


    <div wire:ignore.self class="modal fade" id="modalObservacion" tabindex="-1" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="guardarObservacion" class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="modalLabel">Observaciones del laboratorio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Observación</label>
                        <textarea wire:model.live="observacionDtl" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsable</label>
                        <input type="text" wire:model.live="verificadoDtl" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>


</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('toast-success', () => {
            const toastEl = document.getElementById('liveToast');
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    });
</script>
