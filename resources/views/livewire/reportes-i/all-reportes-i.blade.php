<div id="rep-incidencias-root" class="container-xxl py-3">
    <!-- Título -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Reportes de incidencias por periférico</h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Nota: sin align-items-end -->
            <form wire:submit.prevent="mostrarIncidencias" class="row g-3 align-items-start needs-validation" novalidate>
                <label for="codigo" class="form-label fw-semibold">Código</label>

                <!-- Columna 1: Código + botón escáner -->
                <div class="col-12 col-md-4">

                    <div class="input-group has-validation">
                        <input id="codigo" type="text" class="form-control @error('codigo') is-invalid @enderror"
                            placeholder="Ej. 12345 o 123456789012" wire:model.live="codigo"
                            aria-describedby="codigoFeedback" oninput="this.value = this.value.replace(/[^0-9]/g,'')">

                        <button id="btnScan" type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#scannerModal">
                            <i class="bi bi-upc-scan"></i>
                        </button>

                        <!-- Feedback estándar de Bootstrap -->
                        <div id="codigoFeedback" class="invalid-feedback">
                            @error('codigo')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Columna 2: Buscar -->
                <div class="col-12 col-md-4">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" @disabled($errors->has('codigo') || empty($codigo))>
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                    </div>
                </div>

                <!-- Columna 3: Limpiar -->
                <div class="col-12 col-md-4">
                    <div class="d-grid">
                        <button id="btnLimpiar" type="button" wire:click="limpiar" class="btn btn-outline-secondary">
                            <i class="bi bi-eraser me-1"></i> Limpiar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Filtros (solo dos fechas) -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form id="form-filtros" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="fDesde" class="form-label fw-semibold">Desde</label>
                    <input id="fDesde" type="date" class="form-control @error('fDesde') is-invalid @enderror"
                        wire:model.live="fDesde">
                    @error('fDesde')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label for="fHasta" class="form-label fw-semibold">Hasta</label>
                    <input id="fHasta" type="date" class="form-control @error('fHasta') is-invalid @enderror"
                        wire:model.live="fHasta">
                    @error('fHasta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6 d-flex gap-2">
                    <button id="btnAplicarFiltros" type="button" class="btn btn-outline-primary flex-fill"
                        wire:click="aplicarFiltros" @disabled(!$this->puedeAplicarFiltros)>
                        <i class="bi bi-funnel me-1"></i> Aplicar
                    </button>
                    <button id="btnQuitarFiltros" type="button" class="btn btn-outline-secondary flex-fill"
                        wire:click="quitarFiltros">
                        Quitar
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Resumen del periférico -->
    <div class="card shadow-sm mb-2">
        <div class="card-body d-flex flex-wrap gap-3 small">
            <div><span class="text-muted">Periférico:</span> <span
                    id="resNombre">{{ $periferico->tipoperiferico->NombreTpf ?? '-' }}</span></div>
            <div><span class="text-muted">Código inventario:</span> <span
                    id="resInventario">{{ $periferico->CodigoInventarioPef ?? '-' }}</span></div>
            <div><span class="text-muted">Código CIU:</span> <span
                    id="resCiu">{{ $periferico->CiuPef ?? '-' }}</span>
            </div>
            <div><span class="text-muted">Equipo:</span> <span
                    id="resEquipo">{{ $periferico->detalleequipo->equipo->NombreEqo ?? '—' }}</span></div>
            <div><span class="text-muted">Estado:</span> <span id="resEstado">
                    @if (isset($periferico))
                        {{ $periferico->EstadoPef ? 'Activo' : 'Inactivo' }}
                    @else
                        —
                    @endif
                </span></div>
        </div>
    </div>

    <!-- Tabla de incidencias -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Incidencias</span>
            <small id="resTotal" class="text-muted">0 resultados</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light position-sticky top-0">
                        <tr>
                            <th class="w-35">Incidencia</th>
                            <th class="w-15">Fecha</th>
                            <th class="w-15">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tablaIncidencias">
                        @forelse ($incidencias as $inc)
                            <tr>
                                <td>{{ $inc->incidencia->NombreInc }}</td>

                                {{-- Fecha: dd/mm/yyyy hh:mm (hora local Lima) --}}
                                <td>
                                    {{ \Carbon\Carbon::parse($inc->FechaIpf)->format('d/m/Y') }}
                                </td>

                                {{-- Estado: 1 => Dañado, 0 => Reparado, con badge bonito --}}
                                <td>
                                    <span class="badge bg-{{ $inc->EstadoIpf ? 'danger' : 'success' }}">
                                        {{ $inc->EstadoIpf ? 'Dañado' : 'Reparado' }}
                                    </span>
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
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group">
                <button id="btnImprimir" type="button" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
                <button id="btnExportar" type="button" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-filetype-csv me-1"></i> Exportar
                </button>
            </div>
            <nav id="paginacion" aria-label="Paginación">
                <!-- botones de paginación dinámicos -->
            </nav>
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


    <!-- Modal: Escáner de código de barras -->
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

                    <!-- Fallback manual -->
                    <div class="input-group mt-3">
                        <input id="manualBarcode" type="text" class="form-control"
                            placeholder="Ingresar código manualmente">
                        <button id="useManualBarcode" class="btn btn-outline-secondary" type="button">
                            Usar
                        </button>
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

<script>
    (() => {
        let stream = null;
        let usingBack = true;
        let detector = null;
        let detectLoopId = null;
        let alreadyHandled = false; // <— guardia anti-repetidos

        const video = document.getElementById('scannerVideo');
        const canvas = document.getElementById('scannerCanvas');
        const status = document.getElementById('scannerStatus');
        const btnUse = document.getElementById('useManualBarcode');
        const txtMan = document.getElementById('manualBarcode');
        const btnFlip = document.getElementById('switchCamera');

        const supportedFormats = [
            'code_128', 'code_39', 'code_93', 'ean_13', 'ean_8', 'upc_a', 'upc_e', 'itf', 'codabar',
            'data_matrix', 'qr_code'
        ];

        function setQueryValue(code) {
            const input = document.getElementById('query');
            if (input) {
                input.value = code;
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }
        }

        function showToast(variant, title, message) {
            // dispara un SOLO evento; tus manejadores deben existir una sola vez
            const event = new CustomEvent('modal-open', {
                detail: {
                    payload: {
                        variant,
                        title,
                        message,
                        autoclose: 2000
                    }
                }
            });
            window.dispatchEvent(event);
        }

        async function ensureDetector() {
            if (!('BarcodeDetector' in window)) {
                throw new Error(
                    'Este navegador no soporta BarcodeDetector. Usa el campo manual o un navegador compatible.'
                );
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
            alreadyHandled = false; // <— reset del guard
            status.textContent = 'Abriendo cámara...';
            const constraints = {
                audio: false,
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

        function startDetectLoop() {
            const ctx = canvas.getContext('2d', {
                willReadFrequently: true
            });
            const step = async () => {
                try {
                    if (video.readyState >= 2 && !alreadyHandled) {
                        canvas.width = video.videoWidth || canvas.clientWidth || 1280;
                        canvas.height = video.videoHeight || canvas.clientHeight || 720;
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                        const bitmap = await createImageBitmap(canvas);
                        const det = await ensureDetector();
                        const codes = await det.detect(bitmap);

                        if (codes && codes.length) {
                            const raw = (codes[0].rawValue || '').trim();
                            if (raw && !alreadyHandled) {
                                alreadyHandled = true; // <— corta repetidos inmediatamente
                                handleCode(raw);
                                return;
                            }
                        }
                    }
                } catch (err) {
                    console.error(err);
                    status.textContent =
                        'No pude leer nada. Acerca más, mejora la luz o usa el campo manual.';
                }
                detectLoopId = requestAnimationFrame(step);
            };
            detectLoopId = requestAnimationFrame(step);
        }

        function handleCode(code) {
            stopCamera();

            // Cierra el modal del escáner antes de cualquier otro
            const modalEl = document.getElementById('scannerModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.hide();

            // Pasa el valor al input y dispara Livewire
            setQueryValue(code);
            try {
                const root = document.querySelector('[wire\\:id]');
                const compId = root?.getAttribute('wire:id');
                if (compId && window.Livewire?.find) {
                    window.Livewire.find(compId).call('selectByBarcode', code);
                }
            } catch (e) {
                console.error('No pude invocar Livewire:', e);
            }

            // Luego el modal de confirmación
            showToast('success', 'Código detectado', `Leído: ${code}`);
        }
        window.handleCode = handleCode;

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
                status.textContent = 'No pude cambiar de cámara.';
            }
        });

        document.getElementById('scannerModal')?.addEventListener('shown.bs.modal', async () => {
            try {
                await ensureDetector();
                await startCamera();
            } catch (e) {
                console.error(e);
                status.innerHTML =
                    'Tu navegador no soporta escaneo nativo. Usa el campo manual o un navegador compatible.';
            }
        });
        document.getElementById('scannerModal')?.addEventListener('hidden.bs.modal', () => {
            stopCamera();
        });

        /* Evita registrar doble “showAppModal” si ya lo hiciste en otro script */
        if (!window.__appModalInit) {
            window.__appModalInit = true;
            // aquí puedes dejar tu único bloque que escucha 'modal-open'
            window.addEventListener('modal-open', (e) => {
                const d = e?.detail?.payload ?? e?.detail ?? {};
                // showAppModal(d);  // usa tu implementación ya existente
            });
        }
    })();
</script>
