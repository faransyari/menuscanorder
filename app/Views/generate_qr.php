<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Baloo 2', cursive;
            margin-top: 56px;
            display: flex; 
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        h1, h2 {
            font-size: calc(3rem + 1vw);
            margin-bottom: 0.5rem;
        }
        h2 {
            font-size: calc(1rem + 1vw);
        }
    </style>
</head>
<body>
    <?php include 'components/navbar_admin.php'; ?>

    <main class="container-fluid py-4">
        <h2>Generate QR for Tables</h2>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateQRModal" data-table-number="1">Generate QR</button>
        <?php if (empty($tables)): ?>
            <p>No tables found.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Table Number</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $index => $table): ?>
                        <tr>
                            <th scope="row"><?= $index + 1 ?></th>
                            <td><?= $table['table_number'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#showQRModal-<?= $table['table_id'] ?>">Show QR</button>

                                <div class="modal fade" id="showQRModal-<?= $table['table_id'] ?>" tabindex="-1" aria-labelledby="showQRModalLabel-<?= $table['table_id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="showQRModalLabel-<?= $table['table_id'] ?>">Table QR Code</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="<?= base_url('uploads/qr_codes/' . esc($table['restaurant_id']) . '_table_' . esc($table['table_number']) . '.png') ?>" alt="QR Code for Table <?= esc($table['table_number']) ?>" style="max-width: 100%; height: auto;">
                                                <p class="mt-3">Table Number: <?= esc($table['table_number']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Button -->
                                <a href="<?= base_url('admin/generate_qr/delete/' . $table['table_id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this table? This action cannot be undone.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    <div class="modal fade" id="generateQRModal" tabindex="-1" aria-labelledby="generateQRModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateQRModalLabel">Generate QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="generateQRForm" action="generate_qr" method="post">
                        <div class="mb-3">
                            <label for="tableNumberInput" class="form-label">Table Number</label>
                            <input type="number" class="form-control" id="tableNumberInput" name="tableNumber" required>
                        </div>                      
                        <button type="submit" class="btn btn-primary">Generate QR</button>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</script>
</body>
</html>
