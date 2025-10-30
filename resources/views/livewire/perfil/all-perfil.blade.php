<div class="container py-3"><!-- ÚNICO ROOT -->

    <h1 class="h4 mb-3">Mi perfil</h1>

    <div class="row g-4">
        <!-- DATOS DE PERFIL -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <strong>Datos personales</strong>
                </div>
                <form class="card-body" wire:submit.prevent="guardarPerfil" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control @error('usernameUsa') is-invalid @enderror"
                                wire:model.live="usernameUsa">
                            @error('usernameUsa')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control @error('nombrePer') is-invalid @enderror"
                                wire:model.live="nombrePer">
                            @error('nombrePer')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control @error('apellidoPaternoPer') is-invalid @enderror"
                                wire:model.live="apellidoPaternoPer">
                            @error('apellidoPaternoPer')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control @error('apellidoMaternoPer') is-invalid @enderror"
                                wire:model.live="apellidoMaternoPer">
                            @error('apellidoMaternoPer')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control @error('correoPer') is-invalid @enderror"
                                wire:model.live="correoPer">
                            @error('correoPer')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">DNI</label>
                            <input type="text" inputmode="numeric" pattern="\d*"
                                class="form-control @error('dniPer') is-invalid @enderror" wire:model.live="dniPer">
                            @error('dniPer')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" inputmode="numeric" pattern="\d*"
                                class="form-control @error('telefonoPer') is-invalid @enderror"
                                wire:model.live="telefonoPer">
                            @error('telefonoPer')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control @error('fechaNacimientoPer') is-invalid @enderror"
                                wire:model.live="fechaNacimientoPer">
                            @error('fechaNacimientoPer')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button class="btn btn-success" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="guardarPerfil">Guardar cambios</span>
                                <span wire:loading wire:target="guardarPerfil">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Guardando...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- LADO DERECHO: LECTURA Y CONTRASEÑA -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><strong>Información de cuenta</strong></div>
                <div class="card-body">
                    <div class="mb-2"><span class="text-muted">Rol:</span> {{ $rolNombre }}</div>
                    <div>
                        <span class="text-muted d-block">Laboratorios asignados:</span>
                        @if (count($laboratorios))
                            <ul class="mb-0">
                                @foreach ($laboratorios as $lab)
                                    <li>{{ $lab }}</li>
                                @endforeach
                            </ul>
                        @else
                            <em class="text-muted">Sin laboratorios</em>
                        @endif
                    </div>
                    <small class="text-muted d-block mt-2">Estos valores los gestiona un administrador.</small>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Cambiar contraseña</strong></div>
                <form class="card-body" wire:submit.prevent="cambiarPassword" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Contraseña actual</label>
                        <input type="password" class="form-control @error('passwordActual') is-invalid @enderror"
                            wire:model.live="passwordActual">
                        @error('passwordActual')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control @error('passwordNueva') is-invalid @enderror"
                            wire:model.live="passwordNueva">
                        @error('passwordNueva')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" class="form-control @error('passwordConfirmacion') is-invalid @enderror"
                            wire:model.live="passwordConfirmacion">
                        @error('passwordConfirmacion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-warning w-100" type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="cambiarPassword">Actualizar contraseña</span>
                        <span wire:loading wire:target="cambiarPassword">
                            <span class="spinner-border spinner-border-sm me-2"></span>Actualizando...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
