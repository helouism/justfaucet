<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>

<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-cash-coin me-2"></i>Withdrawal
            </h1>
            <p class="lead text-muted">Manage your withdrawals and request payouts</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-info bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-clock-history text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Your Withdrawal History</h4>
                    </div>

                    <?php if (empty($withdrawals)): ?>
                        <div class="alert alert-info border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">No withdrawals yet!</h6>
                                    <p class="mb-0">Your withdrawal history will appear here once you make your first request.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
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

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-arrow-up-circle text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Request Withdrawal</h4>
                    </div>

                    <?php if ($canWithdraw): ?>
                        <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">You're eligible!</h6>
                                    <p class="mb-0">You can request a withdrawal between 2,000 and 100,000 points.</p>
                                </div>
                            </div>
                        </div>

                        <form action="<?= site_url(
                            "/withdrawal/send"
                        ) ?>" method="post">
                            <div class="mb-4">
                                <label for="amount" class="form-label fw-medium">
                                    <i class="bi bi-coin me-1"></i>Amount (points)
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-currency-exchange"></i>
                                    </span>
                                    <input type="number" class="form-control" id="amount" name="amount"
                                           min="2000" max="100000" required placeholder="Enter amount">
                                    <span class="input-group-text bg-light text-muted">points</span>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Minimum: 2,000 points | Maximum: 100,000 points
                                    </small>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg py-3 fw-semibold">
                                    <i class="bi bi-send me-2"></i>Request Withdrawal
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Insufficient Balance</h6>
                                    <p class="mb-0">You need at least 2,000 points to request a withdrawal. Keep claiming to reach the minimum!</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary btn-lg py-3" disabled>
                                <i class="bi bi-lock me-2"></i>Withdrawal Unavailable
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section("scripts") ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $(document).ready(function () {
        // Handle withdrawal request form submission
        $('form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            var amount = $('#amount').val();
            if (amount < 2000 || amount > 100000) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: `
                            <div>Invalid amount</div>
                            <div class="mt-2">
                                <strong>Amount must be between 2000 and 100000 points</strong>
                            </div>
                        `,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000
                });
                return;
            }

            // Submit the form via AJAX
            $.ajax({
                url: '<?= site_url("withdrawal/send") ?>', // Fixed URL
                type: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Withdraw Successful!',
                            html: `
                            <div>${response.success}</div>
                            <div class="mt-2">
                                <strong>${response.message}</strong>
                            </div>
                        `,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        location.reload(); // Reload the page to show updated withdrawal history
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: `
                            <div>${response.error}</div>
                            <div class="mt-2">
                                <strong>${response.message}</strong>
                            </div>
                        `,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while processing your request. Please try again later.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }
            });
        });
    });
</script>



<?= $this->endSection() ?>
