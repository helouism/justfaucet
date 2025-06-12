<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>
<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-person-plus-fill me-2"></i>Referral Program
            </h1>
            <p class="lead text-muted">Invite friends and earn 10% commission on their claims!</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-link-45deg text-white fs-5"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Your Referral Link</h4>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" value="<?= $referral_link ?>" id="referralLink"
                            readonly>
                        <button class="btn btn-primary btn-lg px-4" type="button" onclick="copyReferralLink()">
                            <i class="bi bi-clipboard me-2"></i>Copy
                        </button>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Share this link with friends to start earning commission
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="bg-info bg-gradient rounded-circle p-3 d-inline-flex mb-3">
                        <i class="bi bi-people-fill text-white fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1"><?= number_format(
                        $total_referrals,
                        0
                    ) ?></h3>
                    <p class="text-muted mb-0 fw-medium">Total Referrals</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-gradient rounded-circle p-3 d-inline-flex mb-3">
                        <i class="bi bi-coin text-white fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1"><?= number_format(
                        $total_earned,
                        2
                    ) ?> <small class="fs-6">points</small></h3>
                    <p class="text-muted mb-0 fw-medium">Total Earned</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="bg-warning bg-gradient rounded-circle p-3 d-inline-flex mb-3">
                        <i class="bi bi-percent text-white fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">10<small class="fs-6">%</small></h3>
                    <p class="text-muted mb-0 fw-medium">Commission Rate</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                    <i class="bi bi-list-ul text-white"></i>
                </div>
                <h4 class="mb-0 fw-semibold text-primary">Your Referrals</h4>
            </div>

            <?php if (empty($referrals)): ?>
                <div class="alert alert-info border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle fs-4 me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">No referrals yet!</h6>
                            <p class="mb-0">Share your referral link to start earning commission on your friends' claims.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="referralsTable" class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">
                                    <i class="bi bi-person me-1"></i>Username
                                </th>
                                <th class="fw-semibold">
                                    <i class="bi bi-calendar-plus me-1"></i>Registered
                                </th>
                                <th class="fw-semibold">
                                    <i class="bi bi-clock me-1"></i>Last Active
                                </th>
                                <th class="fw-semibold">
                                    <i class="bi bi-graph-up me-1"></i>Claims (30d)
                                </th>
                                <th class="fw-semibold">
                                    <i class="bi bi-coin me-1"></i>You Earned
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referrals as $referral): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary bg-gradient rounded-circle p-2 me-2">
                                                <i class="bi bi-person-fill text-white small"></i>
                                            </div>
                                            <span class="fw-medium"><?= esc(
                                                $referral["username"]
                                            ) ?></span>
                                        </div>
                                    </td>
                                    <td data-sort="<?= strtotime(
                                        $referral["created_at"]
                                    ) ?>">
                                        <span class="badge bg-light text-dark">
                                            <?= date(
                                                "M d, Y",
                                                strtotime(
                                                    $referral["created_at"]
                                                )
                                            ) ?>
                                        </span>
                                    </td>
                                    <td data-sort="<?= strtotime(
                                        $referral["last_active"]
                                    ) ?>">
                                        <span class="badge bg-light text-dark">
                                            <?= date(
                                                "M d, Y",
                                                strtotime(
                                                    $referral["last_active"]
                                                )
                                            ) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= $referral[
                                                "claims_30days"
                                            ] ?> claims
                                        </span>
                                    </td>
                                    <td data-sort="<?= $referral[
                                        "earnings"
                                    ] ?>">
                                        <span class="badge bg-success">
                                            <?= number_format(
                                                $referral["earnings"],
                                                2
                                            ) ?> points
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
<?= $this->endSection() ?>
<?= $this->section("scripts") ?>


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
            timer: 3000
        });
    }
</script>

<?= $this->endSection() ?>
