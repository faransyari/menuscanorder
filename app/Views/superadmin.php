<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Users</h1>
            <button class="btn btn-outline-danger" onclick="window.location.href='./logout'" type="button">Logout</button>
        </div>

        <?php if (session()->getFlashdata('message')) : ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>User Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td><?= $user->username ?></td>
                            <td><?= $user->email ?></td>
                            <td><?= $user->active ? 'Inactive' : 'Active' ?></td>
                            <td>
                                <?php if ($groups[$user->id] == 'superadmin') : ?>
                                    <p>Super Admin</p>
                                <?php else : ?>
                                    <form method="post" action="<?= base_url('superadmin/updateUserType/' . $user->id) ?>">
                                        <select name="user_type" class="form-select" onchange="this.form.submit()">
                                            <option value="user" <?= $groups[$user->id] == 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="admin" <?= $groups[$user->id] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user->active) : ?>
                                    <a href="<?= base_url('superadmin/activateUser/' . $user->id) ?>" class="btn btn-success btn-sm">Activate</a>
                                <?php else : ?>
                                    <a href="<?= base_url('superadmin/deactivateUser/' . $user->id) ?>" class="btn btn-warning btn-sm">Deactivate</a>
                                <?php endif; ?>
                                <a href="<?= base_url('superadmin/deleteUser/' . $user->id) ?>" class="btn btn-danger btn-sm">Delete</a>
                                <?php if ($groups[$user->id] == 'admin') : ?>
                                    <a href="<?= base_url('superadmin/editAdmin/' . $user->id) ?>" class="btn btn-primary btn-sm">Edit Admin Details</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
