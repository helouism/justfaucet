<div class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <?php $user = auth()->user(); ?>
        <?php if ($user->inGroup('admin')): ?>

            <li><a href="<?= base_url('/admin/manage-users') ?>" data-page="manage-users"><i class="fas fa-users"></i>Manage
                    Users</a></li>
            <li a href="<?= base_url('/admin/manage-withdrawals') ?>" data-page="manage-withdrawals"><i
                    class="fa-solid fa-money-bill-transfer"></i>Manage
                Withdrawals</a></li>

        <?php endif; ?>
        <?php if ($user->inGroup('user')): ?>
            <li><a href="<?= base_url('/profile') ?>" data-page="profile"><i class="fas fa-user"></i>Profile</a></li>
            <li><a href="<?= base_url('/dashboard') ?>" data-page="dashboard"><i
                        class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="<?= base_url('/claim') ?>" data-page="claim"><i class="fas fa-file-alt"></i>Claim</a></li>
            <li><a href="<?= base_url('/challenge') ?>" data-page="challenge"><i class="fa-solid fa-fire"></i>Challenge</a>
            </li>
            <li><a href="<?= base_url('/referral') ?>" data-page="referral"><i class="fas fa-users"></i>Referral</a></li>
            <li><a href="<?= base_url('/withdrawal') ?>" data-page="withdraw"><i
                        class="fas fa-money-bill-wave"></i>Withdraw</a></li>
        <?php endif; ?>
    </ul>
</div>