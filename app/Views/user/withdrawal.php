<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>

<!-- Display users withdrawals history in table, display it nicely with bootstrap 5 styling -->
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
                        <form action="<?= site_url('/withdrawal/request') ?>" method="post">
                            <div class="mb-3">
                                <label for="amount" class="form-label" style="color: var(--text-color);">Amount
                                    (points)</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="2000" required>
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



<?= $this->endSection() ?>