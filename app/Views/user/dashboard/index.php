<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>

<div class="content-card fade-in-up">
    <div class="welcome-section">
        <h1 class="welcome-title">Welcome to Dashboard</h1>
        <p class="fs-5" style="color: var(--text-color);">Track your progress</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number"><?= esc(number_format($balance, 2)) ?></div>
            <div class="stat-label">Points</div>
        </div>



        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-number"><?= $referralCount ?></div>
            <div class="stat-label">Referrals</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-turn-up"></i>
            </div>
            <div class="stat-number"><?= $user->level ?></div>
            <div class="stat-label">Level</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                EXP
            </div>
            <div class="stat-number"><?= $user->exp ?></div>
            <div class="stat-label">Get <?= $expToNextLevel ?> EXP To reach the next level</div>
        </div>


    </div>
</div>
<?= $this->endSection() ?>