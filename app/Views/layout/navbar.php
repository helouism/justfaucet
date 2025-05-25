<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <a class="navbar-brand" href="dashboard">
            <i class="fas fa-rocket me-2"></i>
            FancyApp
        </a>

        <?php if (auth()->loggedIn()): ?>
            <div class="profile-dropdown dropdown">
                <button class="dropdown-toggle" data-bs-toggle="dropdown">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="d-none d-md-inline"><?= auth()->user()->username ?></span>
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <div class="ms-auto">
                <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                <a href="<?= url_to('register') . (isset($referred_by) ? "?ref={$referred_by}" : '') ?>"
                    class="btn btn-primary">Register</a>
            </div>
        <?php endif; ?>
    </div>
</nav>