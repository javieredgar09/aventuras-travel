<!-- LOGIN ADMINISTRADOR – FIEL AL MOCKUP stitch_prd -->
<div class="login-bg-admin">
    <div style="position:relative;z-index:2;text-align:center;width:100%;max-width:28rem;">
        <div class="login-logo" style="width:8rem;height:8rem;margin:0 auto var(--spacing-6);border-radius:var(--radius-xl);background:var(--surface-container-low);">
            <img src="<?= Router::url('/img/sin_fondo.png') ?>" alt="Aventuras Travel" style="width:100%;height:100%;object-fit:contain;padding:0.5rem;">
        </div>
        <h1 class="display-md" style="color:white;">Portal de Administración</h1>
        <p class="body-md" style="color:rgba(255,255,255,0.6);margin-bottom:var(--spacing-8);">Inicie sesión para gestionar el ecosistema de viajes.</p>

        <div class="login-card-glass" style="text-align:left;">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
            <?php endif; ?>

            <form action="<?= Router::url('/admin/login') ?>" method="POST" data-validate>
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label class="form-label">USUARIO / CORREO</label>
                    <div class="form-icon-group">
                        <span class="form-icon">👤</span>
                        <input type="text" name="codigo" class="form-control" placeholder="nombre.apellido@aventuras.com" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div class="flex justify-between items-center mb-2">
                        <label class="form-label mb-0">CONTRASEÑA</label>
                        <a href="#" class="body-sm" style="text-decoration:none;">¿Olvidó su clave?</a>
                    </div>
                    <div class="password-toggle">
                        <div class="form-icon-group">
                            <span class="form-icon">🔒</span>
                            <input type="password" name="password" id="adminPassword" class="form-control" placeholder="••••••••••••" required>
                        </div>
                        <button type="button" class="toggle-btn" onclick="togglePassword('adminPassword')">👁️</button>
                    </div>
                </div>

                <div class="form-check mb-6">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Recuérdame</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full" style="background:linear-gradient(135deg,#0A6E8A,#1B8AAD);">
                    Acceder al Sistema →
                </button>
            </form>

            <hr class="login-divider">
            <div class="security-badge">
                🔐 Conexión Encriptada AES-256
            </div>
        </div>
    </div>

    <div style="position:absolute;bottom:var(--spacing-6);width:100%;display:flex;justify-content:space-between;padding:0 var(--spacing-6);z-index:2;">
        <span style="color:rgba(255,255,255,0.4);font-size:0.8125rem;">❓ Soporte Técnico</span>
        <span style="color:rgba(255,255,255,0.3);font-size:0.75rem;font-family:monospace;">V2.4.0 HORIZON ENGINE</span>
    </div>
</div>
