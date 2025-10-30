<section class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Reportes de Validacion del Estado de Conectividad y Verificación de Incidencias de Riesgos Informáticos</h1>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="usuario" class="form-label fw-bold">Ingrese usuario</label>
                    <input type="text" id="usuario" class="form-control" placeholder="Nombre del usuario"
                        wire:model.live.debounce.300ms="usuario">
                </div>
                <div class="col-md-3">
                    <label for="idLab" class="form-label fw-bold">Laboratorios</label>
                    <select id="idLab" wire:model.live="idLab" class="form-select">
                        <option value="" hidden>Seleccionar un Laboratorio</option>
                        @foreach ($laboratorios as $lab)
                            <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="idLab" class="form-label fw-bold">Fecha de registro</label>
                    <input type="date" wire:model.live="fechaDtl" class="form-control">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="button" class="btn btn-outline-secondary w-100 w-md-auto"
                        wire:click="limpiarfiltros">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>

                </div>

            </div>
        </div>
    </div>



    <!-- Tabla de equipos -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Laboratorio</th>
                        <th>Realizado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dtlab as $dtl)
                        <tr>
                            <td>{{ $dtl->laboratorio->NombreLab }}</td>
                            <td>{{ $dtl->RealizadoDtl }}</td>
                            <td>{{ $dtl->FechaDtl }}</td>
                            <td class="text-center ">
                                <a href="{{ route('reporte.pdf', $dtl->IdDtl) }}"
                                    class="btn btn-success btn-sm w-20 w-md-auto" target="_blank">
                                    <i class="bi bi-file-earmark-arrow-down me-1"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pie de tabla: paginación/contador -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                @if ($dtlab->count())
                    Mostrando {{ $dtlab->firstItem() }}–{{ $dtlab->lastItem() }} de
                    {{ $dtlab->total() }}
                @else
                    Mostrando 0 de 0
                @endif
            </div>
            <nav aria-label="Paginación">
                {{ $dtlab->onEachSide(1)->links() }}
            </nav>
        </div>
    </div>
</section>
