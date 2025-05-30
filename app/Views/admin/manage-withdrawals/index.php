<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="mb-4">Manage Withdrawals</h1>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4" style="background-color: var(--card-bg);">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="color:var(--secondary-color)">Withdrawal History</h4>
                    <?php if (empty($withdrawals)): ?>
                        <div class="alert alert-info">
                            No withdrawals found.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-info">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($withdrawals as $withdrawal): ?>
                                        <tr>
                                            <td><?= esc($withdrawal['username']) ?></td>
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
    </div>
</div>


<?= $this->endSection() ?>