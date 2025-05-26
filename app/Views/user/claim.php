<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="content-card fade-in-up">
    <div class="welcome-section">
        <h1 class="welcome-title">Claim Points Every 5 Minutes</h1>


        <div id="timer" class="mb-3 fs-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-lg" id="claimButton" disabled>
            Claim Points
        </button>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function () {
        let countdown;



        function updateTimer(nextClaimTime) {
            clearInterval(countdown);

            function updateDisplay() {
                const now = Math.floor(Date.now() / 1000);
                const timeLeft = nextClaimTime - now;

                if (timeLeft <= 0) {
                    $('#timer').text('Ready to claim!');
                    $('#claimButton').prop('disabled', false);
                    clearInterval(countdown);
                    return true;
                } else {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    $('#timer').text(`Next claim in: ${minutes}:${seconds.toString().padStart(2, '0')}`);
                    $('#claimButton').prop('disabled', true);
                    return false;
                }
            }

            if (!updateDisplay()) { // If not ready to claim
                countdown = setInterval(updateDisplay, 1000);
            }
        }

        function checkClaimStatus() {
            $.get('<?= site_url('claim/getNextClaimTime') ?>', function (response) {

                if (response.canClaim) {
                    $('#timer').text('Ready to claim!');
                    $('#claimButton').prop('disabled', false);
                } else {
                    updateTimer(response.nextClaimTime);
                }
            });
        }

        $('#claimButton').click(function () {
            const $btn = $(this);
            $btn.prop('disabled', true);


            $.ajax({
                url: '<?= site_url('claim/action') ?>',
                method: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function (response) {
                    if (response.success) {
                        updateTimer(response.nextClaimTime);

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.success,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        $btn.prop('disabled', false).text('Claim Points');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.error,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).text('Claim Points');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while processing your claim.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });

        // Check claim status on page load
        checkClaimStatus();
    });
</script>

<?= $this->endSection() ?>