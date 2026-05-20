<?php
require_once 'auth_check.php';

// Get current user data
$user = getCurrentUser();

$package_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($package_id <= 0) {
    $_SESSION['error'] = 'Invalid package selected.';
    header('Location: packages.php');
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare('SELECT * FROM packages WHERE id = ?');
$stmt->execute([$package_id]);
$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    $_SESSION['error'] = 'Package not found.';
    header('Location: packages.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $image_url = $package['image_url'];

    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($destination)) {
        $errors[] = 'Destination is required.';
    }
    if (empty($description)) {
        $errors[] = 'Description is required.';
    }
    if ($duration <= 0) {
        $errors[] = 'Duration must be greater than 0.';
    }
    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0.';
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024;

            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $errors[] = 'Invalid image type. Please upload JPG, PNG, or GIF.';
            } elseif ($_FILES['image']['size'] > $max_size) {
                $errors[] = 'Image size must be less than 5MB.';
            } else {
                $upload_dir = '../uploads/packages/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $target_path = $upload_dir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    if (!empty($image_url) && file_exists('../' . $image_url) && strpos($image_url, 'uploads/packages/') === 0) {
                        @unlink('../' . $image_url);
                    }
                    $image_url = 'uploads/packages/' . $filename;
                } else {
                    $errors[] = 'Failed to upload image.';
                }
            }
        } else {
            $errors[] = 'Error uploading image.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare(
                'UPDATE packages SET title = ?, destination = ?, description = ?, duration = ?, price = ?, image_url = ?, featured = ? WHERE id = ?'
            );
            $stmt->execute([$title, $destination, $description, $duration, $price, $image_url, $featured, $package_id]);

            $_SESSION['success'] = 'Package updated successfully.';
            header('Location: packages.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to update package.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - WonderEase Travel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .image-preview {
            width: 200px;
            height: 200px;
            overflow: hidden;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="user-info">
                <h3>Admin Panel</h3>
                <p><?php echo htmlspecialchars($user['name']); ?></p>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Manage Bookings</a></li>
                    <li><a href="packages.php" class="active"><i class="fas fa-suitcase"></i> Manage Packages</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="support.php"><i class="fas fa-headset"></i> Support Messages</a></li>
                    <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <header>
                <div class="header-content">
                    <h1>Edit Package</h1>
                    <a href="packages.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Packages
                    </a>
                </div>
            </header>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Package Title</label>
                        <input type="text" id="title" name="title" required
                               value="<?php echo htmlspecialchars($_POST['title'] ?? $package['title']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="destination">Destination</label>
                        <input type="text" id="destination" name="destination" required
                               value="<?php echo htmlspecialchars($_POST['destination'] ?? $package['destination']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? $package['description']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="duration">Duration (days)</label>
                        <input type="number" id="duration" name="duration" min="1" required
                               value="<?php echo htmlspecialchars($_POST['duration'] ?? $package['duration']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required
                               value="<?php echo htmlspecialchars($_POST['price'] ?? $package['price']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Package Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <?php if (!empty($package['image_url'])): ?>
                        <div class="image-preview" style="height: 200px; width: 200px;">
                            <p>Current image:</p>
                            <img src="<?php echo htmlspecialchars('../' . $package['image_url']); ?>" alt="Current package image" onerror="this.src='../assets/images/default-package.jpg';">
                        </div>
                    <?php endif; ?>
                    <small>Upload a new image only if you want to replace the current one.</small>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="featured" value="1"
                               <?php echo isset($_POST['featured']) || $package['featured'] ? 'checked' : ''; ?>>
                        Feature this package on the homepage
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Package
                    </button>
                    <a href="packages.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
