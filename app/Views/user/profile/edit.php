<?= $this->extend("layout/page_layout") ?>

<?= $this->section("content") ?>
<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-shield-lock me-2"></i>Change Password
            </h1>

        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-key text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Update Password</h4>
                    </div>
</div>
                    <?php if (session()->getFlashdata("success")): ?>
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Success!</h6>
                                    <p class="mb-0"><?= session()->getFlashdata(
                                        "success"
                                    ) ?></p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata("error")): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Error!</h6>
                                    <p class="mb-0"><?= session()->getFlashdata(
                                        "error"
                                    ) ?></p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Please fix the following errors:</h6>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php
                    $attributes = ["id" => "passwordForm"];
                    echo form_open("profile/update", $attributes);
                    ?>


                        <div class="mb-4">
                            <label for="oldPassword" class="form-label fw-medium">
                                <i class="bi bi-lock me-2"></i>Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text ">
                                    <i class="bi bi-shield-lock"></i>
                                </span>
                                <input name="old_password" type="password" id="oldPassword"
                                    class="form-control <?= isset(
                                        $errors["old_password"]
                                    )
                                        ? "is-invalid"
                                        : "" ?>"
                                    required autocomplete="current-password" placeholder="Enter current password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleOldPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter your current password to confirm your identity
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="newPassword" class="form-label fw-medium">
                                <i class="bi bi-key me-2"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text ">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                <input name="new_password" type="password" id="newPassword"
                                    class="form-control <?= isset(
                                        $errors["new_password"]
                                    )
                                        ? "is-invalid"
                                        : "" ?>"
                                    required autocomplete="new-password" minlength="8" maxlength="128" placeholder="Enter new password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Password must be 8-128 characters long and contain:
                                </small>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="bi bi-check-circle me-1"></i>At least one uppercase letter (A-Z)<br>
                                        <i class="bi bi-check-circle me-1"></i>At least one lowercase letter (a-z)
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="bi bi-check-circle me-1"></i>At least one number (0-9)<br>
                                        <i class="bi bi-check-circle me-1"></i>At least one special character (!@#$%^&*)
                                    </small>
                                </div>
                            </div>
                            <!-- Password strength indicator -->
                            <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="form-text fw-medium" id="strengthText"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="newPasswordConfirm" class="form-label fw-medium">
                                <i class="bi bi-check2-circle me-2"></i>Confirm New Password <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text ">
                                    <i class="bi bi-check2-square"></i>
                                </span>
                                <input name="new_password_confirm" type="password" id="newPasswordConfirm"
                                    class="form-control <?= isset(
                                        $errors["new_password_confirm"]
                                    )
                                        ? "is-invalid"
                                        : "" ?>"
                                    required autocomplete="new-password" minlength="8" maxlength="128" placeholder="Confirm new password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="bi bi-arrow-repeat me-1"></i>
                                    Repeat your new password to confirm
                                </small>
                            </div>
                            <div id="passwordMatch" class="invalid-feedback"></div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url(
                                "profile"
                            ) ?>" class="btn btn-outline-secondary btn-lg me-md-2">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-lightbulb text-white"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold text-primary">Password Security Tips</h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-primary-subtle h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-success mb-2">
                                        <i class="bi bi-check-circle-fill me-2"></i>Best Practices
                                    </h6>
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><i class="bi bi-check text-success me-2"></i>Use a unique password</li>
                                        <li class="mb-1"><i class="bi bi-check text-success me-2"></i>Include mixed case letters</li>
                                        <li class="mb-1"><i class="bi bi-check text-success me-2"></i>Add numbers and symbols</li>
                                        <li class="mb-0"><i class="bi bi-check text-success me-2"></i>Make it at least 12 characters</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-primary-subtle h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-danger mb-2">
                                        <i class="bi bi-x-circle-fill me-2"></i>Avoid These
                                    </h6>
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><i class="bi bi-x text-danger me-2"></i>Don't reuse old passwords</li>
                                        <li class="mb-1"><i class="bi bi-x text-danger me-2"></i>Avoid personal information</li>
                                        <li class="mb-1"><i class="bi bi-x text-danger me-2"></i>Don't share your password</li>
                                        <li class="mb-0"><i class="bi bi-x text-danger me-2"></i>Avoid common words</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section("scripts") ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Password visibility toggles
        const toggleButtons = [
            { button: 'toggleOldPassword', input: 'oldPassword' },
            { button: 'toggleNewPassword', input: 'newPassword' },
            { button: 'toggleConfirmPassword', input: 'newPasswordConfirm' }
        ];

        toggleButtons.forEach(item => {
            const button = document.getElementById(item.button);
            const input = document.getElementById(item.input);

            if (button && input) {
                button.addEventListener('click', function () {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });
            }
        });

        // Password strength checker
        const newPasswordInput = document.getElementById('newPassword');
        const strengthIndicator = document.getElementById('passwordStrength');
        const strengthBar = strengthIndicator.querySelector('.progress-bar');
        const strengthText = document.getElementById('strengthText');

        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function () {
                const password = this.value;
                const strength = calculatePasswordStrength(password);

                if (password.length > 0) {
                    strengthIndicator.style.display = 'block';
                    updateStrengthIndicator(strength);
                } else {
                    strengthIndicator.style.display = 'none';
                }
            });
        }

        // Password confirmation checker
        const confirmPasswordInput = document.getElementById('newPasswordConfirm');
        const passwordMatch = document.getElementById('passwordMatch');

        if (confirmPasswordInput && newPasswordInput) {
            confirmPasswordInput.addEventListener('input', function () {
                checkPasswordMatch();
            });

            newPasswordInput.addEventListener('input', function () {
                if (confirmPasswordInput.value.length > 0) {
                    checkPasswordMatch();
                }
            });
        }

        function checkPasswordMatch() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword.length > 0) {
                if (newPassword === confirmPassword) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    passwordMatch.textContent = '';
                } else {
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                    passwordMatch.textContent = 'Passwords do not match';
                }
            } else {
                confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                passwordMatch.textContent = '';
            }
        }

        function calculatePasswordStrength(password) {
            let score = 0;

            // Length check
            if (password.length >= 8) score += 1;
            if (password.length >= 12) score += 1;

            // Character variety checks
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^A-Za-z0-9]/.test(password)) score += 1;

            return Math.min(score, 5);
        }

        function updateStrengthIndicator(strength) {
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];
            const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const widths = [20, 40, 60, 80, 100];

            strengthBar.style.width = widths[strength] + '%';
            strengthBar.style.backgroundColor = colors[strength];
            strengthText.textContent = texts[strength];
            strengthText.style.color = colors[strength];
        }

        // Form submission with loading state
        const form = document.getElementById('passwordForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Updating...';
                submitBtn.classList.add('disabled');
            });
        }
    });
</script>

<?= $this->endSection() ?>
