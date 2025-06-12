<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>

<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-gear-fill me-2"></i>Admin Dashboard
            </h1>
            <p class="lead text-muted">Monitor platform statistics and user activity</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-primary bg-gradient text-white h-100">
                <div class="card-body p-4 text-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-inline-flex mb-3">
                        <i class="fas fa-user-friends text-white fs-1"></i>
                    </div>
                    <h2 class="card-title mb-1 fw-bold"><?= number_format(
                        $total_users,
                        0
                    ) ?></h2>
                    <h6 class="card-subtitle opacity-75 mb-0">Total Users</h6>
                    <small class="opacity-75">Registered members</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-success bg-gradient text-white h-100">
                <div class="card-body p-4 text-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-inline-flex mb-3">
                        <i class="fa-solid fa-money-bill text-white fs-1"></i>
                    </div>
                    <h2 class="card-title mb-1 fw-bold"><?= number_format(
                        $total_withdrawals,
                        0
                    ) ?></h2>
                    <h6 class="card-subtitle opacity-75 mb-0">Total Withdrawals</h6>
                    <small class="opacity-75">Processed requests</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
