<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>

<!-- Display flash messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <div><?= esc($error) ?></div>
        <?php endforeach; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Withdrawal Information Card -->
            <div class="card mb-4" style="background-color: var(--card-bg);">
                <div class="card-body">
                    <h4 class="card-title mb-3" style="color:var(--secondary-color)">
                        <i class="fas fa-info-circle me-2"></i>Withdrawal Information
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-2">
                                <strong>Minimum Withdrawal:</strong> 2,000 points (0.2 USDT)
                            </div>
                            <div class="info-item mb-2">
                                <strong>Conversion Rate:</strong> 10000 points = 1 USDT
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-2">
                                <strong>Payment Method:</strong> USDT with FaucetPay (for now)
                            </div>
                            <div class="info-item mb-2">
                                <strong>Processing:</strong> Instant (automated)
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Make sure you have a FaucetPay account with the same email address before requesting a
                        withdrawal.
                        <a href="https://faucetpay.io/?r=1961178" target="_blank" class="alert-link">Create FaucetPay
                            Account</a>
                    </div>
                </div>
            </div>

            <!-- Request Withdrawal Card -->
            <div class="card mb-4" style="background-color: var(--card-bg);">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="color:var(--secondary-color)">
                        <i class="fas fa-money-bill-wave me-2"></i>Request Withdrawal
                    </h4>
                    <?php if ($canWithdraw): ?>
                        <form action="<?= site_url('/withdrawal/request') ?>" method="post" id="withdrawalForm">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label" style="color: var(--text-color);">
                                            Amount (points) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="amount" name="amount" min="2000"
                                            step="100" placeholder="Minimum 2000 points" required>
                                        <div class="form-text">
                                            <span id="usdtEquivalent">0.00 USDT</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: var(--text-color);">
                                            FaucetPay Email
                                        </label>
                                        <input type="email" class="form-control" value="<?= esc(auth()->user()->email) ?>"
                                            readonly>
                                        <div class="form-text">
                                            Withdrawal will be sent to this email address on FaucetPay
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Request Withdrawal
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You need at least 2,000 points to request a withdrawal.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Withdrawal History Card -->
            <div class="card mb-4" style="background-color: var(--card-bg);">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="color:var(--secondary-color)">
                        <i class="fas fa-history me-2"></i>Your Withdrawal History
                    </h4>
                    <?php if (empty($withdrawals)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You have no withdrawal history yet.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount (Points)</th>
                                        <th>USDT Amount</th>
                                        <th>Status</th>
                                        <th>Reference</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($withdrawals as $withdrawal): ?>
                                        <tr>
                                            <td>
                                                <small><?= date('M d, Y H:i', strtotime($withdrawal['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <strong><?= number_format($withdrawal['amount'], 0) ?></strong>
                                            </td>
                                            <td>
                                                <?php if (!empty($withdrawal['usdt_amount'])): ?>
                                                    <span class="text-success">
                                                        <?= number_format($withdrawal['usdt_amount'], 4) ?> USDT
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = match ($withdrawal['status']) {
                                                    'completed' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger',
                                                    'cancelled' => 'secondary',
                                                    default => 'info'
                                                };
                                                $statusIcon = match ($withdrawal['status']) {
                                                    'completed' => 'fas fa-check-circle',
                                                    'pending' => 'fas fa-clock',
                                                    'failed' => 'fas fa-times-circle',
                                                    'cancelled' => 'fas fa-ban',
                                                    default => 'fas fa-question-circle'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>">
                                                    <i class="<?= $statusIcon ?> me-1"></i>
                                                    <?= ucfirst($withdrawal['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($withdrawal['faucetpay_reference'])): ?>
                                                    <small class="text-muted font-monospace">
                                                        <?= esc($withdrawal['faucetpay_reference']) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($withdrawal['status'] === 'pending'): ?>
                                                    <form action="<?= site_url('/withdrawal/cancel/' . $withdrawal['id']) ?>"
                                                        method="post" style="display: inline;"
                                                        onsubmit="return confirm('Are you sure you want to cancel this withdrawal?')">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-times me-1"></i>Cancel
                                                        </button>
                                                    </form>
                                                <?php elseif ($withdrawal['status'] === 'failed' && !empty($withdrawal['error_message'])): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="tooltip" title="<?= esc($withdrawal['error_message']) ?>">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Calculate USDT equivalent
        const amountInput = document.getElementById('amount');
        const usdtEquivalent = document.getElementById('usdtEquivalent');
        const submitBtn = document.getElementById('submitBtn');

        if (amountInput && usdtEquivalent) {
            amountInput.addEventListener('input', function () {
                const points = parseFloat(this.value) || 0;
                const usdt = points / 1000; // 1000 points = 1 USDT
                usdtEquivalent.textContent = usdt.toFixed(4) + ' USDT';

                // Update button text
                if (submitBtn) {
                    if (points >= 2000) {
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Request ' + usdt.toFixed(4) + ' USDT';
                        submitBtn.disabled = false;
                    } else {
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Request Withdrawal';
                        submitBtn.disabled = points > 0 && points < 2000;
                    }
                }
            });
        }

        // Form validation
        const withdrawalForm = document.getElementById('withdrawalForm');
        if (withdrawalForm) {
            withdrawalForm.addEventListener('submit', function (e) {
                const amount = parseFloat(amountInput.value);

                if (amount < 2000) {
                    e.preventDefault();
                    alert('Minimum withdrawal amount is 2,000 points');
                    return false;
                }

                // Disable submit button to prevent double submission
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                }
            });
        }

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
    .info-item {
        color: var(--text-color);
    }

    .table th {
        border-top: none;
        font-weight: 600;
    }

    .font-monospace {
        font-family: 'Courier New', monospace;
        font-size: 0.85em;
    }

    .alert-link {
        text-decoration: none;
        font-weight: 600;
    }

    .alert-link:hover {
        text-decoration: underline;
    }

    #withdrawalForm .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25);
    }
</style>

<?= $this->endSection() ?>