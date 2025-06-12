<div class="offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header border-bottom d-lg-none">
        <h5 class="offcanvas-title" id="sidebarLabel">
            <i class="fas fa-bars me-2"></i>Menu
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="d-flex flex-column h-100">
            <div class="list-group list-group-flush">
                <?php $user = auth()->user(); ?>

                <?php if ($user->inGroup('admin')): ?>
                    <!-- Admin Section Header -->
                    <div class="list-group-item bg-primary text-white fw-bold py-2">
                        <i class="fas fa-shield-alt me-2"></i>Administration
                    </div>

                    <a href="<?= base_url('admin') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Admin Dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt text-primary"></i>
                        <span>Admin Dashboard</span>
                    </a>

                    <a href="<?= base_url('admin/manage-users') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Manage Users' ? 'active' : '' ?>">
                        <i class="fas fa-users text-success"></i>
                        <span>Manage Users</span>
                    </a>

                    <a href="<?= base_url('admin/manage-withdrawals') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Manage Withdrawals' ? 'active' : '' ?>">
                        <i class="fa-solid fa-money-bill-transfer text-warning"></i>
                        <span>Manage Withdrawals</span>
                    </a>

                    <!-- Divider between admin and user sections -->
                    <div class="list-group-item p-0">
                        <hr class="my-2">
                    </div>
                <?php endif; ?>

                <?php if ($user->inGroup('user')): ?>
                    <!-- User Section Header (only show if not admin to avoid duplication) -->
                    <?php if (!$user->inGroup('admin')): ?>
                        <div class="list-group-item bg-info text-white fw-bold py-2">
                            <i class="fas fa-user me-2"></i>User Menu
                        </div>
                    <?php endif; ?>

                    <a href="<?= base_url('dashboard') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt text-primary"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="<?= base_url('claim') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Claim' ? 'active' : '' ?>">
                        <i class="fas fa-gift text-success"></i>
                        <span>Claim</span>
                    </a>

                    <a href="<?= base_url('challenge') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Challenges' ? 'active' : '' ?>">
                        <i class="fa-solid fa-fire text-danger"></i>
                        <span>Challenge</span>
                    </a>

                    <a href="<?= base_url('referral') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Referral' ? 'active' : '' ?>">
                        <i class="fas fa-users text-info"></i>
                        <span>Referral</span>
                    </a>

                    <a href="<?= base_url('withdrawal') ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 <?= $title === 'Withdrawals' ? 'active' : '' ?>">
                        <i class="fas fa-money-bill-wave text-warning"></i>
                        <span>Withdraw</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>