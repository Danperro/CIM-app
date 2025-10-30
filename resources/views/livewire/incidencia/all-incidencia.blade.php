<section class="container-fluid px-0">
    <!-- Título + CTA -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Incidencias</h1>
        <div class="col-md-2 d-flex align-items-end">
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#kt_modal_create_incidencia">
                <i class="bi bi-plus-lg me-1"></i> Registrar incidencia
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-4">
                    <label for="query" class="form-label">Buscar incidencia</label>
                    <input wire:model.live.debounce.500ms="query" type="text" id="query" class="form-control"
                        placeholder="Nombre o palabra clave">
                </div>

                <div class="col-md-3">
                    <label for="idManFiltro" class="form-label">Mantenimiento</label>
                    <select id="idManFiltro" wire:model.live="idManFiltro" class="form-select">
                        <option value="" hidden>Seleccionar</option>
                        @foreach ($mantenimientos as $man)
                            <option value="{{ $man->IdMan }}">{{ $man->NombreMan ?? '#' . $man->IdMan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="idTpfFiltro" class="form-label">Tipo de periférico</label>
                    <select id="idTpfFiltro" wire:model.live="idTpfFiltro" class="form-select">
                        <option value="" hidden>Seleccionar</option>
                        @foreach ($tiposperiferico as $tpf)
                            <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf ?? '#' . $tpf->IdTpf }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" wire:click.prevent="limpiar">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Mantenimiento</th>
                        <th scope="col">Tipo de periférico</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incidencias as $inc)
                        <tr>
                            <td>{{ $inc->NombreInc }}</td>
                            <td>{{ $inc->mantenimiento->NombreMan ?? '—' }}</td>
                            <td>
                                {{-- Nota: la relación en el componente usa with(['mantenimiento','tipoperiferico']),
                                     pero el listado pasado a la vista es $tiposperiferico. Si tu relación real es tipoperiferico,
                                     ajusta el with() en el componente a ->with(['mantenimiento','tipoperiferico']) --}}
                                {{ optional($inc->tipoperiferico ?? $inc->tipoperiferico)->NombreTpf ?? (optional($inc->tipoperiferico)->NombreTpf ?? '—') }}
                            </td>
                            <td>
                                <span role="button" class="badge {{ $inc->EstadoInc ? 'bg-success' : 'bg-danger' }}"
                                    wire:click="toggleEstado({{ $inc->IdInc }})">
                                    {{ $inc->EstadoInc ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-warning btn-sm" wire:click="selectInfo({{ $inc->IdInc }})"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_incidencia" title="Editar">
                                    <i class="bi bi-pencil-square me-1"></i>
                                </button>

                                <button class="btn btn-danger btn-sm" wire:click="selectInfo({{ $inc->IdInc }})"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_eliminar_incidencia"
                                    title="Eliminar">
                                    <i class="bi bi-trash me-1"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <!-- Estado vacío -->
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Sin datos. Aquí aparecerán las incidencias.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pie de tabla: paginación/contador -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                @if ($incidencias->count())
                    Mostrando {{ $incidencias->firstItem() }}–{{ $incidencias->lastItem() }} de
                    {{ $incidencias->total() }}
                @else
                    Mostrando 0 de 0
                @endif
            </div>
            <nav aria-label="Paginación">
                {{ $incidencias->onEachSide(1)->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>

    <!-- Modal: Registrar incidencia -->
    <div wire:ignore.self class="modal fade" id="kt_modal_create_incidencia" tabindex="-1"
        aria-labelledby="modalLabelCreateInc" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelCreateInc">Registrar incidencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form wire:submit.prevent="registrarIncidencia" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="idMan" class="form-label">Mantenimiento</label>
                            <select id="idMan" class="form-select" wire:model.live="idMan" required>
                                <option value="" hidden>Seleccione el mantenimiento</option>
                                @foreach ($mantenimientos as $man)
                                    <option value="{{ $man->IdMan }}">{{ $man->NombreMan ?? '#' . $man->IdMan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idMan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="idTpf" class="form-label">Tipo de periférico</label>
                            <select id="idTpf" class="form-select" wire:model.live="idTpf" required>
                                <option value="" hidden>Seleccione el tipo</option>
                                @foreach ($tiposperiferico as $tpf)
                                    <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf ?? '#' . $tpf->IdTpf }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idTpf')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nombreInc" class="form-label">Nombre de la incidencia</label>
                            <input type="text" id="nombreInc" class="form-control"
                                placeholder="Ej: Falla de teclado"
                                 wire:model.live="nombreInc" required>
                            @error('nombreInc')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <small class="text-muted d-block mt-3">Completa los campos y presiona Guardar.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Cancelar
                        </button>

                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="registrarIncidencia" @disabled($llave || $errors->any() || empty($nombreInc) || empty($idMan) || empty($idTpf))>
                            <span wire:loading.remove wire:target="registrarIncidencia">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="registrarIncidencia">
                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal: Editar incidencia -->
    <div wire:ignore.self class="modal fade" id="kt_modal_edit_incidencia" tabindex="-1"
        aria-labelledby="modalLabelEditInc" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelEditInc">Editar incidencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form wire:submit.prevent="editarIncidencia" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreInc_edit" class="form-label">Nombre de la incidencia</label>
                            <input type="text" id="nombreInc_edit" class="form-control"
                                placeholder="Ej: Falla de teclado" wire:model.live="nombreInc" required>
                            @error('nombreInc')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="idMan_edit" class="form-label">Mantenimiento</label>
                            <select id="idMan_edit" class="form-select" wire:model.live="idMan" required>
                                <option value="" hidden>Seleccione el mantenimiento</option>
                                @foreach ($mantenimientos as $man)
                                    <option value="{{ $man->IdMan }}">{{ $man->NombreMan ?? '#' . $man->IdMan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idMan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="idTpf_edit" class="form-label">Tipo de periférico</label>
                            <select id="idTpf_edit" class="form-select" wire:model.live="idTpf" required>
                                <option value="" hidden>Seleccione el tipo</option>
                                @foreach ($tiposperiferico as $tpf)
                                    <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf ?? '#' . $tpf->IdTpf }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idTpf')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <small class="text-muted d-block mt-3">Actualiza los campos y presiona Guardar.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Cancelar
                        </button>

                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="editarIncidencia" @disabled($llave || $errors->any() || empty($nombreInc) || empty($idMan) || empty($idTpf))>
                            <span wire:loading.remove wire:target="editarIncidencia">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="editarIncidencia">
                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal: Eliminar incidencia -->
    <div wire:ignore.self class="modal fade" id="kt_modal_eliminar_incidencia" tabindex="-1"
        aria-labelledby="modalLabelDeleteInc" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="eliminarIncidencia" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelDeleteInc">Eliminar incidencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar la incidencia
                        <strong>{{ $nombreInc }}</strong>?
                    </p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <span wire:loading.remove wire:target="eliminarIncidencia">Eliminar</span>
                        <span wire:loading wire:target="eliminarIncidencia">
                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                            Eliminando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: mensaje de confirmación -->
    <div class="modal fade" id="appModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i id="appModalIcon" class="{{ $modalIcon }}" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">{{ $modalTitle }}</h4>
                    <p class="text-muted mb-0">{{ $modalMessage }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Utilidades de modales -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const iconMap = {
            success: 'bi-check-circle-fill text-success',
            warning: 'bi-exclamation-triangle-fill text-warning',
            danger: 'bi-x-circle-fill text-danger',
            info: 'bi-info-circle-fill text-info'
        };

        let lastActiveElement = null;

        function getModalInstance(el) {
            return bootstrap.Modal.getOrCreateInstance(el, {
                backdrop: 'static',
                keyboard: true
            });
        }

        function blurInside(el) {
            const active = document.activeElement;
            if (active && el.contains(active)) {
                try {
                    active.blur();
                } catch {}
            }
            if (document.activeElement === el) {
                try {
                    el.blur();
                } catch {}
            }
        }

        function restoreFocus() {
            if (lastActiveElement && document.contains(lastActiveElement)) {
                try {
                    lastActiveElement.focus({
                        preventScroll: true
                    });
                } catch {}
            } else {
                try {
                    document.body.focus();
                } catch {}
            }
        }

        function cleanBackdrops() {
            document.querySelectorAll('.modal-backdrop.show').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
        }

        function showAppModal(d) {
            if (!d || typeof d !== 'object') d = {};
            const payload = {
                title: d.title ?? 'Aviso',
                message: d.message ?? '',
                variant: d.variant ?? 'info',
                autoclose: Number(d.autoclose ?? 2000)
            };

            const modalEl = document.getElementById('appModal');
            if (!modalEl) return;

            const iconEl = modalEl.querySelector('#appModalIcon');
            const titleEl = modalEl.querySelector('.modal-body h4') || modalEl.querySelector('h4');
            const msgEl = modalEl.querySelector('.modal-body p') || modalEl.querySelector('p');

            if (iconEl) iconEl.className = `bi ${iconMap[payload.variant] || iconMap.info}`;
            if (titleEl) titleEl.textContent = payload.title;
            if (msgEl) msgEl.textContent = payload.message;

            lastActiveElement = document.activeElement;
            const modal = getModalInstance(modalEl);
            if (!modalEl.classList.contains('show')) modal.show();

            if (!Number.isNaN(payload.autoclose) && payload.autoclose > 0) {
                setTimeout(() => {
                    try {
                        safeHide('appModal');
                    } catch {}
                }, payload.autoclose);
            }
        }

        function safeHide(id) {
            const el = document.getElementById(id);
            if (!el) return;
            blurInside(el);
            const modal = getModalInstance(el);
            modal.hide();
        }

        document.getElementById('appModal')?.addEventListener('hidden.bs.modal', () => {
            restoreFocus();
            cleanBackdrops();
        });

        document.addEventListener('livewire:init', () => {
            if (window.Livewire && typeof Livewire.on === 'function') {
                Livewire.on('modal-open', (event) => {
                    const data = event?.payload ?? event ?? {};
                    showAppModal(data);
                });
            }
        });

        window.addEventListener('modal-open', (e) => {
            const d = e?.detail?.payload ?? e?.detail ?? {};
            showAppModal(d);
        });

        window.addEventListener('cerrar-modal', (e) => {
            const id = e?.detail?.modalId;
            if (!id) return;
            safeHide(id);
        });
    });
</script>
