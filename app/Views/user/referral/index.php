<?= $this->extend('layout/page_layout') ?>

<?= $this->section('head') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .dataTables_wrapper {
        color: var(--text-color);
    }

    .table {
        color: var(--text-color);
        border-color: var(--border-color);
    }

    .dataTables_info,
    .dataTables_length,
    .dataTables_filter {
        color: var(--text-muted) !important;
    }

    .page-link {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }

    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .dataTables_wrapper .form-control,
    .dataTables_wrapper .form-select {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }

    .dataTables_wrapper .form-control:focus,
    .dataTables_wrapper .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4 fade-in-up">
    <div class="row">
        <div class="col-12">
            <div class="content-card mb-4">
                <div class="card-body">
                    <h4 class="mb-4" style="color:var(--secondary-color)">Your Referral Link</h4>
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
            <div class="stat-card mb-4">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-label">Total Referrals</div>
                <div class="stat-number"><?= $total_referrals ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card mb-4">
                <i class="fas fa-coins stat-icon"></i>
                <div class="stat-label">Total Earned</div>
                <div class="stat-number"><?= number_format($total_earned, 3) ?><small>points</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card mb-4">
                <i class="fas fa-percentage stat-icon"></i>
                <div class="stat-label">Referral commission</div>
                <div class="stat-number">10<small>%</small></div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-body">
            <h4 class="mb-4" style="color: var(--secondary-color);">Your Referrals</h4>
            <?php if (empty($referrals)): ?>
                <div class="alert alert-info">
                    You haven't referred anyone yet. Share your referral link to start earning!
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="referralsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Registered</th>
                                <th>Last Active</th>
                                <th>Claims (30d)</th>
                                <th>You earned</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referrals as $referral): ?>
                                <tr>
                                    <td><?= esc($referral['username']) ?></td>
                                    <td data-sort="<?= strtotime($referral['created_at']) ?>">
                                        <?= date('Y-m-d', strtotime($referral['created_at'])) ?>
                                    </td>
                                    <td data-sort="<?= strtotime($referral['last_active']) ?>">
                                        <?= date('Y-m-d', strtotime($referral['last_active'])) ?>
                                    </td>
                                    <td><?= $referral['claims_30days'] ?></td>
                                    <td data-sort="<?= $referral['earnings'] ?>">
                                        <?= number_format($referral['earnings'], 3) ?> points
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

<!-- DataTables & SweetAlert, jquery Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('#referralsTable').DataTable({
            responsive: true,
            order: [[1, 'desc']], // Sort by registration date by default
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search referrals...",
                lengthMenu: "_MENU_ referrals per page",
                info: "Showing _START_ to _END_ of _TOTAL_ referrals",
                infoEmpty: "No referrals found",
                infoFiltered: "(filtered from _MAX_ total referrals)"
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
    });

    function copyReferralLink() {
        var copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Referral link copied to clipboard',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            background: getComputedStyle(document.documentElement).getPropertyValue('--card-bg'),
            color: getComputedStyle(document.documentElement).getPropertyValue('--text-color')
        });
    }
</script>

<?= $this->endSection() ?>