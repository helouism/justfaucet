<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>
<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-person-gear me-2"></i>Edit User
            </h1>
            <p class="lead text-muted">Modify user account information</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-person-fill text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">Editing: <?= esc(
                            $username
                        ) ?></h4>
                    </div>

                    <?= form_open("admin/manage-users/update/" . $userId, [
                        "class" => "needs-validation",
                        "novalidate" => true,
                    ]) ?>
                        <div class="mb-4">
                            <label for="username" class="form-label fw-medium">
                                <i class="bi bi-person me-1"></i>Username
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="bi bi-at"></i>
                                </span>
                                <?= form_input([
                                    "name" => "username",
                                    "id" => "username",
                                    "class" =>
                                        "form-control" .
                                        (isset($validation) &&
                                        $validation->hasError("username")
                                            ? " is-invalid"
                                            : ""),
                                    "value" => set_value("username", $username),
                                    "placeholder" => "Enter username",
                                    "required" => true,
                                ]) ?>
                                <?php if (
                                    isset($validation) &&
                                    $validation->hasError("username")
                                ): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError("username") ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>
                                <?= form_input([
                                    "name" => "email",
                                    "id" => "email",
                                    "type" => "email",
                                    "class" =>
                                        "form-control" .
                                        (isset($validation) &&
                                        $validation->hasError("email")
                                            ? " is-invalid"
                                            : ""),
                                    "value" => set_value("email", $email),
                                    "placeholder" => "Enter email address",
                                    "required" => true,
                                ]) ?>
                                <?php if (
                                    isset($validation) &&
                                    $validation->hasError("email")
                                ): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError("email") ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?= anchor(
                                "admin/manage-users",
                                '<i class="bi bi-arrow-left me-2"></i>Back to Users',
                                [
                                    "class" =>
                                        "btn btn-outline-secondary btn-lg me-md-2",
                                ]
                            ) ?>
                            <?= form_submit([
                                "name" => "submit",
                                "value" => "Update User",
                                "class" => "btn btn-primary btn-lg px-4",
                            ]) ?>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-info-circle text-white"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold text-primary">Important Notes</h5>
                    </div>
                    <div class="alert alert-info border-0 shadow-sm" role="alert">
                        <ul class="mb-0">
                            <li>Username changes will affect user login credentials</li>
                            <li>Email changes may require re-verification</li>
                            <li>All changes are logged for security purposes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("scripts") ?>

<script>
    $(document).ready(function () {
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        $('form').on('submit', function (e) {
            e.preventDefault();

            // Check if form is valid
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }

            // Show loading state
            const submitBtn = $(this).find('input[type="submit"]');
            const originalText = submitBtn.val();
            submitBtn.prop('disabled', true).val('Updating...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "<?= site_url(
                                    "admin/manage-users"
                                ) ?>";
                            }
                        });
                    } else if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: response.message,
                            showConfirmButton: true
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An unexpected error occurred. Please try again.',
                        showConfirmButton: true
                    });
                },
                complete: function() {
                    // Reset button state
                    submitBtn.prop('disabled', false).val(originalText);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
