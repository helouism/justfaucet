<?= $this->extend("layout/page_layout") ?>

<?= $this->section("content") ?>
<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-person-circle me-2"></i>User Profile
            </h1>
            <p class="lead ">Manage your account and view your progress</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border border-primary-subtle shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-3 me-3">
                            <i class="bi bi-person-fill text-white fs-3"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-primary"><?= esc(
                                $user->username
                            ) ?></h2>
                            <p class=" mb-0">Level <?= $user->level ?> Member</p>
                        </div>
                    </div>

                    <!-- User Details Section -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-gradient rounded-circle p-2 me-3">
                                <i class="bi bi-person-badge text-white"></i>
                            </div>
                            <h4 class="mb-0 fw-semibold text-primary">User Details</h4>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border border-primary-subtle shadow-sm h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="bg-success bg-gradient rounded-circle p-2 d-inline-flex mb-2">
                                            <i class="bi bi-envelope-fill text-white"></i>
                                        </div>
                                        <h6 class=" mb-1">Email Address</h6>
                                        <p class="mb-0 fw-medium"><?= esc(
                                            $user->email
                                        ) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border border-primary-subtle shadow-sm h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="bg-warning bg-gradient rounded-circle p-2 d-inline-flex mb-2">
                                            <i class="bi bi-calendar-event text-white"></i>
                                        </div>
                                        <h6 class=" mb-1">Member Since</h6>
                                        <p class="mb-0 fw-medium"><?= date(
                                            "F j, Y",
                                            strtotime($user->created_at)
                                        ) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a class="btn btn-outline-primary" href="<?= base_url(
                                "/profile/edit"
                            ) ?>">
                                <i class="bi bi-key me-1"></i>Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="card border border-primary-subtle shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-graph-up text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Account Statistics</h4>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card border border-primary-subtle h-100">
                                <div class="card-body text-center p-3">
                                    <div class="bg-primary bg-gradient rounded-circle p-3 d-inline-flex mb-2">
                                        <i class="bi bi-coin text-white fs-4"></i>
                                    </div>
                                    <h3 class="fw-bold mb-1"><?= number_format(
                                        $balance,
                                        2
                                    ) ?></h3>
                                    <p class=" mb-0 fw-medium">Points Balance</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border border-primary-subtle  h-100">
                                <div class="card-body text-center p-3">
                                    <div class="bg-info bg-gradient rounded-circle p-3 d-inline-flex mb-2">
                                        <i class="bi bi-people-fill text-white fs-4"></i>
                                    </div>
                                    <h3 class="fw-bold  mb-1"><?= $totalReferrals ?></h3>
                                    <p class=" mb-0 fw-medium">Total Referrals</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border border-primary-subtle  h-100">
                                <div class="card-body text-center p-3">
                                    <div class="bg-warning bg-gradient rounded-circle p-3 d-inline-flex mb-2">
                                        <i class="bi bi-star-fill text-white fs-4"></i>
                                    </div>
                                    <h3 class="fw-bold  mb-1"><?= $user->level ?></h3>
                                    <p class=" mb-0 fw-medium">Current Level</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Level Progress Section -->
            <div class="card border border-primary-subtle shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-warning bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-trophy text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Level Progress</h4>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Experience Progress</span>
                            <span class="badge bg-primary">
                                <?= floor(
                                    ($currentExp / $expRequired) * 100
                                ) ?>% Complete
                            </span>
                        </div>
                        <?php $progressPercentage =
                            ($currentExp / $expRequired) * 100; ?>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated"
                                 role="progressbar"
                                 style="width: <?= $progressPercentage ?>%"
                                 aria-valuenow="<?= $progressPercentage ?>"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                                <?= $currentExp ?>/<?= $expRequired ?> EXP
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="">
                                <i class="bi bi-info-circle me-1"></i>
                                <?= $expToNextLevel ?> EXP needed to reach level <?= $user->level +
     1 ?>
                            </small>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>
