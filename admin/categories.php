<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
}  
?>
<!doctype html>
  <html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="../styles/lesserafim.css">
    <style>
      body {
        font-family: "Arial";
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="display-4 text-center">Category Management <span class="text-success"><?php echo $_SESSION['username']; ?></span></div>
      
      <!-- Add Category Form -->
      <div class="row justify-content-center mt-4">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h4>Add New Category</h4>
            </div>
            <div class="card-body">
              <form action="core/handleForms.php" method="POST">
                <div class="form-group">
                  <label for="category_name">Category Name</label>
                  <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Enter category name" required>
                </div>
                <div class="form-group">
                  <label for="category_description">Description (Optional)</label>
                  <textarea class="form-control" name="category_description" id="category_description" placeholder="Enter category description"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="addCategoryBtn">Add Category</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Categories List -->
      <div class="row justify-content-center mt-4">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h4>Existing Categories</h4>
            </div>
            <div class="card-body">
              <?php 
              $categories = $categoryObj->getCategories();
              if (empty($categories)): ?>
                <p class="text-muted">No categories found.</p>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($categories as $category): ?>
                        <tr>
                          <td><?php echo $category['category_id']; ?></td>
                          <td><?php echo htmlspecialchars($category['name']); ?></td>
                          <td><?php echo htmlspecialchars($category['description']); ?></td>
                          <td><?php echo $category['created_at']; ?></td>
                          <td>
                            <button class="btn btn-sm btn-warning" onclick="editCategory(<?php echo $category['category_id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>', '<?php echo htmlspecialchars($category['description']); ?>')">Edit</button>
                            <form action="core/handleForms.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?')">
                              <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                              <button type="submit" class="btn btn-sm btn-danger" name="deleteCategoryBtn">Delete</button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Category Modal -->
      <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Category</h5>
              <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <form action="core/handleForms.php" method="POST">
              <div class="modal-body">
                <input type="hidden" name="edit_category_id" id="edit_category_id">
                <div class="form-group">
                  <label for="edit_category_name">Category Name</label>
                  <input type="text" class="form-control" name="edit_category_name" id="edit_category_name" required>
                </div>
                <div class="form-group">
                  <label for="edit_category_description">Description</label>
                  <textarea class="form-control" name="edit_category_description" id="edit_category_description"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" name="updateCategoryBtn">Update Category</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
      function editCategory(id, name, description) {
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = name;
        document.getElementById('edit_category_description').value = description;
        $('#editCategoryModal').modal('show');
      }
    </script>
  </body>
</html>
