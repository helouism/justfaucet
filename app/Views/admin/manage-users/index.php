<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="mb-4">Manage Users</h1>


    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table id="usersTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>

                        <th>Username</th>
                        <th>Email Activated</th>
                        <th>Status</th>
                        <th>Ban Reason</th>
                        <th>Registered At</th>
                        <th>Last Active</th>
                        <th>Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['id']) ?></td>

                            <td><?= esc($user['username']) ?></td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span>Yes</span>
                                <?php else: ?>
                                    <span>No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_banned']): ?>
                                    <span class="badge bg-danger">Banned</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Not Banned</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($user['ban_reason']) ?></td>
                            <td><?= esc($user['created_at']) ?></td>
                            <td><?= esc($user['last_active']) ?></td>
                            <td><?= esc($user['level']) ?></td>
                            <td>
                                <!-- Edit User button -->
                                <a href="<?= site_url('admin/manage-users/edit/' . $user['id']) ?>"
                                    class="btn btn-primary btn-sm">Edit</a>

                                <!-- Ban/Unban -->
                                <?php if ($user['is_banned']): ?>
                                    <a href="<?= site_url('admin/manage-users/unban/' . $user['id']) ?>"
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Are you sure you want to unban this user?')">Unban</a>
                                <?php else: ?>
                                    <a href="<?= site_url('admin/manage-users/ban/' . $user['id']) ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to ban this user?')">Ban</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?= base_url("assets/jquery/jquery.min.js") ?>"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#usersTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "language": {
                "search": "Search Users:",
                "lengthMenu": "Show _MENU_ users per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ users",
                "infoEmpty": "No users available",
                "zeroRecords": "No matching users found"
            }
        });
    });
</script>
<?= $this->endSection() ?>