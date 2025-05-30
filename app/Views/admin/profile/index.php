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
                    <h2 class="welcome-title mb-0"><?= esc($user->username) ?>'s Profile</h2>
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
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>