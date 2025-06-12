<?= $this->extend("layout/page_layout") ?>
<?= $this->section("content") ?>
<div class="py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="bi bi-people-fill me-2"></i>Manage Users
            </h1>
            <p class="lead text-muted">Monitor and manage platform users</p>
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
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata("error")): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Error!</h6>
                    <p class="mb-0"><?= session()->getFlashdata("error") ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata("warning")): ?>
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Warning!</h6>
                    <p class="mb-0"><?= session()->getFlashdata(
                        "warning"
                    ) ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                            <i class="bi bi-table text-white"></i>
                        </div>
                        <h4 class="mb-0 fw-semibold text-primary">User Management Table</h4>
                    </div>

                    <div class="table-responsive">
                        <table id="usersTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="fw-semibold">
                                        <i class="bi bi-hash me-1"></i>ID
                                    </th>
                                    <th class="fw-semibold">
                                        <i class="bi bi-person me-1"></i>Username
                                    </th>
                                    <th class="fw-semibold">
                                        <i class="bi bi-envelope-check me-1"></i>Email Activated
                                    </th>
                                    <th class="fw-semibold">
                                        <i class="bi bi-flag me-1"></i>Status
                                    </th>
                                    <th class="fw-semibold">
                                        <i class="bi bi-calendar-plus me-1"></i>Registered At
                                    </th>
                                    <th class="fw-semibold">
                                        <i class="bi bi-clock me-1"></i>Last Active
                                    </th>
                                    <th class="fw-semibold">
                                        <i class="bi bi-star me-1"></i>Level
                                    </th>
                                    <th class="fw-semibold">
                                        <i class="bi bi-gear me-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark"><?= esc(
                                                $user["id"]
                                            ) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary bg-gradient rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-white small"></i>
                                                </div>
                                                <span class="fw-medium"><?= esc(
                                                    $user["username"]
                                                ) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($user["is_active"]): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Activated
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user["is_banned"]): ?>
                                                <span class="badge bg-danger" data-bs-toggle="tooltip"
                                                      data-bs-placement="right" data-bs-title="<?= $user[
                                                          "ban_reason"
                                                      ] ?>">
                                                    <i class="bi bi-ban me-1"></i>Banned
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Active
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?= date(
                                                    "M d, Y",
                                                    strtotime(
                                                        $user["created_at"]
                                                    )
                                                ) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?= date(
                                                    "M d, Y",
                                                    strtotime(
                                                        $user["last_active"]
                                                    )
                                                ) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <i class="bi bi-star-fill me-1"></i>Level <?= esc(
                                                    $user["level"]
                                                ) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= site_url(
                                                    "admin/manage-users/edit/" .
                                                        $user["id"]
                                                ) ?>"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                                <?php if (
                                                    $user["is_banned"]
                                                ): ?>
                                                    <a href="<?= site_url(
                                                        "admin/manage-users/unban/" .
                                                            $user["id"]
                                                    ) ?>"
                                                       class="btn btn-outline-success btn-sm"
                                                       onclick="return confirm('Are you sure you want to unban this user?')">
                                                        <i class="bi bi-check-circle me-1"></i>Unban
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= site_url(
                                                        "admin/manage-users/ban/" .
                                                            $user["id"]
                                                    ) ?>"
                                                       class="btn btn-outline-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to ban this user?')">
                                                        <i class="bi bi-ban me-1"></i>Ban
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm mt-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle fs-4 me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Quick Tip</h6>
                                <p class="mb-0">For banned users, hover over the "Banned" status to see the ban reason.</p>
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
    $(document).ready(function () {
        $('#usersTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25,
            "lengthMenu": [10, 25, 50, 100],
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search users...",
                "lengthMenu": "_MENU_ users per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ users",
                "infoEmpty": "No users available",
                "infoFiltered": "(filtered from _MAX_ total users)",
                "zeroRecords": "No matching users found"
            },
            "responsive": true,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    });
</script>
<?= $this->endSection() ?>
