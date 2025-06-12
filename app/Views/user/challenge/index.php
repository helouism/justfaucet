<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>

<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-trophy-fill me-2"></i>Daily Challenges
            </h1>
            <p class="lead text-muted">Complete challenges to earn bonus points!</p>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($challenges as $challenge): ?>
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                                <i class="bi bi-star-fill text-white"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-semibold"><?= esc(
                                $challenge["title"]
                            ) ?></h5>
                        </div>

                        <p class="card-text text-muted mb-3"><?= esc(
                            $challenge["description"]
                        ) ?></p>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted fw-medium">Progress</small>
                                <small class="text-muted">
                                    <?= $challenge[
                                        "progress"
                                    ] ?>/<?= $challenge["target"] ?>
                                </small>
                            </div>
                            <?php
                            $percentage = min(
                                ($challenge["progress"] /
                                    $challenge["target"]) *
                                    100,
                                100
                            );
                            $isCompleted =
                                $challenge["progress"] >= $challenge["target"];
                            ?>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar <?= $isCompleted
                                    ? "bg-success"
                                    : "bg-primary" ?> progress-bar-striped <?= !$isCompleted
     ? "progress-bar-animated"
     : "" ?>"
                                     role="progressbar"
                                     style="width: <?= $percentage ?>%"
                                     aria-valuenow="<?= $challenge[
                                         "progress"
                                     ] ?>"
                                     aria-valuemin="0"
                                     aria-valuemax="<?= $challenge[
                                         "target"
                                     ] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-coin text-warning me-1"></i>
                                <span class="badge bg-warning text-dark fw-semibold">
                                    <?= $challenge["reward"] ?> points
                                </span>
                            </div>
                            <?php if ($isCompleted): ?>
                                <button class="btn btn-success btn-sm claim-btn fw-semibold" data-challenge-id="<?= $challenge[
                                    "id"
                                ] ?>">
                                    <i class="bi bi-gift-fill me-1"></i>Claim Reward
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    <i class="bi bi-hourglass-split me-1"></i>In Progress
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($isCompleted): ?>
                            <div class="mt-2">
                                <div class="alert alert-success border-0 py-2 px-3 mb-0" role="alert">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    <small class="fw-medium">Challenge completed! Ready to claim.</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section("scripts") ?>
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const claimButtons = document.querySelectorAll('.claim-btn');

        claimButtons.forEach(button => {
            button.addEventListener('click', async function () {
                const challengeId = this.dataset.challengeId;

                try {
                    const response = await fetch(`/challenge/claim/${challengeId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Use SweetAlert2 for better UX
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error!',
                        text: 'An error occurred while claiming the reward.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>
