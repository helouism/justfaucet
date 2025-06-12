<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>

<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </h1>
            <p class="lead text-muted">Track your progress and activity</p>
        </div>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-primary bg-gradient text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Points Balance</h6>
                        <h2 class="card-title mb-0 fw-bold"><?= esc(
                            number_format($balance, 2)
                        ) ?></h2>
                        <small class="opacity-75">Available to withdraw</small>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="bi bi-coin text-white fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-success bg-gradient text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Total Referrals</h6>
                        <h2 class="card-title mb-0 fw-bold"><?= $referralCount ?></h2>
                        <small class="opacity-75">Active invites</small>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="bi bi-people-fill text-white fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-info bg-gradient text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Current Level</h6>
                        <h2 class="card-title mb-0 fw-bold"><?= $user->level ?></h2>
                        <small class="opacity-75">User rank</small>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="bi bi-star-fill text-white fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-warning bg-gradient text-dark h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Experience</h6>
                        <h2 class="card-title mb-0 fw-bold"><?= $user->exp ?></h2>
                        <small class="opacity-75">Need <?= $expToNextLevel ?> EXP for level <?= $user->level +
     1 ?></small>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="bi bi-trophy-fill text-dark fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
