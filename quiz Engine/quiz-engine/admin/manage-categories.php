<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$msg = "";
$error = "";

// Handle Add Category
if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            if ($conn->affected_rows > 0) {
                $msg = "Category added successfully.";
            } else {
                $error = "Category already exists.";
            }
        } else {
            $error = "Error adding category.";
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $msg = "Category deleted successfully.";
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories – Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="top-banner">Online Examination Portal – Admin</div>
    <nav class="navbar">
        <div class="logo">Admin Panel</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage-questions.php">Questions</a>
            <a href="manage-categories.php">Categories</a>
            <a href="manage-assignments.php">Assignments</a>
            <a href="manage-students.php">Students</a>
            <a href="view-results.php">Results</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            <!-- Add Category Form -->
            <div class="card">
                <h2>Add New Category</h2>
                <?php if ($msg) echo "<p class='pass'>$msg</p>"; ?>
                <?php if ($error) echo "<p class='fail'>$error</p>"; ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="category_name" placeholder="e.g. Cloud Computing" required>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary" style="width: 100%;">Create Category</button>
                </form>
            </div>

            <!-- Category List -->
            <div class="card">
                <h2>Existing Categories</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; while($row = $categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['category_name']); ?></strong></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="add-question.php?category=<?php echo urlencode($row['category_name']); ?>" 
                                       class="btn btn-primary" 
                                       style="padding: 5px 12px; font-size: 0.75rem;">+ Add Question</a>
                                    <a href="?delete=<?php echo $row['category_id']; ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 5px 12px; font-size: 0.75rem;"
                                       onclick="return confirm('Note: This will NOT delete questions in this category, but the category will no longer be suggested. Continue?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal</div>
</body>
</html>
