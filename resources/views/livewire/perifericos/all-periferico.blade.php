<section class="container-fluid px-0">
    <!-- Título + CTA -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Periféricos</h1>
        <div class="col-md-2 d-flex align-items-end">
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#kt_modal_create_periferico">
                <i class="bi bi-plus-lg me-1"></i> Registrar periférico
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-12 col-lg-6">
                    <label for="query" class="form-label">Código de inventario</label>
                    <div class="input-group has-validation">
                        <input id="inpCodigoInv" type="text" class="form-control" placeholder="Ej: 202476673231-0034"
                            wire:model.live="query">
                        <button type="button" class="btn btn-outline-secondary" id="btnScan" title="Escanar codigo"
                            data-bs-toggle="modal" data-bs-target="#scannerModal"><i
                                class="bi bi-upc-scan"></i></button>
                        <div id="codigoFeedback" class="invalid-feedback">
                            @error('query')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <label for="idTpfFiltro" class="form-label">Tipo</label>
                    <select id="idTpfFiltro" wire:model.live="idTpfFiltro" name="tipo" class="form-select">
                        <option value="" hidden>Seleccionar</option>
                        @foreach ($tipoperifericos as $tpf)
                            <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="estadoPefFiltro" class="form-label">Estado</label>
                    <select id="estadoPefFiltro" wire:model.live="estadoPefFiltro" class="form-select">
                        <option value="" hidden>Seleccionar</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-160" wire:click.prevent="limpiar">
                        <i class="bi bi-eraser me-1"></i>
                        Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <form id="form-etiquetas" action="{{ route('perifericos.etiquetas.pdf') }}" method="POST" target="_blank">
        @csrf
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width:36px">
                                <input type="checkbox" id="chk-all"
                                    onclick="document.querySelectorAll('.chk-item').forEach(c=>c.checked=this.checked)">
                            </th>
                            <th scope="col">Código de Inventario</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Color</th>
                            <th scope="col">Codigo CIU</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perifericos as $pef)
                            <tr>
                                <td><input type="checkbox" class="chk-item" name="ids[]" value="{{ $pef->IdPef }}">
                                </td>
                                <td>{{ $pef->CodigoInventarioPef }}</td>
                                <td>{{ $pef->tipoperiferico->NombreTpf ?? '—' }}</td>
                                <td>{{ $pef->MarcaPef }}</td>
                                <td>{{ $pef->ColorPef ?? '—' }}</td>
                                <td>{{ $pef->CiuPef ?? '—' }}</td>
                                <td><span role="button"
                                        class="badge {{ $pef->EstadoPef ? 'bg-success' : 'bg-danger' }}">
                                        {{ $pef->EstadoPef ? 'Activo' : 'Inactivo' }}
                                    </span></td>
                                <td class="text-end">
                                    <button class="btn btn-warning btn-sm" wire:click="selectInfo({{ $pef->IdPef }})"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_edit_periferico">
                                        <i class="bi bi-pencil-square me-1"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm" wire:click="selectInfo({{ $pef->IdPef }})"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_eliminar_periferico">
                                        <i class="bi bi-trash me-1"></i>
                                    </button>
                                    <a class="btn btn-secondary btn-sm"
                                        href="{{ route('perifericos.etiqueta', $pef->IdPef) }}" target="_blank"
                                        rel="noopener">
                                        <i class="bi bi-printer me-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <!-- Estado vacío -->
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Sin datos. Aquí aparecerán los periféricos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-end mb-3">
                <button type="submit" form="form-etiquetas" class="btn btn-primary">
                    Imprimir etiquetas seleccionadas
                </button>
            </div>
            <!-- Pie de tabla: paginación/contador -->
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    @if ($perifericos->count())
                        Mostrando {{ $perifericos->firstItem() }}–{{ $perifericos->lastItem() }} de
                        {{ $perifericos->total() }}
                    @else
                        Mostrando 0 de 0
                    @endif
                </div>
                <nav aria-label="Paginación">
                    {{ $perifericos->onEachSide(1)->links() }}
                </nav>
            </div>
        </div>
    </form>


    <!-- Modal: Registrar periférico -->
    <div wire:ignore.self class="modal fade" id="kt_modal_create_periferico" tabindex="-1"
        aria-labelledby="modalLabelCreatePef" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelCreatePef">Registrar periférico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form wire:submit.prevent="registrarPeriferico" novalidate>
                    <div class="modal-body">
                        <div class="col-md-6">
                            <label for="codigoInventarioPef" class="form-label">Codigo CIU</label>
                            <input type="text" id="codigoInventarioPef" name="codigoInventarioPef"
                                class="form-control" placeholder="Ej: 789341243423 "
                                wire:model.live="codigoInventarioPef">
                            @error('codigoInventarioPef')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="idTpf" class="form-label">Tipo</label>
                                <select id="idTpf" name="idTpf" class="form-select" wire:model.live="idTpf"
                                    required>
                                    <option value="" hidden>Seleccione el tipo</option>
                                    @foreach ($tipoperifericos as $tpf)
                                        <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf }}</option>
                                    @endforeach
                                </select>
                                @error('idTpf')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="marcaPef" class="form-label">Marca</label>
                                <input type="text" id="marcaPef" name="marcaPef" class="form-control"
                                    placeholder="Ej: HP" wire:model.live="marcaPef" required>
                                @error('marcaPef')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label for="colorPef" class="form-label">Color</label>
                                <input type="text" id="colorPef" name="colorPef" class="form-control"
                                    placeholder="Ej: Blanco" wire:model.live="colorPef">
                                @error('colorPef')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="ciuPef" class="form-label">Codigo CIU</label>
                                <input type="text" id="ciuPef" name="ciuPef" class="form-control"
                                    placeholder="Ej: 00098 " wire:model.live="ciuPef">
                                @error('ciuPef')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
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
                            wire:target="registrarPeriferico" @disabled($llave || $errors->any() || empty($codigoInventarioPef) || empty($idTpf) || empty($marcaPef))>
                            <span wire:loading.remove wire:target="registrarPeriferico">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="registrarPeriferico">
                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div> <!-- Modal: Escáner de código de barras -->
    <div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-upc-scan me-2"></i>Escanear código</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-3">
                    <!-- Vista de cámara del tamaño del rectángulo -->
                    <div class="d-flex justify-content-center align-items-center bg-dark rounded"
                        style="height:60vh;">
                        <div id="scannerViewport" class="position-relative"
                            style="width:70%; height:60%; border:3px solid #28a745; border-radius:12px; overflow:hidden;">
                            <video id="scannerVideo" playsinline muted></video>
                            <canvas id="scannerCanvas"></canvas>
                        </div>
                    </div>

                    <div id="scannerStatus" class="small text-muted mt-2">
                        Concede permiso a la cámara y apunta al código. Procura buena luz. Yo hago el resto.
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="switchCamera" class="btn btn-outline-primary" type="button">
                        <i class="bi bi-camera-reverse me-1"></i> Cambiar cámara
                    </button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal: Editar periférico -->
    <div wire:ignore.self class="modal fade" id="kt_modal_edit_periferico" tabindex="-1"
        aria-labelledby="modalLabelEditPef" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelEditPef">Editar periférico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form wire:submit.prevent="editarPeriferico" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="codigoInventarioPef_edit" class="form-label">Código de inventario</label>
                            <input type="text" id="codigoInventarioPef_edit" name="codigoInventarioPef_edit"
                                class="form-control" placeholder="Ej: PEF-0001" wire:model.live="codigoInventarioPef"
                                required>
                            @error('codigoInventarioPef')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="idTpf_edit" class="form-label">Tipo</label>
                                <select id="idTpf_edit" name="idTpf_edit" class="form-select"
                                    wire:model.live="idTpf" required>
                                    <option value="" hidden>Seleccione el tipo</option>
                                    @foreach ($tipoperifericos as $tpf)
                                        <option value="{{ $tpf->IdTpf }}">{{ $tpf->NombreTpf }}</option>
                                    @endforeach
                                </select>
                                @error('idTpf')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="marcaPef_edit" class="form-label">Marca</label>
                                <input type="text" id="marcaPef_edit" name="marcaPef_edit" class="form-control"
                                    placeholder="Ej: HP" wire:model.live="marcaPef" required>
                                @error('marcaPef')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label for="colorPef_edit" class="form-label">Color</label>
                                <input type="text" id="colorPef_edit" name="colorPef_edit" class="form-control"
                                    placeholder="Ej: Blanco" wire:model.live="colorPef">
                                @error('colorPef')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="ciuPef_edit" class="form-label">Ubicación</label>
                                <input type="text" id="ciuPef_edit" name="ciuPef_edit" class="form-control"
                                    placeholder="Ej: Lima / Almacén 1" wire:model.live="ciuPef">
                                @error('ciuPef')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <small class="text-muted d-block mt-3">Actualiza los campos y presiona Guardar.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Cancelar
                        </button>

                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="editarPeriferico" @disabled($llave || $errors->any() || empty($codigoInventarioPef) || empty($idTpf) || empty($marcaPef))>
                            <span wire:loading.remove wire:target="editarPeriferico">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="editarPeriferico">
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

    <!-- Modal: Eliminar periférico -->
    <div wire:ignore.self class="modal fade" id="kt_modal_eliminar_periferico" tabindex="-1"
        aria-labelledby="modalLabelDeletePef" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="eliminarPeriferico" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelDeletePef">Eliminar periférico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar el periférico
                        <strong>{{ $codigoInventarioPef }}</strong>?
                    </p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <span wire:loading.remove wire:target="eliminarPeriferico">Eliminar</span>
                        <span wire:loading wire:target="eliminarPeriferico">
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

