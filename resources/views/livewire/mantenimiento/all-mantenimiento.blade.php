<section class="container-fluid px-0">
    <!-- Título + CTA -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Mantenimientos</h1>
        <div class="col-md-2 d-flex align-items-end">
            <a href="#" class="btn btn-success" data-bs-toggle="modal"
                data-bs-target="#kt_modal_create_mantenimiento">
                <i class="bi bi-plus-lg me-1"></i> Registrar mantenimiento
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-4">
                    <label for="q" class="form-label">Buscar mantenimiento</label>
                    <input wire:model.live.debounce.500ms="query" type="text" id="query" class="form-control"
                        placeholder="Nombre del mantenimiento">
                </div>
                <div class="col-md-3">
                    <label for="idTpmFiltro" class="form-label">Tipo</label>
                    <select id="idTpmFiltro" wire:model.live="idTpmFiltro" name="tipo" class="form-select">
                        <option value="" hidden>Seleccionar</option>
                        @foreach ($tipomantenimientos as $tpm)
                            <option value="{{ $tpm->IdTpm }}">{{ $tpm->NombreTpm }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="idClmFiltro" class="form-label">Clase</label>
                    <select id="idClmFiltro" wire:model.live="idClmFiltro" name="clase" class="form-select">
                        <option value="" hidden>Seleccionar</option>
                        @foreach ($clasemantenimientos as $clm)
                            <option value="{{ $clm->IdClm }}">{{ $clm->NombreClm }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" wire:click.prevent="limpiar">
                        <i class="bi bi-eraser me-1"></i> Limpiar filtros
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
                        <th scope="col">Mantenimiento</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Clase</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mantenimientos as $man)
                        <tr>
                            <td>{{ $man->NombreMan }}</td>
                            <td>{{ $man->tipomantenimiento->NombreTpm }}</td>
                            <td>{{ $man->clasemantenimiento->NombreClm }}</td>
                            <td>
                                <span role="button" 
                                class="badge {{ $man->EstadoMan ? 'bg-success' : 'bg-danger' }}"
                                    wire:click="toggleEstado({{ $man->IdMan }})">
                                    {{ $man->EstadoMan ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-warning btn-sm" wire:click="selectInfo({{ $man->IdMan }})"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_mantenimiento">
                                    <i class="bi bi-pencil-square me-1"></i>
                                </button>

                                <button class="btn btn-danger btn-sm" wire:click="selectInfo({{ $man->IdMan }})"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_eliminar_mantenimiento">
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
                    @if ($mantenimientos->count())
                        Mostrando {{ $mantenimientos->firstItem() }}–{{ $mantenimientos->lastItem() }} de
                        {{ $mantenimientos->total() }}
                    @else
                        Mostrando 0 de 0
                    @endif
                </div>
                <nav aria-label="Paginación">
                    {{ $mantenimientos->onEachSide(1)->links() }}
                </nav>
            </div>
        </div>
    </div>
    <!-- Modal: Registrar mantenimiento -->
    <div wire:ignore.self class="modal fade" id="kt_modal_create_mantenimiento" tabindex="-1"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar mantenimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <!-- Cascarón de formulario (sin lógica) -->
                <form wire:submit.prevent="registrarMantenimiento" novalidate>
                    <div class="modal-body">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="idTpm" class="form-label">Tipo</label>
                                <select id="idTpm" name="tipo" class="form-select" wire:model.live="idTpm"
                                    required>
                                    <option value="" hidden>Seleccione el tipo</option>
                                    @foreach ($tipomantenimientos as $tpm)
                                        <option value="{{ $tpm->IdTpm }}">{{ $tpm->NombreTpm }}</option>
                                    @endforeach
                                </select>
                                @error('idTpm')
                                    <div id="err-edit-tipo" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="idClm" class="form-label">Clase</label>
                                <select id="idClm" name="clase" class="form-select" wire:model.live="idClm"
                                    required>
                                    <option value="" hidden>Seleccione la clase</option>
                                    @foreach ($clasemantenimientos as $clm)
                                        <option value="{{ $clm->IdClm }}">{{ $clm->NombreClm }}</option>
                                    @endforeach
                                </select>
                                @error('idClm')
                                    <div id="err-edit-clase" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nombreMan" class="form-label">Nombre del mantenimiento</label>
                            <input type="text" id="nombreMan" name="nombreMan" class="form-control"
                                placeholder="Ej: Actualización de SO" wire:model.live="nombreMan" required>
                            @error('nombreMan')
                                <div id="err-edit-nombre" class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted d-block mt-3">Completa los campos y presiona Guardar.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="registrarMantenimiento" @disabled($llave || $errors->any() || empty($nombreMan) || empty($idTpm) || empty($idClm))>

                            <span wire:loading.remove wire:target="registrarMantenimiento">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="registrarMantenimiento">
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

    <!-- Modal: Editar mantenimiento -->
    <div wire:ignore.self class="modal fade" id="kt_modal_edit_mantenimiento" tabindex="-1"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar mantenimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <!-- Cascarón de formulario (sin lógica) -->
                <form wire:submit.prevent="editarMantenimiento" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreMan" class="form-label">Nombre del mantenimiento</label>
                            <input type="text" id="nombreMan" name="nombreMan" class="form-control"
                                placeholder="Ej: Actualización de SO" wire:model.live="nombreMan" required>
                            @error('nombreMan')
                                <div id="err-edit-nombre" class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="idTpm" class="form-label">Tipo</label>
                                <select id="idTpm" name="tipo" class="form-select" wire:model.live="idTpm"
                                    required>
                                    <option value="" hidden>Seleccione el tipo</option>
                                    @foreach ($tipomantenimientos as $tpm)
                                        <option value="{{ $tpm->IdTpm }}">{{ $tpm->NombreTpm }}</option>
                                    @endforeach
                                </select>
                                @error('idTpm')
                                    <div id="err-edit-tipo" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="idClm" class="form-label">Clase</label>
                                <select id="idClm" name="clase" class="form-select" wire:model.live="idClm"
                                    required>
                                    <option value="" hidden>Seleccione la clase</option>
                                    @foreach ($clasemantenimientos as $clm)
                                        <option value="{{ $clm->IdClm }}">{{ $clm->NombreClm }}</option>
                                    @endforeach
                                </select>
                                @error('idClm')
                                    <div id="err-edit-clase" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <small class="text-muted d-block mt-3">Completa los campos y presiona Guardar.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="editarMantenimiento" @disabled($llave || $errors->any() || empty($nombreMan) || empty($idTpm) || empty($idClm))>

                            <span wire:loading.remove wire:target="editarMantenimiento">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="editarMantenimiento">
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

    <!-- Modal: Eliminar mantenimiento -->
    <div wire:ignore.self class="modal fade" id="kt_modal_eliminar_mantenimiento" tabindex="-1"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="eliminarMantenimiento" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Eliminar Mantenimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <p>¿Estás seguro que deseas eliminar el Mantenimiento <strong>{{ $nombreMan }}</strong>?
                            </p>
                            <p class="text-muted">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <span wire:loading.remove wire:target="eliminarMantenimiento">Eliminar</span>
                        <span wire:loading wire:target="eliminarMantenimiento">
                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                            Eliminando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal: mensaje de confimacion mantenimiento -->
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const iconMap = {
            success: 'bi-check-circle-fill text-success',
            warning: 'bi-exclamation-triangle-fill text-warning',
            danger: 'bi-x-circle-fill text-danger',
            info: 'bi-info-circle-fill text-info'
        };

        // Guardamos quién tenía el foco antes de abrir el modal
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
            // Por si el foco quedó en el propio contenedor del modal
            if (document.activeElement === el) {
                try {
                    el.blur();
                } catch {}
            }
        }

        function restoreFocus() {
            // Si el elemento guardado sigue en el DOM y visible, volvemos el foco ahí.
            if (lastActiveElement && document.contains(lastActiveElement)) {
                try {
                    lastActiveElement.focus({
                        preventScroll: true
                    });
                } catch {}
            } else {
                // Plan B: foco al body
                try {
                    document.body.focus();
                } catch {}
            }
        }

        function cleanBackdrops() {
            // A veces Bootstrap deja un backdrop huérfano si hubo errores
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

            // Guardar dónde estaba el foco antes de abrir
            lastActiveElement = document.activeElement;

            const modal = getModalInstance(modalEl);

            // Evita doble show si ya está visible
            if (!modalEl.classList.contains('show')) modal.show();

            if (!Number.isNaN(payload.autoclose) && payload.autoclose > 0) {
                setTimeout(() => {
                    try {
                        safeHide('appModal');
                    } catch {}
                }, payload.autoclose);
            }
        }

        // Cierre seguro: quita foco interno y luego cierra
        function safeHide(id) {
            const el = document.getElementById(id);
            if (!el) return;
            blurInside(el);
            const modal = getModalInstance(el);
            modal.hide();
        }

        // Eventos de ciclo de vida del modal para sanear el foco y los backdrops
        document.getElementById('appModal')?.addEventListener('hidden.bs.modal', () => {
            // El modal terminó de cerrarse: restaurar foco y limpiar backdrop
            restoreFocus();
            cleanBackdrops();
        });

        // Livewire v3
        document.addEventListener('livewire:init', () => {
            if (window.Livewire && typeof Livewire.on === 'function') {
                Livewire.on('modal-open', (event) => {
                    const data = event?.payload ?? event ?? {};
                    showAppModal(data);
                });
            }
        });

        // Fallback manual
        window.addEventListener('modal-open', (e) => {
            const d = e?.detail?.payload ?? e?.detail ?? {};
            showAppModal(d);
        });

        // Cerrar desde Livewire: 'cerrar-modal' con { modalId }
        window.addEventListener('cerrar-modal', (e) => {
            const id = e?.detail?.modalId;
            if (!id) return;
            safeHide(id);
        });
    });
</script>
