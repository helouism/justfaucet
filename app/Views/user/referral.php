<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="main-content">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Your Referral Link</h4>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="<?= $referral_link ?>" id="referralLink"
                                readonly>
                            <button class="btn btn-primary" type="button" onclick="copyReferralLink()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Referrals</h5>
                        <h2 class="card-text"><?= $total_referrals ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Earned</h5>
                        <h2 class="card-text"><?= number_format($total_earned, 2) ?> points</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Bonus Rate</h5>
                        <h2 class="card-text">10%</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Your Referrals</h4>
                <?php if (empty($referrals)): ?>
                    <div class="alert alert-info">
                        You haven't referred anyone yet. Share your referral link to start earning!
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Joined Date</th>
                                    <th>Claims (Last 30 days)</th>
                                    <th>Earnings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($referrals as $referral): ?>
                                    <tr>
                                        <td><?= esc($referral['username']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($referral['created_at'])) ?></td>
                                        <td><?= $referral['claims_30days'] ?></td>
                                        <td><?= number_format($referral['earnings'], 2) ?> points</td>
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

<script>
    function copyReferralLink() {
        var copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        // Show toast notification
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Referral link copied to clipboard',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
</script>

<?= $this->endSection() ?>