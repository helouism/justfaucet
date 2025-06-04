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

        <!-- hCaptcha Container -->
        <div id="captcha-container" class="mb-3" style="display: none;">
            <div class="text-center mb-2">
                <label class="form-label">Complete the captcha to enable claim:</label>
            </div>
            <div class="d-flex justify-content-center">
                <div class="h-captcha" data-sitekey="<?= env('HCAPTCHA_SITE_KEY') ?>" data-callback="hcaptchaCallback"
                    data-expired-callback="hcaptchaExpiredCallback"></div>
            </div>
        </div>

        <button type="button" class="btn btn-primary btn-lg" id="claimButton" disabled>
            <span id="button-text">Solve Captcha First</span>
            <span id="button-spinner" class="spinner-border spinner-border-sm ms-2" role="status"
                style="display: none;">
                <span class="visually-hidden">Loading...</span>
            </span>
        </button>


    </div>
</div>



<!-- jQuery -->
<script src="<?= base_url("assets/jquery/jquery.min.js") ?>"></script>

<!-- Hcaptcha & sweetalert2  -->
<script src="https://js.hcaptcha.com/1/api.js?hl=en" async defer></script>


<script>
    $(document).ready(function () {
        let countdown;
        let captchaCompleted = false;
        let canClaimNow = false;

        function updateButtonState() {
            const $btn = $('#claimButton');
            const $btnText = $('#button-text');

            if (!canClaimNow) {
                $btn.prop('disabled', true);
                $btnText.text('Wait for Timer');
            } else if (!captchaCompleted) {
                $btn.prop('disabled', true);
                $btnText.text('Solve Captcha First');
            } else {
                $btn.prop('disabled', false);
                $btnText.text('Claim');
            }
        }

        function showCaptcha() {
            $('#captcha-container').slideDown();
            updateButtonState();
        }

        function hideCaptcha() {
            $('#captcha-container').slideUp();
            captchaCompleted = false;
            hcaptcha.reset();
            updateButtonState();
        }

        function showErrorAlert(message) {
            // Hide timer and captcha
            $('#timer').hide();
            hideCaptcha();

            // Show bootstrap alert
            const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="vpn-error-alert">
                <strong>Access Denied!</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

            // Remove existing alert if present
            $('#vpn-error-alert').remove();

            // Add alert before the welcome section
            $('.welcome-section').prepend(alertHtml);

            // Update button state
            canClaimNow = false;
            updateButtonState();
        }

        function hideErrorAlert() {
            $('#vpn-error-alert').remove();
            $('#timer').show();
        }

        function updateTimer(nextClaimTime) {
            clearInterval(countdown);
            canClaimNow = false;
            hideCaptcha();
            hideErrorAlert(); // Hide any existing error alerts

            function updateDisplay() {
                const now = Math.floor(Date.now() / 1000);
                const timeLeft = nextClaimTime - now;

                if (timeLeft <= 0) {
                    $('#timer').text('Ready to claim!');
                    canClaimNow = true;
                    showCaptcha();
                    clearInterval(countdown);
                    return true;
                } else {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    $('#timer').text(`Next claim in: ${minutes}:${seconds.toString().padStart(2, '0')}`);
                    canClaimNow = false;
                    updateButtonState();
                    return false;
                }
            }

            if (!updateDisplay()) {
                countdown = setInterval(updateDisplay, 1000);
            }
        }

        function checkClaimStatus() {
            $.get('<?= site_url('claim/status') ?>', function (response) {
                // Check if there's an error in the response
                if (response.error) {
                    showErrorAlert(response.error);
                    return;
                }

                if (response.canClaim) {
                    $('#timer').text('Ready to claim!');
                    canClaimNow = true;
                    showCaptcha();
                    hideErrorAlert();
                } else if (response.nextClaimTime) {
                    updateTimer(response.nextClaimTime);
                } else {
                    // Handle case where nextClaimTime is not provided
                    $('#timer').text('Unable to determine next claim time');
                    canClaimNow = false;
                    updateButtonState();
                }
            }).fail(function () {
                $('#timer').text('Error loading claim status');
                showErrorAlert('Unable to connect to server. Please refresh the page.');
            });
        }

        // hCaptcha callback functions
        window.hcaptchaCallback = function () {
            captchaCompleted = true;
            updateButtonState();
        };

        window.hcaptchaExpiredCallback = function () {
            captchaCompleted = false;
            updateButtonState();
        };

        $('#claimButton').click(function () {
            if (!captchaCompleted || !canClaimNow) {
                return;
            }

            const $btn = $(this);
            const $btnText = $('#button-text');
            const $spinner = $('#button-spinner');

            $btn.prop('disabled', true);
            $btnText.text('Processing...');
            $spinner.show();

            const hcaptchaResponse = hcaptcha.getResponse();

            $.ajax({
                url: '<?= site_url('claim/action') ?>',
                method: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                    'h-captcha-response': hcaptchaResponse
                },
                success: function (response) {
                    $spinner.hide();

                    if (response.success) {
                        captchaCompleted = false;
                        canClaimNow = false;
                        hideCaptcha();

                        updateTimer(response.nextClaimTime);

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            html: `
                        <div>${response.success}</div>
                        <div class="mt-2">
                            <strong>New Balance:</strong> ${response.newBalance} points<br>
                            <strong>Level:</strong> ${response.level} (${response.exp}/${response.nextLevelExp} XP)
                        </div>
                    `,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });

                        if (response.levelUp) {
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: '🎉 Level Up!',
                                    text: `Congratulations! You reached level ${response.newLevel}!`,
                                    confirmButtonText: 'Awesome!'
                                });
                            }, 1000);
                        }
                    } else {
                        captchaCompleted = false;
                        hcaptcha.reset();
                        updateButtonState();

                        // Show error as alert instead of SweetAlert for VPN/Proxy errors
                        if (response.error && (response.error.includes('VPN') || response.error.includes('Proxy') || response.error.includes('flagged'))) {
                            showErrorAlert(response.error);
                        } else {
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
                    }
                },
                error: function (xhr) {
                    $spinner.hide();
                    captchaCompleted = false;
                    hcaptcha.reset();
                    updateButtonState();

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