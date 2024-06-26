<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Edit Admin Details</h1>
        <form method="post" action="<?= base_url('superadmin/updateAdminDetails/' . $user->id) ?>">
            <div class="mb-3">
                <label for="restaurant_name" class="form-label">Restaurant Name</label>
                <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" value="<?= $restaurant->name ?>">
            </div>
            <div class="mb-3">
                <label for="restaurant_address" class="form-label">Restaurant Address</label>
                <input type="text" class="form-control" id="restaurant_address" name="restaurant_address" value="<?= $restaurant->address ?>">
            </div>
            <input type="hidden" name="restaurant_id" value="<?= $restaurant->restaurant_id ?>">
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
        <br>
        <a href="<?= base_url('superadmin') ?>" class="btn btn-secondary">Back to User Management</a>
    </div>
</body>
</html>
