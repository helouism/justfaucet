<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>


<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4" style="background-color: var(--card-bg);">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="color:var(--secondary-color)">Your Withdrawal History</h4>
                    <?php if (empty($withdrawals)): ?>
                        <div class="alert alert-info">
                            You have no withdrawal history yet.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-info">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($withdrawals as $withdrawal): ?>
                                        <tr>
                                            <td><?= date('Y-m-d H:i:s', strtotime($withdrawal['created_at'])) ?></td>
                                            <td><?= number_format($withdrawal['amount'], 2) ?> points</td>
                                            <td><?= ucfirst($withdrawal['status']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12">
            <!-- Request withdrawal form and button, enabled only if user points is at least 2000 -->
            <div class="card mb-4" style="background-color: var(--card-bg);">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="color:var(--secondary-color)">Request Withdrawal</h4>
                    <?php if ($canWithdraw): ?>
                        <form action="<?= site_url('/withdrawal/send') ?>" method="post">
                            <div class="mb-3">
                                <label for="amount" class="form-label" style="color: var(--text-color);">Amount
                                    (points)</label>
                                <input type="number" class="form-control" id="amount" name="amount" max="100000" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Request Withdrawal</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            You need at least 2000 points to request a withdrawal.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
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
                url: '<?= site_url('withdrawal/send') ?>', // Fixed URL
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