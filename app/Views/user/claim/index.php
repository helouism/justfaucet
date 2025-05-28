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

        <!-- IconCaptcha Container -->
        <div id="captcha-container" class="mb-3" style="display: none;">
            <div class="text-center mb-2">
                <label class="form-label">Complete the captcha to enable claim:</label>
            </div>
            <div class="d-flex justify-content-center">
                <div class="iconcaptcha-widget" data-theme="light">

                </div>
                <?php echo \IconCaptcha\Token\IconCaptchaToken::render(); ?>
            </div>
        </div>

        <button type="button" class="btn btn-primary btn-lg" id="claimButton" disabled>
            <span id="button-text">Solve Captcha First</span>
            <span id="button-spinner" class="spinner-border spinner-border-sm ms-2" role="status"
                style="display: none;">
                <span class="visually-hidden">Loading...</span>
            </span>
        </button>

        <div class="mt-2">
            <small class="text-muted">Complete the captcha above to enable the claim button</small>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="<?= base_url("/js/jquery/jquery.min.js") ?>"></script>

<!-- IconCaptcha JS and CSS -->
<link rel="stylesheet" href="<?= base_url('assets/iconcaptcha/client/css/iconcaptcha.min.css') ?>">
<script src="<?= base_url('assets/iconcaptcha/client/js/iconcaptcha.min.js') ?>"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        let countdown;
        let captchaCompleted = false;
        let canClaimNow = false;
        let iconCaptcha;

        function initializeCaptcha() {
            // Clear any existing captcha first
            $('.iconcaptcha-widget').empty();

            iconCaptcha = $('.iconcaptcha-widget').iconCaptcha({
                general: {
                    endpoint: '/captcha-request',
                    fontFamily: 'inherit',
                    showCredits: true,
                },
                security: {
                    interactionDelay: 1500,
                    hoverProtection: true,
                    displayInitialMessage: true,
                    initializationDelay: 500,
                    incorrectSelectionResetDelay: 3000,
                    loadingAnimationDuration: 1000,
                },
                locale: {
                    initialization: {
                        verify: 'Verify that you are human.',
                        loading: 'Loading challenge...',
                    },
                    header: 'Select the image displayed the <u>least</u> amount of times',
                    correct: 'Verification complete.',
                    incorrect: {
                        title: 'Uh oh.',
                        subtitle: "You've selected the wrong image.",
                    },
                    timeout: {
                        title: 'Please wait.',
                        subtitle: 'You made too many incorrect selections.'
                    }
                }
            });

            // Use the working event listener (no namespace version)
            $('.iconcaptcha-widget').on('success', function () {

                captchaCompleted = true;
                updateButtonState();
            });

            $('.iconcaptcha-widget').on('error', function () {

                captchaCompleted = false;
                updateButtonState();
            });
        }

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
            // Don't reset captcha state when showing - only when initializing
            initializeCaptcha();
            updateButtonState();
        }

        function hideCaptcha() {
            $('#captcha-container').slideUp();
            captchaCompleted = false;
            updateButtonState();
        }

        function updateTimer(nextClaimTime) {
            clearInterval(countdown);
            canClaimNow = false;
            hideCaptcha();

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
            $.get('<?= site_url('api/claim/status') ?>', function (response) {
                if (response.canClaim) {
                    // User can claim immediately - show captcha right away
                    $('#timer').text('Ready to claim!');
                    canClaimNow = true;
                    showCaptcha();
                } else {
                    // User must wait - start countdown timer
                    updateTimer(response.nextClaimTime);
                }
            }).fail(function () {
                $('#timer').text('Error loading claim status');
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

            $.ajax({
                url: '<?= site_url('api/claim') ?>',
                method: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>',

                },
                success: function (response) {
                    $spinner.hide();

                    if (response.success) {
                        // Reset captcha state after successful claim
                        captchaCompleted = false;
                        canClaimNow = false;
                        hideCaptcha();

                        // Start the countdown for next claim
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

                        // Show level up notification if applicable
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
                        // Reset captcha on error
                        captchaCompleted = false;
                        updateButtonState();

                        // Re-initialize captcha for retry
                        initializeCaptcha();

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
                    $spinner.hide();

                    // Reset captcha on error
                    captchaCompleted = false;
                    updateButtonState();

                    // Re-initialize captcha for retry
                    initializeCaptcha();

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