<?= $this->extend("layout/page_layout") ?>

<?= $this->section("content") ?>
<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-person-badge me-2"></i>Admin Profile
            </h1>
            <p class="lead text-muted">Administrator account information</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-3 me-3">
                            <i class="bi bi-person-fill text-white fs-3"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-primary"><?= esc(
                                $user->username
                            ) ?></h2>
                            <p class="text-muted mb-0">Administrator Account</p>
                        </div>
                    </div>

                    <!-- User Details Section -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-gradient rounded-circle p-2 me-3">
                                <i class="bi bi-person-badge text-white"></i>
                            </div>
                            <h4 class="mb-0 fw-semibold text-primary">Account Details</h4>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="bg-success bg-gradient rounded-circle p-2 d-inline-flex mb-2">
                                            <i class="bi bi-envelope-fill text-white"></i>
                                        </div>
                                        <h6 class="text-muted mb-1">Email Address</h6>
                                        <p class="mb-0 fw-medium"><?= esc(
                                            $user->email
                                        ) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="bg-warning bg-gradient rounded-circle p-2 d-inline-flex mb-2">
                                            <i class="bi bi-calendar-event text-white"></i>
                                        </div>
                                        <h6 class="text-muted mb-1">Admin Since</h6>
                                        <p class="mb-0 fw-medium"><?= date(
                                            "F j, Y",
                                            strtotime($user->created_at)
                                        ) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-4 me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Administrator Privileges</h6>
                                <p class="mb-0">You have full access to all administrative functions and user management features.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
