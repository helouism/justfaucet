<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h1 class="mb-4">Challenges</h1>

    <div class="row g-4">
        <?php foreach ($challenges as $challenge): ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($challenge['title']) ?></h5>
                        <p class="card-text"><?= esc($challenge['description']) ?></p>

                        <div class="progress mb-3">
                            <?php
                            $percentage = min(($challenge['progress'] / $challenge['target']) * 100, 100);
                            ?>
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%"
                                aria-valuenow="<?= $challenge['progress'] ?>" aria-valuemin="0"
                                aria-valuemax="<?= $challenge['target'] ?>">
                                <?= $challenge['progress'] ?>/<?= $challenge['target'] ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-success">Reward: <?= $challenge['reward'] ?> points</span>
                            <button class="btn btn-primary claim-btn" data-challenge-id="<?= $challenge['id'] ?>"
                                <?= $challenge['progress'] >= $challenge['target'] ? '' : 'disabled' ?>>
                                Claim Reward
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script {csp-script-nonce}>
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
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while claiming the reward.');
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>