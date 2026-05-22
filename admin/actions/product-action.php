<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../products.php');
    exit();
}

require_once '../../config/db.php';

$action = $_POST['action'] ?? '';


// ADD PRODUCT
if ($action === 'add') {

    // Get all form data
    $model_number     = trim($_POST['model_number'] ?? '');
    $name             = trim($_POST['name'] ?? '');
    $brand_id         = intval($_POST['brand_id'] ?? 0);
    $gender           = trim($_POST['gender'] ?? '');
    $price            = floatval($_POST['price'] ?? 0);
    $stock_quantity   = intval($_POST['stock_quantity'] ?? 0);
    $movement_type    = trim($_POST['movement_type'] ?? '-');
    $dial_shape       = trim($_POST['dial_shape'] ?? 'Round');
    $dial_color       = trim($_POST['dial_color'] ?? '');
    $case_diameter_mm = trim($_POST['case_diameter_mm'] ?? '');
    $case_material    = trim($_POST['case_material'] ?? '');
    $strap_material   = trim($_POST['strap_material'] ?? '');
    $strap_color      = trim($_POST['strap_color'] ?? '');
    $strap_adjustable = isset($_POST['strap_adjustable']) ? 1 : 0;
    $strap_length_mm  = trim($_POST['strap_length_mm'] ?? '');
    $strap_size_options = trim($_POST['strap_size_options'] ?? '');
    $water_resistance = trim($_POST['water_resistance'] ?? '');
    $features         = trim($_POST['features'] ?? '');
    $description      = trim($_POST['description'] ?? '');
    $warranty_years   = intval($_POST['warranty_years'] ?? 2);
    $is_expensive     = isset($_POST['is_expensive']) ? 1 : 0;
    $is_active        = isset($_POST['is_active']) ? 1 : 0;

    // --- Validation ---
    if (empty($model_number) || empty($name) || $brand_id <= 0 || empty($gender) || $price <= 0) {
        $_SESSION['product_error'] = 'Please fill in all required fields (Model Number, Name, Brand, Gender, Price).';
        $_SESSION['product_form'] = $_POST;
        header('Location: ../add-product.php');
        exit();
    }

    // Create slug from name
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    // Check if model number or slug already exists
    $check = $conn->prepare("SELECT id FROM products WHERE model_number = ? OR slug = ?");
    $check->bind_param("ss", $model_number, $slug);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['product_error'] = 'A product with this model number or name already exists.';
        $_SESSION['product_form'] = $_POST;
        $check->close();
        header('Location: ../add-product.php');
        exit();
    }
    $check->close();

    // Handle empty optional fields — set to null
    $case_diameter_mm = !empty($case_diameter_mm) ? floatval($case_diameter_mm) : null;
    $strap_length_mm  = !empty($strap_length_mm) ? floatval($strap_length_mm) : null;
    $dial_color       = !empty($dial_color) ? $dial_color : null;
    $case_material    = !empty($case_material) ? $case_material : null;
    $strap_material   = !empty($strap_material) ? $strap_material : null;
    $strap_color      = !empty($strap_color) ? $strap_color : null;
    $strap_size_options = !empty($strap_size_options) ? $strap_size_options : null;
    $water_resistance = !empty($water_resistance) ? $water_resistance : null;
    $features         = !empty($features) ? $features : null;
    $description      = !empty($description) ? $description : null;

    // --- Insert Product ---
    $stmt = $conn->prepare("
        INSERT INTO products (
            model_number, name, slug, brand_id, gender,
            strap_material, strap_color, strap_adjustable, strap_length_mm, strap_size_options,
            dial_shape, dial_color, case_diameter_mm, case_material,
            water_resistance, movement_type, features,
            price, is_expensive, stock_quantity, is_active,
            description, warranty_years
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssisssidsssdssssdiiisi",
        $model_number, $name, $slug, $brand_id, $gender,
        $strap_material, $strap_color, $strap_adjustable, $strap_length_mm, $strap_size_options,
        $dial_shape, $dial_color, $case_diameter_mm, $case_material,
        $water_resistance, $movement_type, $features,
        $price, $is_expensive, $stock_quantity, $is_active,
        $description, $warranty_years
    );

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id; // Get the ID of newly inserted product
        $stmt->close();

        // --- Handle Image Uploads ---
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {

            $upload_dir = '../assets/uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB per image
            $main_image_set = false;

            // Loop through each uploaded file
            // $_FILES['images']['name'] is an array when input has multiple attribute
            for ($i = 0; $i < count($_FILES['images']['name']); $i++) {

                // Skip if no file in this slot
                if ($_FILES['images']['error'][$i] !== 0) continue;

                $file_type = $_FILES['images']['type'][$i];
                $file_size = $_FILES['images']['size'][$i];
                $file_tmp  = $_FILES['images']['tmp_name'][$i];
                $file_name = $_FILES['images']['name'][$i];

                // Validate type
                if (!in_array($file_type, $allowed_types)) continue;

                // Validate size
                if ($file_size > $max_size) continue;

                // Create unique filename
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_filename = $slug . '-' . time() . '-' . $i . '.' . $extension;

                // Move file
                if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {

                    $image_url = 'uploads/products/' . $new_filename;

                    // First image becomes the main image
                    $is_main = (!$main_image_set) ? 1 : 0;
                    if ($is_main) $main_image_set = true;

                    $sort_order = $i;

                    // Insert image record into database
                    $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url, is_main, sort_order) VALUES (?, ?, ?, ?)");
                    $img_stmt->bind_param("isii", $product_id, $image_url, $is_main, $sort_order);
                    $img_stmt->execute();
                    $img_stmt->close();
                }
            }
        }

        $_SESSION['product_success'] = 'Product added successfully.';
    } else {
        $_SESSION['product_error'] = 'Failed to add product. Please try again.';
        $_SESSION['product_form'] = $_POST;
        $stmt->close();
        $conn->close();
        header('Location: ../add-product.php');
        exit();
    }

    $conn->close();
    header('Location: ../products.php');
    exit();
}