<!-- Utilidades de modales (mismo manejador que en mantenimiento) -->
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
<!-- ZXing solo se carga si el navegador no soporta BarcodeDetector -->
<!-- ZXing solo se carga si el navegador no soporta BarcodeDetector -->
<script>
    (() => {
        let stream = null;
        let usingBack = true;
        let detector = null;
        let detectLoopId = null;
        let alreadyHandled = false;

        const video = document.getElementById('scannerVideo');
        const canvas = document.getElementById('scannerCanvas');
        const status = document.getElementById('scannerStatus');
        const btnUse = document.getElementById('useManualBarcode');
        const txtMan = document.getElementById('manualBarcode');
        const btnFlip = document.getElementById('switchCamera');
        const supportedFormats = [
            'code_128', 'code_39', 'code_93', 'ean_13', 'ean_8',
            'upc_a', 'upc_e', 'itf', 'codabar', 'data_matrix', 'qr_code'
        ];

        // ------------- utilidades -------------
        function stopCamera() {
            if (detectLoopId) {
                cancelAnimationFrame(detectLoopId);
                detectLoopId = null;
            }
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
            if (video) {
                video.pause();
                video.srcObject = null;
            }
        }

        async function ensureDetector() {
            if (!('BarcodeDetector' in window)) {
                throw new Error('Este navegador no soporta BarcodeDetector.');
            }
            if (!detector) {
                try {
                    detector = new window.BarcodeDetector({
                        formats: supportedFormats
                    });
                } catch {
                    detector = new window.BarcodeDetector();
                }
            }
            return detector;
        }

        async function startCamera() {
            stopCamera();
            alreadyHandled = false;
            status.textContent = 'Abriendo cámara...';
            const constraints = {
                video: {
                    facingMode: usingBack ? {
                        ideal: 'environment'
                    } : 'user',
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                }
            };
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;
            await video.play();
            status.textContent = 'Apunta el código dentro del marco.';
            startDetectLoop();
        }

        // ------------- detección -------------
        function startDetectLoop() {
            const ctx = canvas.getContext('2d', {
                willReadFrequently: true
            });
            const step = async () => {
                try {
                    if (video.readyState >= 2 && !alreadyHandled) {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        const bitmap = await createImageBitmap(canvas);
                        const det = await ensureDetector();
                        const codes = await det.detect(bitmap);
                        if (codes.length) {
                            const raw = (codes[0].rawValue || '').trim();
                            if (raw && !alreadyHandled) {
                                alreadyHandled = true;
                                handleCode(raw);
                                return;
                            }
                        }
                    }
                } catch (e) {
                    console.error(e);
                    status.textContent = 'No pude leer. Mejora la luz o usa el campo manual.';
                }
                detectLoopId = requestAnimationFrame(step);
            };
            detectLoopId = requestAnimationFrame(step);
        }

        // ------------- cierre y Livewire -------------
        function forceCloseModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const instance = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            instance.hide();

            // fallback: limpiar si Bootstrap falla
            setTimeout(() => {
                el.classList.remove('show');
                document.querySelectorAll('.modal-backdrop.show').forEach(e => e.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            }, 300);
        }

        function handleCode(code) {
            stopCamera();
            forceCloseModal('scannerModal'); // cierre forzado inmediato
            Livewire.dispatch('scanner:code-detected', {
                code
            });

            if (typeof showToast === 'function') {
                showToast('success', 'Código detectado', `Leído: ${code}`);
            } else {
                console.log('✅ Código:', code);
            }
        }

        // ------------- eventos UI -------------
        btnUse?.addEventListener('click', () => {
            const code = (txtMan?.value || '').trim();
            if (!code) return;
            alreadyHandled = true;
            handleCode(code);
        });

        btnFlip?.addEventListener('click', async () => {
            usingBack = !usingBack;
            try {
                await startCamera();
            } catch {
                status.textContent = 'No pude cambiar cámara.';
            }
        });

        document.getElementById('scannerModal')?.addEventListener('shown.bs.modal', async () => {
            try {
                await ensureDetector();
                await startCamera();
            } catch (e) {
                console.error(e);
                status.innerHTML = 'Tu navegador no soporta escaneo. Usa el campo manual.';
            }
        });
        document.getElementById('scannerModal')?.addEventListener('hidden.bs.modal', stopCamera);
    })();
</script>
