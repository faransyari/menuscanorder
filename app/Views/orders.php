<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        body {
            font-family: 'Baloo 2', cursive;
            margin-top: 56px; /* Reset default margin */
            display: flex; /* Enable flex container */
            flex-direction: column; /* Stack children vertically */
            justify-content: center; /* Center children vertically */
            align-items: center; /* Center children horizontally */
            text-align: center; /* Center text */

        }

        h1, h2 {
            font-size: calc(3rem + 1vw); /* Responsive font size for h1 */
            margin-bottom: 0.5rem; /* Spacing between h1 and h2 */
        }

        h2 {
            font-size: calc(1rem + 1vw); /* Responsive font size for h2 */
        }

    </style>
</head>
<body>
    <?php include 'components/navbar_admin.php'; ?>


    <main class="container-fluid py-4">
        <h2>Orders Management</h2>

        <?php if (empty($customerOrders)): ?>
            <p>No Orders Found</p>
        <?php else: ?>
            <table class="table">
        <thead>
            <tr>
                <th scope="col">Order ID</th>
                <th scope="col">Table Number</th>
                <th scope="col">Orders</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>

        <?php
        $statusOrder = ['placed' => 1, 'in progress' => 2, 'completed' => 3, 'cancelled' => 4];

        usort($customerOrders, function($a, $b) use ($statusOrder) {
            return $statusOrder[$a['status']] <=> $statusOrder[$b['status']];
        });
        ?>
        <?php foreach ($customerOrders as $order): ?>
            
            <tr>
                <th scope="row"><?= esc($order['order_id']) ?></th>
                <td><?= esc($order['table_number']) ?></td>
                <td>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderDetailsModal<?= esc($order['order_id']) ?>">View</button>
                     <!-- Modal for each order -->
                    <div class="modal fade" id="orderDetailsModal<?= esc($order['order_id']) ?>" tabindex="-1" aria-labelledby="orderDetailsModalLabel<?= esc($order['order_id']) ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orderDetailsModalLabel<?= esc($order['order_id']) ?>">
                                        Order Details for Table <?= esc($order['table_number']) ?> - Order #<?= esc($order['order_id']) ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Item</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $count = 1; ?>
                                            <?php foreach ($orderItems[$order['order_id']] as $item): ?>
                                                <tr>
                                                    <th scope="row"><?= $count++ ?></th>
                                                    <td><?= esc($item['item_name']) ?></td>
                                                    <td><?= esc($item['quantity']) ?></td>
                                                    <td>$<?= esc($item['price'] * $item['quantity']) ?></td>  <!-- Multiply price by quantity -->

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align:right">Total:</th>
                                                <th>$<?= esc($order['total_price']) ?></th>
                                            </tr>
                                            <tr>
                                            <?php
                                            $next_status = [
                                                'placed' => 'in progress',
                                                'in progress' => 'completed',
                                                'completed' => 'completed', // No next status after completed
                                                'cancelled' => 'cancelled'
                                            ];
                                            $current_status = $order['status'];
                                            $next_label = $next_status[$current_status];

                                            // or cancelled
                                            if ($current_status !== 'completed' && $current_status !== 'cancelled'):
                                            ?>
                                                <button onclick="updateOrderStatus('<?= esc($order['order_id']) ?>', '<?= $next_label ?>')" class="btn btn-info">
                                                    Change to <?= ucfirst($next_label) ?>
                                                </button>

                                                <button onclick="updateOrderStatus('<?= esc($order['order_id']) ?>', 'cancelled')" class="btn btn-danger">
                                                    Cancel Order
                                                </button>
                                            <?php endif; ?>

                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            
                <td>
                    <?php
                        $status_classes = [
                            'placed' => 'warning', 
                            'in progress' => 'info', 
                            'completed' => 'success', 
                            'cancelled' => 'danger'
                        ];
                        $status_label = $status_classes[$order['status']] ?? 'secondary';
                    ?>
                    <span class="btn btn-<?= $status_label ?> disabled"><?= ucfirst($order['status']) ?></span>
                </td>
            </tr>

           

        <?php endforeach; ?>

        </tbody>
    </table>

    <?php endif; ?>

    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    function updateOrderStatus(orderId, newStatus) {
        $.ajax({
            url: '<?= site_url('admin/orders/update_order_status') ?>', // Adjusted to match your CodeIgniter routes
            type: 'POST',
            data: { order_id: orderId, status: newStatus },
            success: function(response) {
                alert('Order status updated!');
                location.reload(); // Reload the page to reflect the changes
            },
            error: function(xhr, status, error) {
                alert('Error updating status: ' + xhr.responseText);
            }
        });
    }
    </script>
        
    
</body>
</html>
