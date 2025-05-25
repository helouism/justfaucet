<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="main-content">
    <div class="content-card fade-in-up">
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome to Dashboard</h1>
            <p class="text-muted fs-5">Manage your account and track your progress</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-number"><?= $balance ?></div>
                <div class="stat-label">Points</div>
            </div>

            <p><? $data ?></p>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-number"><?= $referralCount ?></div>
                <div class="stat-label">Referrals</div>
            </div>


        </div>
    </div>
</div>

<?= $this->endSection() ?>