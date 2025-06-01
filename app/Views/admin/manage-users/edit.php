<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="mb-4">Edit <?= $username ?></h1>
    <div class="card">
        <div class="card-body">
            <form action="<?= site_url('admin/manage-users/update/' . $userId) ?>" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= esc($username) ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= esc($email) ?>"
                        required>
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>

</div>
<script src="<?= base_url("/js/jquery/jquery.min.js") ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script {csp-script-nonce}>
    $(document).ready(function () {
        $('form').on('submit', function (e) {
            e.preventDefault();

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
                                window.location.href = '<?= site_url('admin/manage-users') ?>';
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
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>