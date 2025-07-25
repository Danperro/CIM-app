<div class="container">
    <h1 class="mb-4">Reportes</h1>

    <!-- Formulario de registro -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="" class="form-label fw-bold">Ingrese usuario</label>
            <input type="text" id="" class="form-control" placeholder="Nombre del usuario">
        </div>
        <div class="col-md-4">
            <label for="idLab" class="form-label fw-bold">Laboratorios</label>
            <select id="idLab" wire:model.live="idLab" class="form-select">
                <option value="" hidden>Seleccionar un Laboratorio</option>
                @foreach ($laboratorios as $lab)
                    <option value="{{ $lab->IdLab }}">{{ $lab->NombreLab }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="idLab" class="form-label fw-bold">Fecha de registro</label>
            <input type="date" wire:model.live="fechaDtl" class="form-control">
        </div>
    </div>

    <!-- Tabla de equipos -->
    <div class="mb-4">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Laboratorio</th>
                    <th>Realizado</th>
                    <th>Responsable</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dtlab as $dtl)
                    <tr>
                        <td>{{ $dtl->laboratorio->NombreLab }}</td>
                        <td>{{ $dtl->RealizadoDtl }}</td>
                        <td>{{ $dtl->VerificadoDtl }}</td>
                        <td>{{ $dtl->FechaDtl }}</td>
                        <td>
                            <a href="{{ route('reporte.pdf', $dtl->IdDtl) }}" class="btn btn-success btn-sm"
                                target="_blank">
                                Descargar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>
