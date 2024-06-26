<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <style>
        .scrollable {
            overflow-x: auto;
            white-space: nowrap;
        }

        .heart-logo {
            width: 20px;
            height: auto;
            object-fit: contain; 
        }


        img {
            border-radius: 5% !important ;
            width: 130px;
        }

        .percentage, .price, .heart-logo{
            margin-right: 3px;
        }


        .card-body {
            padding: 0.2rem;
        }
        
        .col-feature {
            width: 150px;
        }

        .food-icon {
            border-radius: 5% !important ;
            width: 130px;
        }
        body {
            font-family: 'Baloo 2', cursive;
            background-color: #000; /* Dark theme background */
            color: #fff; /* Light text color for contrast */
            padding-top: 70px; /* Set to the height of the navbar */
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent black */
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #007bff; /* Blue accent border */
        }

        .navbar-brand, .navbar-nav .nav-link, .navbar-text {
            color: #fff !important; /* White text color for contrast */
        }

        .navbar-nav .nav-link:hover {
            color: #007bff !important; /* Blue color for hover state */
        }

        .btn-dark, .btn-success {
            border: none; /* No border */
            margin-right: 5px; /* Spacing between buttons */
            border-radius: 20px; /* Rounded borders for the buttons */
        }

        .btn-dark:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .modal-content {
            background-color: #121212; /* Dark background for modal */
            color: #fff; /* Light text color for modal content */
        }

        .table thead th {
            color: white;
        }

        .table tbody tr {
            background-color: rgba(255, 255, 255, 0.1); /* Slightly lighter rows for contrast */
            color: #fff; /* Light text color for modal content */

        }

        btn-custom {
            background-color: #007bff; /* Blue background color for buttons */
            border: none; /* No border */
            border-radius: 20px; /* Rounded borders for the buttons */
            color: #fff; /* White text color */
            padding: 8px 20px; /* Padding inside the button */
            font-size: 0.9rem; /* Font size for the text inside the button */
            transition: background-color 0.3s ease; /* Smooth background color transition */
        }

        .btn-custom:hover {
            background-color: #0056b3; /* Darker blue on hover */
            color: #fff; /* Ensure text remains white on hover */
        }

        /* Apply custom button styles to specific buttons */
        .btn-dark, .btn-success, .btn-outline-info {
            border: none; /* Remove default border */
            border-radius: 20px; /* Rounded borders for the buttons */
            padding: 8px 20px; /* Padding inside the button */
            font-size: 0.9rem; /* Font size for the text inside the button */
        }

        /* Modify the button colors accordingly */
        .btn-dark {
            background-color: #007bff; /* Blue background color */
        }

        .btn-dark:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .btn-success {
            background-color: #28a745; /* Green background color */
        }

        .btn-success:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .btn-outline-info {
            color: #fff; /* White text */
            border-color: #007bff; /* Blue border */
            background-color: #007bff;
        }

        .btn-outline-info:hover {
            background-color: #0056b3; /* Lighter blue background on hover */
            border-color: #0056b3; /* Lighter blue border on hover */
            color: #fff; /* White text on hover */
        }
        
        .btn-outline-danger {
            color: #fff; /* White text */
            border-color: #dc3545; /* Red border */
            background-color: #dc3545;
            border-radius: 20px; /* Rounded borders for the buttons */
        }

        .btn-outline-danger:hover {
            background-color: #c82333; /* Darker red background on hover */
            border-color: #c82333; /* Darker red border on hover */
            color: #fff; /* White text on hover */
        }
        
        /* Customizing scrollbar for dark theme */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #121212;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #007bff; /* Blue scrollbar handle */
            border-radius: 20px;
            border: 3px solid #121212; /* Dark background around the handle */
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            margin-top: 5px; /* Adjust this value as needed to align it with the button */

        }

        .quantity-selector button {
            width: 40px;
            height: 40px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .quantity-selector input {
            width: 40px;
            text-align: center;
            background-color: transparent;
            border: none;
            color: white;
            pointer-events: none;
        }

        
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top" style="background-color: rgba(0, 0, 0, 0.8);">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MenuScanOrder</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                </ul>
                <button class="btn btn-outline-info cart-modal" type="button" data-bs-toggle="modal" data-bs-target="#cartModal">Cart</button>
                <button class="btn btn-outline-danger" onclick="window.location.href='./logout'" type="button">Logout</button>

            </div>
        </div>
    </nav>
    <body >

    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
                </div>
                <div class="modal-body">
                    <div id="cartContents" class="cart-contents">
                        <!-- JavaScript will populate this -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger " data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" onclick="return sendOrder();">Order</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt">
        <div class="container" style="width: 100%; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;">
            <div class="row" style="display: flex; width: max-content;">
                <div class="col-feature d-flex">
                    <?php foreach ($category as $category): ?>
                        <button type="button" id=<?= esc($category['name']) ?> class="btn btn-outline-info rounded-pill " style="margin-right: 7px;"><?= esc($category['name']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    

    <hr class="mt-3 mt-3">

    <div class="container mt">
        <h4>Featured items</h4>
        <div class="container" style="width: 100%; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;">
            <div class="row" style="display: flex; width: max-content;">

                <?php foreach ($menuItems as $item): ?>
                    <?php if ($item['is_featured'] == 1): // Check if the item is explicitly featured as '1' ?>
                        <div class="col-feature">
                            <div class="card-0">
                                <img src="<?= base_url('uploads/menu_items/' . esc($item['image'])) ?>" class="card-img-top" alt="<?= esc($item['name']) ?>">
                                <div class="card-body">
                                    <h6 class="card-title mb-1"><?= esc($item['name']) ?></h6>
                                    <p class="card-text m-0">A$<?= esc($item['price']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
    </div> 

    <div class="container">
        <?php 
        $groupedItems = [];
        foreach ($menuItems as $item) {
            $groupedItems[$item['category_name']][] = $item; // Group items by category_name
        }

        foreach ($groupedItems as $categoryName => $items): ?>
            <hr class="mt-3 mt-3">

            <h4><?= esc($categoryName) ?></h4> <!-- Category name -->
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 d-flex mt-3" id="item-container-<?= $item['item_id'] ?>">
                        <div class="col">
                            <h5><?= esc($item['name']) ?></h6>
                            <p>A$<?= esc($item['price']) ?></p>

                            <p><?= esc($item['description']) ?></p>
                        </div>
                        <div>
                            <img class="food-icon" src="<?= base_url('uploads/menu_items/' . esc($item['image'])) ?>">
                            <div class="d-flex flex-column align-items-center">
                                <button class="btn btn-outline-info rounded-pill mt-2 add-to-cart-btn" data-item-id="<?= $item['item_id'] ?>">Add to Cart</button>
                                <div class="quantity-selector d-none" id="quantity-selector-<?= $item['item_id'] ?>">
                                    <button class="btn btn-outline-info minus-btn">-</button>
                                    <input type="text" class="quantity-input" value="1" readonly>
                                    <button class="btn btn-outline-info plus-btn">+</button>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var orderList = {};

        document.addEventListener("DOMContentLoaded", function() {

            // Add to Cart button functionality
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const container = document.getElementById('quantity-selector-' + itemId);
                    this.classList.add('d-none'); // Hide add to cart button
                    container.classList.remove('d-none'); // Show quantity selector

                    // Handle adding to cart with AJAX
                    orderList[itemId] = 1;
                    container.querySelector('.quantity-input').value = 1; // Set initial quantity to 1
                });
            });

            // Plus button functionality
            document.querySelectorAll('.plus-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.quantity-selector');
                    const input = container.querySelector('.quantity-input');
                    let quantity = parseInt(input.value);
                    quantity++;
                    input.value = quantity;

                    // Update cart via AJAX
                    const itemId = container.parentNode.querySelector('.add-to-cart-btn').getAttribute('data-item-id');
                    orderList[itemId] = quantity;
                });
            });

            // Minus button functionality
            document.querySelectorAll('.minus-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.quantity-selector');
                    const input = container.querySelector('.quantity-input');
                    let quantity = parseInt(input.value);
                    if (quantity > 1) {
                        quantity--;
                        input.value = quantity;
                        const itemId = container.parentNode.querySelector('.add-to-cart-btn').getAttribute('data-item-id');
                        orderList[itemId] = quantity;
                    } else {
                        // If quantity goes to 0, revert UI changes
                        const itemId = container.parentNode.querySelector('.add-to-cart-btn').getAttribute('data-item-id');
                        container.classList.add('d-none'); // Hide quantity selector
                        document.querySelector('.add-to-cart-btn[data-item-id="' + itemId + '"]').classList.remove('d-none');
                        delete orderList[itemId];
                    }
                });
            });

            document.querySelectorAll('.cart-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const modalBody = document.getElementById('cartContents');
                    // Initialize the table with headers
                    modalBody.innerHTML = `
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    `;

                    let total = 0;
                    const tbody = modalBody.querySelector('tbody');

                    // Populate the table body with cart items
                    for (const itemId in orderList) {
                        const item = document.getElementById('item-container-' + itemId);
                        if (!item) continue; // Skip if item details are missing
                        const name = item.querySelector('h5').innerText;
                        const price = parseFloat(item.querySelector('p').innerText.replace('A$', ''));
                        const quantity = orderList[itemId];
                        const itemTotal = price * quantity;
                        total += itemTotal;
                        
                        const row = `
                            <tr>
                                <td>${name}</td>
                                <td>${quantity}</td>
                                <td>A$${itemTotal.toFixed(2)}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row; // Add each row to the table body
                    }

                    // Append the total row at the end of the table body
                    const totalRow = `
                        <tr>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td><strong>A$${total.toFixed(2)}</strong></td>
                        </tr>
                    `;
                    tbody.innerHTML += totalRow;
                });
            });

        });

        function sendOrder() {
            if (!confirm('Send Order?')) {
                return false; // Prevent further actions if the user cancels.
            }

            const data = {
                items: orderList
            };

            fetch('cart/order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => {
                if (!response.ok) {
                    // Convert non-2xx HTTP responses into errors:
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json(); // Parse JSON data from the response
            })
            .then(data => {
                if (data.success) {
                    alert('Order successfully sent!'); // Show success message
                    window.location.reload(); // Refresh the page if the order was successful
                } else {
                    alert('Failed to send the order: ' + data.message); // Show error message from server
                }
            })
            .catch(error => {
                console.error('There was a problem with your fetch operation:', error);
                alert('Error sending order: ' + (error.message || 'Unknown error')); // Show error message
            });

            return false; // Keep this to prevent the default form action while fetch is pending
        }


        



    </script>

    
</body>
</html>