// EDIT PRODUCT
if ($action === 'edit') {

    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['product_error'] = 'Invalid product.';
        header('Location: ../products.php');
        exit();
    }

    // Get form data
    $model_number     = trim($_POST['model_number'] ?? '');
    $name             = trim($_POST['name'] ?? '');
    $brand_id         = intval($_POST['brand_id'] ?? 0);
    $gender           = trim($_POST['gender'] ?? '');
    $price            = floatval($_POST['price'] ?? 0);
    $stock_quantity   = intval($_POST['stock_quantity'] ?? 0);
    $movement_type    = trim($_POST['movement_type'] ?? '-');
    $dial_shape       = trim($_POST['dial_shape'] ?? 'Round');
    $dial_color       = trim($_POST['dial_color'] ?? '');
    $case_diameter_mm = trim($_POST['case_diameter_mm'] ?? '');
    $case_material    = trim($_POST['case_material'] ?? '');
    $strap_material   = trim($_POST['strap_material'] ?? '');
    $strap_color      = trim($_POST['strap_color'] ?? '');
    $strap_adjustable = isset($_POST['strap_adjustable']) ? 1 : 0;
    $strap_length_mm  = trim($_POST['strap_length_mm'] ?? '');
    $strap_size_options = trim($_POST['strap_size_options'] ?? '');
    $water_resistance = trim($_POST['water_resistance'] ?? '');
    $features         = trim($_POST['features'] ?? '');
    $description      = trim($_POST['description'] ?? '');
    $warranty_years   = intval($_POST['warranty_years'] ?? 2);
    $is_expensive     = isset($_POST['is_expensive']) ? 1 : 0;
    $is_active        = isset($_POST['is_active']) ? 1 : 0;

    // Validation
    if (empty($model_number) || empty($name) || $brand_id <= 0 || empty($gender) || $price <= 0) {
        $_SESSION['product_error'] = 'Please fill in all required fields.';
        header("Location: ../edit-product.php?id=$id");
        exit();
    }

    // Create slug
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    // Check duplicate (exclude current product)
    $check = $conn->prepare("SELECT id FROM products WHERE (model_number = ? OR slug = ?) AND id != ?");
    $check->bind_param("ssi", $model_number, $slug, $id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['product_error'] = 'Another product with this model number or name already exists.';
        $check->close();
        header("Location: ../edit-product.php?id=$id");
        exit();
    }
    $check->close();

    // Handle optional fields
    $case_diameter_mm = !empty($case_diameter_mm) ? floatval($case_diameter_mm) : null;
    $strap_length_mm  = !empty($strap_length_mm) ? floatval($strap_length_mm) : null;
    $dial_color       = !empty($dial_color) ? $dial_color : null;
    $case_material    = !empty($case_material) ? $case_material : null;
    $strap_material   = !empty($strap_material) ? $strap_material : null;
    $strap_color      = !empty($strap_color) ? $strap_color : null;
    $strap_size_options = !empty($strap_size_options) ? $strap_size_options : null;
    $water_resistance = !empty($water_resistance) ? $water_resistance : null;
    $features         = !empty($features) ? $features : null;
    $description      = !empty($description) ? $description : null;

    // Update product
    $stmt = $conn->prepare("
        UPDATE products SET
            model_number = ?, name = ?, slug = ?, brand_id = ?, gender = ?,
            strap_material = ?, strap_color = ?, strap_adjustable = ?, strap_length_mm = ?, strap_size_options = ?,
            dial_shape = ?, dial_color = ?, case_diameter_mm = ?, case_material = ?,
            water_resistance = ?, movement_type = ?, features = ?,
            price = ?, is_expensive = ?, stock_quantity = ?, is_active = ?,
            description = ?, warranty_years = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssisssidsssdssssdiiisii",
        $model_number, $name, $slug, $brand_id, $gender,
        $strap_material, $strap_color, $strap_adjustable, $strap_length_mm, $strap_size_options,
        $dial_shape, $dial_color, $case_diameter_mm, $case_material,
        $water_resistance, $movement_type, $features,
        $price, $is_expensive, $stock_quantity, $is_active,
        $description, $warranty_years,
        $id
    );

    if ($stmt->execute()) {

        // --- Handle New Image Uploads ---
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {

            $upload_dir = '../assets/uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024;

            // Check if product already has a main image
            $main_check = $conn->prepare("SELECT id FROM product_images WHERE product_id = ? AND is_main = 1");
            $main_check->bind_param("i", $id);
            $main_check->execute();
            $has_main = $main_check->get_result()->num_rows > 0;
            $main_check->close();

            // Get current max sort order
            $sort_check = $conn->prepare("SELECT MAX(sort_order) as max_sort FROM product_images WHERE product_id = ?");
            $sort_check->bind_param("i", $id);
            $sort_check->execute();
            $sort_row = $sort_check->get_result()->fetch_assoc();
            $current_sort = ($sort_row['max_sort'] !== null) ? $sort_row['max_sort'] + 1 : 0;
            $sort_check->close();

            for ($i = 0; $i < count($_FILES['images']['name']); $i++) {

                if ($_FILES['images']['error'][$i] !== 0) continue;

                $file_type = $_FILES['images']['type'][$i];
                $file_size = $_FILES['images']['size'][$i];
                $file_tmp  = $_FILES['images']['tmp_name'][$i];
                $file_name = $_FILES['images']['name'][$i];

                if (!in_array($file_type, $allowed_types)) continue;
                if ($file_size > $max_size) continue;

                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_filename = $slug . '-' . time() . '-' . $i . '.' . $extension;

                if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {

                    $image_url = 'uploads/products/' . $new_filename;
                    $is_main = (!$has_main && $i === 0) ? 1 : 0;
                    if ($is_main) $has_main = true;
                    $sort_order = $current_sort + $i;

                    $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url, is_main, sort_order) VALUES (?, ?, ?, ?)");
                    $img_stmt->bind_param("isii", $id, $image_url, $is_main, $sort_order);
                    $img_stmt->execute();
                    $img_stmt->close();
                }
            }
        }

        $_SESSION['product_success'] = 'Product updated successfully.';
    } else {
        $_SESSION['product_error'] = 'Failed to update product.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../products.php');
    exit();
}


