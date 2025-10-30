<section class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Equipos</h1>
        <div class="col-md-2 d-flex align-items-end">
            <a href="#" class="btn btn-success"
             data-bs-toggle="modal"
              data-bs-target="#kt_modal_create_equipo"
                wire:click="limpiar()">
                <i class="bi bi-plus-lg me-1"></i>Registrar Equipo
            </a>
        </div>
    </div>

    <!-- Formulario de registro -->
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-4">
                    <label for="" class="form-label fw-bold">Ingrese equipo</label>
                    <input wire:model.live.debounce.500ms="query"
                     type="text" id="query" class="form-control"
                        placeholder="Nombre del Equipo">
                </div>
                <div class="col-md-4">
                    <label for="idLabFiltro" class="form-label fw-bold">Laboratorios</label>
                    <select id="idLabFiltro" 
                    wire:model.live="idLabFiltro"
                     class="form-select">
                        <option value="" hidden>Seleccionar un laboratorio</option>
                        @foreach ($laboratorios as $lab)
                            <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" 
                    class="btn btn-outline-secondary w-100"
                    wire:click.prevent="limpiar">
                <i class="bi bi-eraser me-1"></i>
            Limpiar Filtros</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de equipos -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Laboratorio</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
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
                                    <i class="bi bi-eye"></i></button>

                                <button class="btn btn-warning btn-sm"
                                    wire:click="selectEditarEquipo({{ $eqo->IdEqo }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button wire:click="selectInfo({{ $eqo->IdEqo }})" class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_eliminar_equipo">
                                    <i class="bi bi-trash me-1"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pie de tabla: paginación/contador -->
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    @if ($equipos->count())
                        Mostrando {{ $equipos->firstItem() }}–{{ $equipos->lastItem() }} de
                        {{ $equipos->total() }}
                    @else
                        Mostrando 0 de 0
                    @endif
                </div>
                <nav aria-label="Paginación">
                    {{ $equipos->onEachSide(1)->links() }}
                </nav>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="kt_modal_editar_equipo" tabindex="-1"
        aria-labelledby="modalLabelEditar" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form wire:submit.prevent="actualizarEquipo" class="modal-content" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelEditar">Editar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        {{-- Izquierda --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" wire:model.live="nombreEqo"
                                    class="form-control @error('nombreEqo') is-invalid @enderror"
                                    aria-describedby="err-edit-nombre">
                                @error('nombreEqo')
                                    <div id="err-edit-nombre" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Código</label>
                                <input type="text" wire:model.live="codigoEqo" inputmode="numeric" pattern="\d*"
                                    autocomplete="off" class="form-control @error('codigoEqo') is-invalid @enderror"
                                    aria-describedby="err-edit-codigo">
                                @error('codigoEqo')
                                    <div id="err-edit-codigo" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="idLab" class="form-label">Laboratorio</label>
                                <select id="idLab" wire:model.live="idLab"
                                    class="form-select @error('idLab') is-invalid @enderror"
                                    aria-describedby="err-edit-lab">
                                    <option value="">Selecciona Laboratorio</option>
                                    @foreach ($laboratorios as $lab)
                                        <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                                    @endforeach
                                </select>
                                @error('idLab')
                                    <div id="err-edit-lab" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tipos requeridos --}}
                            @php
                                $tpfSel = collect($perifericosSeleccionados)->pluck('IdTpf');
                                $req = [1 => 'Monitor', 2 => 'CPU', 3 => 'Teclado', 4 => 'Ratón'];
                            @endphp
                            <div class="mb-2">
                                <div class="fw-semibold mb-1">Tipos requeridos</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($req as $id => $label)
                                        <span
                                            class="badge {{ $tpfSel->contains($id) ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $label }}
                                            @if ($tpfSel->contains($id))
                                                <i class="bi bi-check-circle ms-1"></i>
                                            @else
                                                <i class="bi bi-dash-circle ms-1"></i>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Periféricos seleccionados</label>
                                <div class="table-responsive border rounded">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Código CIU</th>
                                                <th>Marca</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($perifericosSeleccionados as $p)
                                                <tr wire:key="edit-sel-{{ $p['IdPef'] }}">
                                                    <td>{{ $p['tipoperiferico']['NombreTpf'] ?? '---' }}</td>
                                                    <td>{{ $p['CiuPef'] }}</td>
                                                    <td>{{ $p['MarcaPef'] }}</td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            wire:click="quitarPeriferico({{ $p['IdPef'] }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="quitarPeriferico"
                                                            class="btn btn-danger btn-sm">
                                                            <i class="bi bi-x-circle"></i> Quitar
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Sin periféricos
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @error('perifericosSeleccionados')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Derecha --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="idTpf" class="form-label">Filtrar por tipo de periférico</label>
                                <select id="idTpf" wire:model.live="idTpf" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    @foreach ($tiposperifericos as $tpf)
                                        <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="table-responsive mb-3" style="max-height: 320px; overflow-y: auto;">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Código CIU</th>
                                            <th>Marca</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mostrarPerifericosNoAsignados as $mpf)
                                            @if (empty($idTpf) || $mpf->IdTpf == $idTpf)
                                                @php
                                                    $ya4 = count($perifericosSeleccionados) >= 4;
                                                    $repet = collect($perifericosSeleccionados)
                                                        ->pluck('IdTpf')
                                                        ->contains($mpf->IdTpf);
                                                @endphp
                                                <tr wire:key="edit-dispo-{{ $mpf->IdPef }}">
                                                    <td>{{ $mpf->tipoperiferico->NombreTpf }}</td>
                                                    <td>{{ $mpf->CiuPef }}</td>
                                                    <td>{{ $mpf->MarcaPef }}</td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            wire:click="agregarPeriferico({{ $mpf->IdPef }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="agregarPeriferico"
                                                            class="btn btn-success btn-sm"
                                                            {{ $ya4 || $repet ? 'disabled' : '' }}>
                                                            <i class="bi bi-plus-circle"></i> Agregar
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay periféricos
                                                    disponibles</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                @php
                    $puedeActualizar =
                        !empty($nombreEqo) &&
                        mb_strlen((string) $nombreEqo) >= 3 &&
                        !empty($idLab) &&
                        count($perifericosSeleccionados) === 4;
                @endphp
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                        {{ $puedeActualizar ? '' : 'disabled' }}>
                        <span wire:loading.remove>Actualizar</span>
                        <span wire:loading class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>



    <div wire:ignore.self class="modal fade" id="kt_modal_create_equipo" tabindex="-1" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form wire:submit.prevent="registrarEquipo" class="modal-content" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="col-md-6">

                            {{-- NOMBRE --}}
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" wire:model.live="nombreEqo"
                                    class="form-control @error('nombreEqo') is-invalid @enderror"
                                    aria-describedby="err-nombre">
                                @error('nombreEqo')
                                    <div id="err-nombre" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CÓDIGO (OPCIONAL, SÓLO NÚMEROS) --}}
                            <div class="mb-3">
                                <label class="form-label">Código (opcional, solo números)</label>
                                <input type="text" wire:model.live="codigoEqo" inputmode="numeric" pattern="\d*"
                                    autocomplete="off" class="form-control @error('codigoEqo') is-invalid @enderror"
                                    aria-describedby="err-codigo">
                                @error('codigoEqo')
                                    <div id="err-codigo" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- LABORATORIO --}}
                            <div class="mb-3">
                                <label for="idLab" class="form-label">Laboratorio</label>
                                <select id="idLab" wire:model.live="idLab"
                                    class="form-select @error('idLab') is-invalid @enderror"
                                    aria-describedby="err-lab">
                                    <option value="">Selecciona Laboratorio</option>
                                    @foreach ($laboratorios as $lab)
                                        <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                                    @endforeach
                                </select>
                                @error('idLab')
                                    <div id="err-lab" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CHECKLIST TIPOS REQUERIDOS --}}
                            @php
                                $tpfSel = collect($perifericosSeleccionados)->pluck('IdTpf');
                                $req = [1 => 'Monitor', 2 => 'CPU', 3 => 'Teclado', 4 => 'Ratón'];
                            @endphp
                            <div class="mb-2">
                                <div class="fw-semibold mb-1">Tipos requeridos</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($req as $id => $label)
                                        <span
                                            class="badge {{ $tpfSel->contains($id) ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $label }}
                                            @if ($tpfSel->contains($id))
                                                <i class="bi bi-check-circle ms-1"></i>
                                            @else
                                                <i class="bi bi-dash-circle ms-1"></i>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- TABLA DE PERIFÉRICOS SELECCIONADOS --}}
                            <div class="mb-3">
                                <label class="form-label">Periféricos seleccionados</label>
                                <div class="table-responsive border rounded">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Código CIU</th>
                                                <th>Marca</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($perifericosSeleccionados as $p)
                                                <tr wire:key="sel-{{ $p['IdPef'] }}">
                                                    <td>{{ $p['tipoperiferico']['NombreTpf'] ?? '---' }}</td>
                                                    <td>{{ $p['CiuPef'] }}</td>
                                                    <td>{{ $p['MarcaPef'] }}</td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            wire:click="quitarPeriferico({{ $p['IdPef'] }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="quitarPeriferico"
                                                            class="btn btn-danger btn-sm">
                                                            <i class="bi bi-x-circle"></i> Quitar
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Sin periféricos
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @error('perifericosSeleccionados')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="idTpf" class="form-label">Filtrar por tipo</label>
                                <select id="idTpf" wire:model.live="idTpf" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    @foreach ($tiposperifericos as $tpf)
                                        <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="table-responsive mb-3" style="max-height: 320px; overflow-y: auto;">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Código CIU</th>
                                            <th>Marca</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mostrarPerifericosNoAsignados as $mpf)
                                            @if (empty($idTpf) || $mpf->IdTpf == $idTpf)
                                                @php
                                                    $ya4 = count($perifericosSeleccionados) >= 4;
                                                    $repet = collect($perifericosSeleccionados)
                                                        ->pluck('IdTpf')
                                                        ->contains($mpf->IdTpf);
                                                @endphp
                                                <tr wire:key="dispo-{{ $mpf->IdPef }}">
                                                    <td>{{ $mpf->tipoperiferico->NombreTpf }}</td>
                                                    <td>{{ $mpf->CiuPef }}</td>
                                                    <td>{{ $mpf->MarcaPef }}</td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            wire:click="agregarPeriferico({{ $mpf->IdPef }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="agregarPeriferico"
                                                            class="btn btn-success btn-sm"
                                                            {{ count($perifericosSeleccionados) >= 4 ||
                                                            collect($perifericosSeleccionados)->pluck('IdTpf')->contains($mpf->IdTpf)
                                                                ? 'disabled'
                                                                : '' }}>
                                                            <i class="bi bi-plus-circle"></i> Agregar
                                                        </button>

                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay periféricos
                                                    disponibles</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $puedeGuardar =
                        !empty($nombreEqo) &&
                        mb_strlen((string) $nombreEqo) >= 3 &&
                        !empty($idLab) &&
                        count($perifericosSeleccionados) === 4;
                @endphp
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                        {{ $puedeGuardar ? '' : 'disabled' }}>
                        <span wire:loading.remove>Guardar</span>
                        <span wire:loading class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </button>
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
    <!-- Modal de confirmación de éxito (reutilizable) -->
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

</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('toast-success', (event) => {
            // Cierra modales por si quedara alguno abierto
            ['kt_modal_create_equipo', 'kt_modal_editar_equipo', 'kt_modal_eliminar_equipo']
            .forEach(id => {
                const m = bootstrap.Modal.getInstance(document.getElementById(id));
                if (m) m.hide();
            });

            // Muestra modal de éxito
            const title = event?.title || '¡Éxito!';
            const message = event?.message || 'Operación completada correctamente.';
            document.getElementById('successModalTitle').textContent = title;
            document.getElementById('successModalMessage').textContent = message;

            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            setTimeout(() => successModal.hide(), 2000);

            // (Opcional) también puedes mostrar el toast verde:
            const toastEl = document.getElementById('toast-success');
            if (toastEl) {
                document.getElementById('toast-success-message').textContent = message;
                new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 4000
                }).show();
            }
        });

        Livewire.on('toast-danger', (event) => {
            // Muestra toast rojo de error
            const toastEl = document.getElementById('toast-danger');
            if (toastEl) {
                document.getElementById('toast-danger-message').textContent = event.message ||
                    'Ocurrió un error.';
                new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                }).show();
            }
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
