<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="content-card fade-in-up">
    <div class="welcome-section">
        <h1 class="welcome-title">Admin Dashboard</h1>

    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-number"><?= number_format($total_users, 0) ?></div>
            <div class="stat-label">Users</div>
        </div>



        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-money-bill"></i>
            </div>
            <div class="stat-number"><?= number_format($total_withdrawals, 0) ?></div>
            <div class="stat-label">Withdrawals</div>
        </div>


    </div>
</div>
<?= $this->endSection() ?>