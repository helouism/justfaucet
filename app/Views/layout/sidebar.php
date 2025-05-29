<div class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <?php $user = auth()->user(); ?>
        <?php if ($user->inGroup('admin')): ?>
            <li><a href="<?= base_url('/admin') ?>" class="<?= $title === 'Admin Dashboard' ? 'active' : '' ?>"><i
                        class="fas fa-tachometer-alt"></i>Admin Dashboard</a></li>

            <li><a href="<?= base_url('/admin/manage-users') ?>" class="<?= $title === 'Manage Users' ? 'active' : '' ?>"><i
                        class="fas fa-users"></i>Manage
                    Users</a></li>

            <li> <a href="<?= base_url('/admin/manage-withdrawals') ?>"
                    class="<?= $title === 'Manage Withdrawals' ? 'active' : '' ?>"><i
                        class="fa-solid fa-money-bill-transfer"></i>Manage
                    Withdrawals</a></li>

        <?php endif; ?>
        <?php if ($user->inGroup('user')): ?>

            <li><a href="<?= base_url('/dashboard') ?>" class="<?= $title === 'Dashboard' ? 'active' : '' ?>"> <i
                        class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="<?= base_url('/claim') ?>" class="<?= $title === 'Claim' ? 'active' : '' ?>"><i
                        class="fas fa-file-alt"></i>Claim</a></li>
            <li><a href="<?= base_url('/challenge') ?>" class="<?= $title === 'Challenge' ? 'active' : '' ?>"><i
                        class="fa-solid fa-fire"></i>Challenge</a>
            </li>
            <li><a href="<?= base_url('/referral') ?>" class="<?= $title === 'Referral' ? 'active' : '' ?>"><i
                        class="fas fa-users"></i>Referral</a></li>
            <li><a href="<?= base_url('/withdrawal') ?>" class="<?= $title === 'Withdrawals' ? 'active' : '' ?>"><i
                        class="fas fa-money-bill-wave"></i>Withdraw</a></li>
        <?php endif; ?>
    </ul>
</div>