<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
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

        @media (max-width: 768px) {
        .hide-in-portrait {
            display: none;
        }
    }

    </style>
</head>
<body>
    <?php include 'components/navbar_admin.php'; ?>

    <main class="container-fluid py-4">
        <h2>Menu Management</h2>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMenuItemModal">
            Add Menu Item
        </button>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
            Edit Category
        </button>
        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Edit Categories</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <form action="<?= site_url('admin/menu_management/add_category') ?>" method="post">
                    
                        <div class="mb-3 d-flex">
                            <input type="text" class="form-control me-2" name="categoryName" placeholder="New Category Name" required>
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </form>

                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Category Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody">
                            <?php foreach ($categories as $index => $category): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($category['name']) ?></td>
                                    <td>
                                        <form action="<?= site_url('/menu_management/delete_category/' . $category['category_id']) ?>" method="post" onsubmit="return confirm('Are you sure?');">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addMenuItemModal" tabindex="-1" aria-labelledby="addMenuItemModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMenuItemModalLabel">Add Menu Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <form id="addMenuItemForm" method="post" action="<?= site_url('admin/menu_management/addMenuItem') ?>" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="menuItemName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="menuItemName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="menuItemPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="menuItemPrice" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="menuItemDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="menuItemDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="menuItemCategory" class="form-label">Category</label>
                            <select class="form-select" id="menuItemCategory" name="category" required>
                                <option selected>None Selected</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= esc($category['name']) ?>"><?= esc($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>


                        </div>

                        <div class="mb-3">
                            <label for="menuItemImg" class="form-label">Image</label>
                            <input type="file" class="form-control" id="menuItemImg" name="image" accept="image/*">
                        </div>
                    </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="addMenuItemForm" class="btn btn-primary">Add Item</button>
                    </div>
                </div>
            </div>
        </div>
        <?php if (empty($menuItems)): ?>
            <p>No Items Added</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Image</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="hide-in-portrait">Price</th>
                        <th scope="col" class="hide-in-portrait">Description</th>
                        <th scope="col" class="hide-in-portrait">Category</th>
                        <th scope="col">Actions</th>
                        <th scope="col">Featured</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menuItems as $item): ?>
                        <tr>
                            <td><img src="<?= base_url('uploads/menu_items/' . esc($item['image'])) ?>" alt="<?= esc($item['name']) ?>" style="width: 60px; height: 60px;"></td>
                            <td ><?= esc($item['name']) ?></td>
                            <td class="hide-in-portrait">$<?= esc($item['price']) ?></td>
                            <td class="hide-in-portrait"><?= esc($item['description']) ?></td>
                            <td class="hide-in-portrait"><?= esc($item['category']) ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editMenuItemModal-<?= $item['item_id'] ?>">Edit</button>
                                <a href="<?= site_url('menu_management/delete/' . $item['item_id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                            <td>
                                <input type="checkbox" class="form-check-input feature-checkbox" id="featuredCheck<?= $item['item_id'] ?>" 
                                <?= $item['is_featured'] ? 'checked' : '' ?> 
                                data-item-id="<?= $item['item_id'] ?>">

                            </td>
                        </tr>
                        <div class="modal fade" id="editMenuItemModal-<?= $item['item_id'] ?>" tabindex="-1" aria-labelledby="editMenuItemModalLabel-<?= $item['item_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editMenuItemModalLabel-<?= $item['item_id'] ?>">Edit Menu Item</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="editMenuItemForm-<?= $item['item_id'] ?>" method="post" action="<?= site_url('/admin/menu_management/updateMenuItem') ?>" enctype="multipart/form-data">
                                            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                            <div class="mb-3">
                                                <label for="menuItemName-<?= $item['item_id'] ?>" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="menuItemName-<?= $item['item_id'] ?>" name="name" required value="<?= esc($item['name']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="menuItemPrice-<?= $item['item_id'] ?>" class="form-label">Price</label>
                                                <input type="text" class="form-control" id="menuItemPrice-<?= $item['item_id'] ?>" name="price" required value="<?= esc($item['price']) ?>">
                                            </div>      
                                            <div class="mb-3">
                                                <label for="menuItemDescription-<?= $item['item_id'] ?>" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="menuItemDescription-<?= $item['item_id'] ?>" name="description" required value="<?= esc($item['description']) ?>">
                                            </div>    
                                            <div class="mb-3">
                                                <label for="menuItemCategory-<?= $item['item_id'] ?>" class="form-label">Category</label>
                                                <select class="form-select" id="menuItemCategory-<?= $item['item_id'] ?>" name="category" required>
                                                    <option value="">Choose...</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= esc($category['name']) ?>" <?= $item['category'] == $category['name'] ? 'selected' : '' ?>>
                                                            <?= esc($category['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" form="editMenuItemForm-<?= $item['item_id'] ?>" class="btn btn-primary">Save Item</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $('.feature-checkbox').change(function() {
            var itemId = $(this).data('item-id');
            var isFeatured = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '<?= site_url('admin/menu_management/toggle_feature') ?>',  // Ensure this URL is correct
                type: 'POST',
                data: {
                    item_id: itemId,
                    is_featured: isFeatured
                },
                success: function(response) {
                    alert('Feature status updated successfully!');
                },
                error: function(xhr) {  // Added xhr to get more details on the error
                    console.log(xhr.responseText);  // Log the server's error message if available
                    alert('Error updating feature status. Please try again.');
                }
            });
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterElement = document.getElementById('categoryFilter');
        filterElement.addEventListener('change', function() {
            const selectedCategory = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('table tbody tr');
            tableRows.forEach(function(row) {
                const categoryCellText = row.cells[4].textContent.trim().toLowerCase(); // Adjusted for the 5th cell
                if (selectedCategory === '' || categoryCellText === selectedCategory) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    </script>


</body>
</html>
