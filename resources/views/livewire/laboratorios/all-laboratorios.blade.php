<section class="container-fluid px-0">
    <!-- Título + CTA -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Laboratorios</h1>
        <div class="col-md-2 d-flex align-items-end">
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#kt_modal_create_laboratorio">
                <i class="bi bi-plus-lg me-1"></i> Registrar laboratorio
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-6">
                    <label for="query" class="form-label">Buscar laboratorio</label>
                    <input wire:model.live.debounce.500ms="query" type="text" id="query" class="form-control"
                        placeholder="Nombre o palabra clave">
                </div>

                <div class="col-md-4">
                    <label for="idAreFiltro" class="form-label">Área</label>
                    <select id="idAreFiltro" wire:model.live="idAreFiltro" name="area" class="form-select">
                        <option value="" hidden>Seleccionar</option>
                        @foreach ($areas as $are)
                            <option value="{{ $are->IdAre }}">{{ $are->NombreAre }}</option>
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
                        <th scope="col">Área</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laboratorios as $lab)
                        <tr>
                            <td>{{ $lab->NombreLab }}</td>
                            <td>{{ $lab->area->NombreAre ?? '—' }}</td>
                            <td>
                                <span role="button" class="badge {{ $lab->EstadoLab ? 'bg-success' : 'bg-danger' }}"
                                    wire:click="toggleEstado({{ $lab->IdLab }})">
                                    {{ $lab->EstadoLab ? 'Activo' : 'Inactivo' }}</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-warning btn-sm" wire:click="selectInfo({{ $lab->IdLab }})"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_laboratorio">
                                    <i class="bi bi-pencil-square me-1"></i>
                                </button>

                                <button class="btn btn-danger btn-sm" wire:click="selectInfo({{ $lab->IdLab }})"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_eliminar_laboratorio">
                                    <i class="bi bi-trash me-1"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <!-- Estado vacío -->
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Sin datos. Aquí aparecerán los laboratorios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pie de tabla: paginación/contador -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                @if ($laboratorios->count())
                    Mostrando {{ $laboratorios->firstItem() }}–{{ $laboratorios->lastItem() }} de
                    {{ $laboratorios->total() }}
                @else
                    Mostrando 0 de 0
                @endif
            </div>
            <nav aria-label="Paginación">
                {{ $laboratorios->onEachSide(1)->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>

    <!-- Modal: Registrar laboratorio -->
    <div wire:ignore.self class="modal fade" id="kt_modal_create_laboratorio" tabindex="-1"
        aria-labelledby="modalLabelCreateLab" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelCreateLab">Registrar laboratorio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form wire:submit.prevent="registrarLaboratorio" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="idAre" class="form-label">Área</label>
                            <select id="idAre" name="idAre" class="form-select" wire:model.live="idAre" required>
                                <option value="" hidden>Seleccione el área</option>
                                @foreach ($areas as $are)
                                    <option value="{{ $are->IdAre }}">{{ $are->NombreAre }}</option>
                                @endforeach
                            </select>
                            @error('idAre')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nombreLab" class="form-label">Nombre del laboratorio</label>
                            <input type="text" id="nombreLab" name="nombreLab" class="form-control"
                                placeholder="Ej: Lab de Química" wire:model.live="nombreLab" required>
                            @error('nombreLab')
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
                            wire:target="registrarLaboratorio" @disabled($llave || $errors->any() || empty($nombreLab) || empty($idAre))>
                            <span wire:loading.remove wire:target="registrarLaboratorio">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="registrarLaboratorio">
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

    <!-- Modal: Editar laboratorio -->
    <div wire:ignore.self class="modal fade" id="kt_modal_edit_laboratorio" tabindex="-1"
        aria-labelledby="modalLabelEditLab" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelEditLab">Editar laboratorio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form wire:submit.prevent="editarLaboratorio" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreLab_edit" class="form-label">Nombre del laboratorio</label>
                            <input type="text" id="nombreLab_edit" name="nombreLab_edit" class="form-control"
                                placeholder="Ej: Lab de Química" wire:model.live="nombreLab" required>
                            @error('nombreLab')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="idAre_edit" class="form-label">Área</label>
                            <select id="idAre_edit" name="idAre_edit" class="form-select" wire:model.live="idAre"
                                required>
                                <option value="" hidden>Seleccione el área</option>
                                @foreach ($areas as $are)
                                    <option value="{{ $are->IdAre }}">{{ $are->NombreAre }}</option>
                                @endforeach
                            </select>
                            @error('idAre')
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
                            wire:target="editarLaboratorio" @disabled($llave || $errors->any() || empty($nombreLab) || empty($idAre))>
                            <span wire:loading.remove wire:target="editarLaboratorio">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="editarLaboratorio">
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

    <!-- Modal: Eliminar laboratorio -->
    <div wire:ignore.self class="modal fade" id="kt_modal_eliminar_laboratorio" tabindex="-1"
        aria-labelledby="modalLabelDeleteLab" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="eliminarLaboratorio" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelDeleteLab">Eliminar laboratorio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar el laboratorio
                        <strong>{{ $nombreLab }}</strong>?
                    </p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <span wire:loading.remove wire:target="eliminarLaboratorio">Eliminar</span>
                        <span wire:loading wire:target="eliminarLaboratorio">
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
