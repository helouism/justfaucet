<?= $this->extend('layout/page_layout') ?>

<?= $this->section('content') ?>
<div class="container py-4 fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="content-card card shadow">
                <div class="card-header " style="color:var(--text-color)">
                    <h5 class="card-title mb-0">Change Password</h5>
                </div>
                <div class="card-body " style="color:var(--text-color)">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('profile/update') ?>" method="post" id="passwordForm" novalidate>
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="oldPassword" class="form-label">
                                <i class="fas fa-lock me-2"></i>Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input name="old_password" type="password" id="oldPassword"
                                    class="form-control <?= isset($errors['old_password']) ? 'is-invalid' : '' ?>"
                                    required autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleOldPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter your current password to confirm your identity
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="newPassword" class="form-label">
                                <i class="fas fa-key me-2"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input name="new_password" type="password" id="newPassword"
                                    class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                                    required autocomplete="new-password" minlength="8" maxlength="128">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text stat-label">
                                <i class="fas fa-shield-alt me-1"></i>
                                Password must be 8-128 characters long and contain:
                                <ul class="small mt-1 mb-0">
                                    <li>At least one uppercase letter (A-Z)</li>
                                    <li>At least one lowercase letter (a-z)</li>
                                    <li>At least one number (0-9)</li>
                                    <li>At least one special character (!@#$%^&*)</li>
                                </ul>
                            </div>
                            <!-- Password strength indicator -->
                            <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="form-text" id="strengthText"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="newPasswordConfirm" class="form-label">
                                <i class="fas fa-check-double me-2"></i>Confirm New Password <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input name="new_password_confirm" type="password" id="newPasswordConfirm"
                                    class="form-control <?= isset($errors['new_password_confirm']) ? 'is-invalid' : '' ?>"
                                    required autocomplete="new-password" minlength="8" maxlength="128">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text stat-label">
                                <i class="fas fa-sync me-1"></i>
                                Repeat your new password to confirm
                            </div>
                            <div id="passwordMatch" class="invalid-feedback"></div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('profile') ?>" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-4 content-card">
                <div class="card-header " style="color:var(--text-color)">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Password Security Tips
                    </h6>
                </div>
                <div class="card-body " style="color:var(--text-color)">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>Use a unique password</li>
                                <li><i class="fas fa-check text-success me-2"></i>Include mixed case letters</li>
                                <li><i class="fas fa-check text-success me-2"></i>Add numbers and symbols</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-times text-danger me-2"></i>Don't reuse old passwords</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Avoid personal information</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Don't share your password</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            });
        }
    });
</script>

<?= $this->endSection() ?>