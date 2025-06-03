<?= $this->extend('layout/page_layout') ?>



<?= $this->section('content') ?>
<div class="container py-4 fade-in-up">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="content-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="profile-avatar me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2 class="welcome-title mb-0"><?= esc($user->username) ?></h2>

                </div>

                <!-- User Details Section -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-id-card me-2 text-primary"></i>
                        <h4 class="mb-0">User Details</h4>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <i class="fas fa-envelope stat-icon"></i>
                                <div class="stat-label">Email</div>
                                <div class="stat-number" style="font-size: 1rem;">
                                    <?= esc($user->email) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card">
                                <i class="fas fa-calendar-alt stat-icon"></i>
                                <div class="stat-label">Registration Date</div>
                                <div class="stat-number" style="font-size: 1rem;">
                                    <?= date('F j, Y', strtotime($user->created_at)) ?>
                                </div>
                            </div>
                        </div>

                        <a class="btn btn-sm btn-outline-primary" href="<?= base_url('/profile/edit') ?>">
                            Change Password
                        </a>


                    </div>
                </div>

                <!-- Stats Section -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        <h4 class="mb-0">Stats</h4>
                    </div>
                    <hr>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-bitcoin stat-icon"></i>
                            <div class="stat-label">Balance</div>
                            <div class="stat-number">
                                <?= number_format($balance, 2) ?>
                                <small>Points</small>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-users stat-icon"></i>
                            <div class="stat-label">Total Referrals</div>
                            <div class="stat-number"><?= $totalReferrals ?></div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-star stat-icon"></i>
                            <div class="stat-label">Level</div>
                            <div class="stat-number"><?= $user->level ?></div>
                        </div>
                    </div>
                </div>

                <!-- Level Progress Section -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>
                        <h4 class="mb-0">Level Progress</h4>
                    </div>
                    <hr>
                    <div class="stat-card">
                        <div class="mb-2">
                            <span class="stat-label">Current Progress</span>
                            <span class="float-end text-primary">
                                <?= floor(($currentExp / $expRequired) * 100) ?>%
                            </span>
                        </div>
                        <div class="progress" style="height: 25px; background: var(--border-color);">
                            <?php
                            $progressPercentage = ($currentExp / $expRequired) * 100;
                            ?>
                            <div class="progress-bar" role="progressbar"
                                style="width: <?= $progressPercentage ?>%; background: var(--primary-color);"
                                aria-valuenow="<?= $progressPercentage ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= $currentExp ?>/<?= $expRequired ?> EXP
                            </div>
                        </div>
                        <div class="mt-2 text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <?= $expToNextLevel ?> EXP needed for next level
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>