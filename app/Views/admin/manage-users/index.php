<?= $this->extend('layout/page_layout') ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="mb-4">Manage Users</h1>

    <div class="card">
        <div class="card-body">
            <table id="usersTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>

                        <th>Active</th>
                        <th>Registered At</th>
                        <th>Last Active</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['id']) ?></td>
                            <td><?= esc($user['username']) ?></td>
                            <td><?= $isActive ?></td>
                            <td><?= esc($user['created_at']) ?></td>
                            <td><?= esc($user['last_active']) ?></td>
                            <td><?= esc($user['level']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>
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