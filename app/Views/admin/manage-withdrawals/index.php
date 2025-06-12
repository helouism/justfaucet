<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>
<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-cash-stack me-2"></i>Manage Withdrawals
            </h1>
            <p class="lead text-muted">Monitor and manage user withdrawal requests</p>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-table text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Withdrawal History</h4>
                    </div>
                    <?php if (empty($withdrawals)): ?>
                        <div class="alert alert-info border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">No withdrawals found!</h6>
                                    <p class="mb-0">No withdrawal requests have been made yet.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold">
                                            <i class="bi bi-person me-1"></i>Username
                                        </th>
                                        <th class="fw-semibold">
                                            <i class="bi bi-calendar me-1"></i>Date
                                        </th>
                                        <th class="fw-semibold">
                                            <i class="bi bi-coin me-1"></i>Amount
                                        </th>
                                        <th class="fw-semibold">
                                            <i class="bi bi-flag me-1"></i>Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (
                                        $withdrawals
                                        as $withdrawal
                                    ): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-secondary bg-gradient rounded-circle p-2 me-2">
                                                        <i class="bi bi-person-fill text-white small"></i>
                                                    </div>
                                                    <span class="fw-medium"><?= esc(
                                                        $withdrawal["username"]
                                                    ) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?= date(
                                                        "M d, Y H:i",
                                                        strtotime(
                                                            $withdrawal[
                                                                "created_at"
                                                            ]
                                                        )
                                                    ) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-success">
                                                    <?= number_format(
                                                        $withdrawal["amount"],
                                                        2
                                                    ) ?> points
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $status = strtolower(
                                                    $withdrawal["status"]
                                                );
                                                $badgeClass = "bg-secondary";
                                                if ($status === "completed") {
                                                    $badgeClass = "bg-success";
                                                } elseif (
                                                    $status === "pending"
                                                ) {
                                                    $badgeClass =
                                                        "bg-warning text-dark";
                                                } elseif (
                                                    $status === "rejected"
                                                ) {
                                                    $badgeClass = "bg-danger";
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <?= ucfirst(
                                                        $withdrawal["status"]
                                                    ) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
