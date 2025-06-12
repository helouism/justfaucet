<nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top border-bottom" style="z-index: 1030;">
    <div class="container-fluid">
        <?php if (auth()->loggedIn()): ?>
            <button class="btn btn-outline-secondary btn-sm me-3 d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        <?php endif; ?>

        <a class="navbar-brand fw-bold" href="<?= base_url("dashboard") ?>">
            <i class="fas fa-tint me-2 text-primary"></i>JustFaucet
        </a>

        <div class="ms-auto d-flex align-items-center">
            <div class="col-auto me-3 d-flex align-items-center">
                <i class="fa-solid fa-sun fa-sm me-2" style="color: #FFD43B;"></i>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                </div>
                <i class="fa-solid fa-moon fa-sm ms-2" style="color: #d7e4f9;"></i>
            </div>
            <?php if (auth()->loggedIn()): ?>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle d-flex align-items-center gap-2" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                        <span class="d-none d-md-inline"><?= auth()->user()
                            ->username ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-user me-2"></i><?= auth()->user()
                                    ->username ?>
                            </h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <?php $user = auth()->user(); ?>

                            <?php if ($user->inGroup("admin")): ?>
                                <a class="dropdown-item" href="<?= base_url(
                                    "admin/profile"
                                ) ?>">
                                <?php else: ?>
                                    <a class="dropdown-item" href="<?= base_url(
                                        "profile"
                                    ) ?>">
                                    <?php endif; ?>
                                    <i class="fas fa-user-edit me-2 text-primary"></i>Profile
                                </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= base_url(
                                "logout"
                            ) ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= base_url(
                    "login"
                ) ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-sign-in-alt me-1"></i>Login
                </a>
                <a href="<?= base_url("register") .
                    (isset($referred_by) ? "?ref={$referred_by}" : "") ?>" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i>Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>