<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <?php if (auth()->loggedIn()): ?>
            <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        <?php endif; ?>

        <a class="navbar-brand" href="dashboard">
            JustFaucet
        </a>

        <div class="ms-auto d-flex align-items-center">


            <?php if (auth()->loggedIn()): ?>
                <div class="dropdown">
                    <button class="profile-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="profile-name d-none d-md-inline"><?= auth()->user()->username ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                <a href="<?= base_url('register') . (isset($referred_by) ? "?ref={$referred_by}" : '') ?>"
                    class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>