// DELETE PRODUCT
if ($action === 'delete') {

    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['product_error'] = 'Invalid product.';
        header('Location: ../products.php');
        exit();
    }

    // Check if product has orders
    $check = $conn->prepare("SELECT COUNT(*) as total FROM order_items WHERE product_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $row = $check->get_result()->fetch_assoc();
    $check->close();

    if ($row['total'] > 0) {
        $_SESSION['product_error'] = 'Cannot delete product. It has ' . $row['total'] . ' order(s) linked to it.';
        header('Location: ../products.php');
        exit();
    }

    // Get all images for this product before deleting
    $img_result = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $img_result->bind_param("i", $id);
    $img_result->execute();
    $images = $img_result->get_result();
    $image_paths = [];
    while ($img = $images->fetch_assoc()) {
        $image_paths[] = $img['image_url'];
    }
    $img_result->close();

    // Delete product (product_images will cascade delete)
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Delete image files from server
        foreach ($image_paths as $path) {
            $full_path = '../assets/' . $path;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }
        $_SESSION['product_success'] = 'Product deleted successfully.';
    } else {
        $_SESSION['product_error'] = 'Failed to delete product.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../products.php');
    exit();
}


// DELETE SINGLE IMAGE
if ($action === 'delete_image') {

    $image_id   = intval($_POST['image_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($image_id <= 0 || $product_id <= 0) {
        $_SESSION['product_error'] = 'Invalid image.';
        header("Location: ../edit-product.php?id=$product_id");
        exit();
    }

    // Get image path
    $stmt = $conn->prepare("SELECT image_url, is_main FROM product_images WHERE id = ? AND product_id = ?");
    $stmt->bind_param("ii", $image_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $image = $result->fetch_assoc();
        $stmt->close();

        // Delete from database
        $del = $conn->prepare("DELETE FROM product_images WHERE id = ?");
        $del->bind_param("i", $image_id);
        $del->execute();
        $del->close();

        // Delete file from server
        $full_path = '../assets/' . $image['image_url'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }

        // If deleted image was main, make the first remaining image the main
        if ($image['is_main'] == 1) {
            $next = $conn->prepare("SELECT id FROM product_images WHERE product_id = ? ORDER BY sort_order ASC LIMIT 1");
            $next->bind_param("i", $product_id);
            $next->execute();
            $next_result = $next->get_result();
            if ($next_result->num_rows > 0) {
                $next_img = $next_result->fetch_assoc();
                $update_main = $conn->prepare("UPDATE product_images SET is_main = 1 WHERE id = ?");
                $update_main->bind_param("i", $next_img['id']);
                $update_main->execute();
                $update_main->close();
            }
            $next->close();
        }

        $_SESSION['product_success'] = 'Image deleted.';
    } else {
        $_SESSION['product_error'] = 'Image not found.';
        $stmt->close();
    }

    $conn->close();
    header("Location: ../edit-product.php?id=$product_id");
    exit();
}


// SET MAIN IMAGE
if ($action === 'set_main_image') {

    $image_id   = intval($_POST['image_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($image_id <= 0 || $product_id <= 0) {
        $_SESSION['product_error'] = 'Invalid image.';
        header("Location: ../edit-product.php?id=$product_id");
        exit();
    }

    // Remove main from all images of this product
    $reset = $conn->prepare("UPDATE product_images SET is_main = 0 WHERE product_id = ?");
    $reset->bind_param("i", $product_id);
    $reset->execute();
    $reset->close();

    // Set selected image as main
    $set = $conn->prepare("UPDATE product_images SET is_main = 1 WHERE id = ? AND product_id = ?");
    $set->bind_param("ii", $image_id, $product_id);
    $set->execute();
    $set->close();

    $_SESSION['product_success'] = 'Main image updated.';
    $conn->close();
    header("Location: ../edit-product.php?id=$product_id");
    exit();
}


// TOGGLE STATUS
if ($action === 'toggle_status') {

    $id = intval($_POST['id'] ?? 0);
    $current_status = intval($_POST['current_status'] ?? 0);

    if ($id <= 0) {
        $_SESSION['product_error'] = 'Invalid product.';
        header('Location: ../products.php');
        exit();
    }

    $new_status = ($current_status === 1) ? 0 : 1;

    $stmt = $conn->prepare("UPDATE products SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $id);

    if ($stmt->execute()) {
        $status_text = ($new_status === 1) ? 'activated' : 'deactivated';
        $_SESSION['product_success'] = 'Product ' . $status_text . ' successfully.';
    } else {
        $_SESSION['product_error'] = 'Failed to update status.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../products.php');
    exit();
}


// No matching action
header('Location: ../products.php');
exit();
?>