<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>



<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-10">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-5">
                <h1 class="display-6 text-center mb-4 text-primary fw-bold">
                    <i class="bi bi-gift-fill me-2"></i>Claim Points Every 5 Minutes
                </h1>

                <div id="timer" class="mb-4 fs-3 text-center fw-semibold">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="spinner-border text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="text-muted">Loading timer...</span>
                    </div>
                </div>

                <!-- CAPTCHA Container -->
                <div id="captcha-container" class="mb-4" style="display: none;">
                    <div class="alert alert-info border-0 shadow-sm mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-4 me-2"></i>
                            <div>
                                <strong>Solve the CAPTCHA below to claim</strong>

                            </div>
                        </div>
                    </div>
                    <div class="card bg-light border-1 p-3 mb-3">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="flex-shrink-0">
                                <img id="captcha-image" alt="CAPTCHA"
                                    class="img-fluid" style="max-width: 150px; border: 1px solid #ddd; padding: 5px;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" id="captcha-input" class="form-control form-control-lg"
                                    placeholder="Enter the characters above" autocomplete="off">
                            </div>
                            <button type="button" class="btn btn-outline-secondary" id="refresh-captcha"
                                title="Refresh CAPTCHA">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg py-3 fw-semibold position-relative"
                        id="claimButton" disabled>
                        <i class="bi bi-coin me-2"></i>
                        <span id="button-text">Solve Captcha First</span>
                        <span id="button-spinner" class="spinner-border spinner-border-sm ms-2" role="status"
                            style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>


            </div>
        </div>
    </div>
</div>




<?= $this->endSection() ?>
<?= $this->section("scripts") ?>
<!-- CAPTCHA & SweetAlert2 -->
<script>
    // Define variables globally
    let captchaCompleted = false;
    let canClaimNow = false;
    let countdown;

    // Load CAPTCHA image from server
    function loadCaptchaImage() {
        $.get('<?= site_url("claim/captcha-image") ?>', function(response) {
            if (response.image) {
                $('#captcha-image').attr('src', response.image);
            }
        });
    }

    function updateButtonState() {
        const $btn = $('#claimButton');
        const $btnText = $('#button-text');

        if (!$btn.length) return;

        if (!canClaimNow) {
            $btn.prop('disabled', true);
            $btnText.text('Wait for Timer');
        } else if (!captchaCompleted) {
            $btn.prop('disabled', true).removeClass('btn-success').addClass('btn-primary');
            $btnText.text('Solve CAPTCHA First');
        } else {
            $btn.prop('disabled', false).removeClass('btn-primary').addClass('btn-success');
            $btnText.text('Claim Now');
        }
    }

    function checkCaptchaInput() {
        const captchaInput = $('#captcha-input').val().trim();
        captchaCompleted = captchaInput.length > 0;
        updateButtonState();
    }
</script>

<script>
    $(document).ready(function () {

        function showCaptcha() {
            $('#captcha-container').slideDown();
            loadCaptchaImage();
            $('#captcha-input').focus();
            updateButtonState();
        }

        function hideCaptcha() {
            $('#captcha-container').slideUp();
            $('#captcha-input').val('');
            captchaCompleted = false;
            updateButtonState();
        }

        function showErrorAlert(message) {
            // Hide timer and captcha
            $('#timer').hide();
            hideCaptcha();

            // Show bootstrap alert
            const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" id="vpn-error-alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                    <div>
                        <strong>Access Denied!</strong> ${message}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

            // Remove existing alert if present
            $('#vpn-error-alert').remove();

            // Add alert before the card
            $('.container-fluid').prepend(alertHtml);

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
                    $('#timer').html(`
                        <i class="bi bi-clock text-primary me-2"></i>
                        <span class="text-primary">Next claim in: </span>
                        <span class="badge bg-primary fs-6">${minutes}:${seconds.toString().padStart(2, '0')}</span>
                    `);
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
            $.get('<?= site_url("claim/status") ?>', function (response) {
                // Check if there's an error in the response
                if (response.error) {
                    showErrorAlert(response.error);
                    return;
                }

                if (response.canClaim) {
                    $('#timer').html('<i class="bi bi-check-circle-fill text-success me-2"></i><span class="text-success">Ready to claim!</span>');
                    canClaimNow = true;
                    showCaptcha();
                    hideErrorAlert();
                } else if (response.nextClaimTime) {
                    updateTimer(response.nextClaimTime);
                } else {
                    // Handle case where nextClaimTime is not provided
                    $('#timer').html('<i class="bi bi-exclamation-circle text-warning me-2"></i>Unable to determine next claim time');
                    canClaimNow = false;
                    updateButtonState();
                }
            }).fail(function () {
                $('#timer').html('<i class="bi bi-exclamation-triangle text-danger me-2"></i><span class="text-danger">Error loading claim status</span>');
                showErrorAlert('Unable to connect to server. Please refresh the page.');
            });
        }

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

            const captchaAnswer = $('#captcha-input').val();

            $.ajax({
                url: '<?= site_url("claim/action") ?>',
                method: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                    'captcha-answer': captchaAnswer
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
                        $('#captcha-input').val('');
                        loadCaptchaImage();
                        updateButtonState();


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
                    $('#captcha-input').val('');
                    loadCaptchaImage();
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

        // Refresh CAPTCHA button
        $('#refresh-captcha').click(function () {
            $('#captcha-input').val('');
            loadCaptchaImage();
            captchaCompleted = false;
            updateButtonState();
        });

        // Check CAPTCHA input in real-time
        $('#captcha-input').on('keyup', function () {
            checkCaptchaInput();
        });

        // Check claim status on page load
        checkClaimStatus();
    });
</script>

<?= $this->endSection() ?>