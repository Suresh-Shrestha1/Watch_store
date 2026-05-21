<?php
session_start();

// Block if not admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../brands.php');
    exit();
}

require_once '../../config/db.php';

// Get the action type from hidden input
$action = $_POST['action'] ?? '';

// ADD BRAND
if ($action === 'add') {

    $name = trim($_POST['name'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // --- Validation ---
    if (empty($name)) {
        $_SESSION['brand_error'] = 'Brand name is required.';
        header('Location: ../add-brand.php');
        exit();
    }

    // Create slug from name
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    // Check if brand name already exists
    $check = $conn->prepare("SELECT id FROM brands WHERE name = ? OR slug = ?");
    $check->bind_param("ss", $name, $slug);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['brand_error'] = 'A brand with this name already exists.';
        $_SESSION['brand_form'] = ['name' => $name, 'is_active' => $is_active];
        $check->close();
        header('Location: ../add-brand.php');
        exit();
    }
    $check->close();

    // --- Handle Logo Upload ---
    $logo = null;

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {

        $file = $_FILES['logo'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        $max_size = 2 * 1024 * 1024;

        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['brand_error'] = 'Logo must be JPG, PNG, WEBP, or SVG.';
            $_SESSION['brand_form'] = ['name' => $name, 'is_active' => $is_active];
            header('Location: ../add-brand.php');
            exit();
        }

        if ($file['size'] > $max_size) {
            $_SESSION['brand_error'] = 'Logo must be less than 2MB.';
            $_SESSION['brand_form'] = ['name' => $name, 'is_active' => $is_active];
            header('Location: ../add-brand.php');
            exit();
        }

        $upload_dir = '../../assets/uploads/brands/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = $slug . '-' . time() . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
            // DB stores relative path from project root
            $logo = 'assets/uploads/brands/' . $new_filename;
        } else {
            $_SESSION['brand_error'] = 'Failed to upload logo. Please try again.';
            $_SESSION['brand_form'] = ['name' => $name, 'is_active' => $is_active];
            header('Location: ../add-brand.php');
            exit();
        }
    }

    // --- Insert into Database ---
    $stmt = $conn->prepare("INSERT INTO brands (name, slug, logo, is_active) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $slug, $logo, $is_active);

    if ($stmt->execute()) {
        $_SESSION['brand_success'] = 'Brand added successfully.';
    } else {
        $_SESSION['brand_error'] = 'Failed to add brand. Please try again.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../brands.php');
    exit();
}

// EDIT BRAND
if ($action === 'edit') {

    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $existing_logo = $_POST['existing_logo'] ?? '';

    // --- Validation ---
    if ($id <= 0) {
        $_SESSION['brand_error'] = 'Invalid brand.';
        header('Location: ../brands.php');
        exit();
    }

    if (empty($name)) {
        $_SESSION['brand_error'] = 'Brand name is required.';
        header("Location: ../edit-brand.php?id=$id");
        exit();
    }

    // Create slug
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    // Check if name exists for ANOTHER brand (not current)
    $check = $conn->prepare("SELECT id FROM brands WHERE (name = ? OR slug = ?) AND id != ?");
    $check->bind_param("ssi", $name, $slug, $id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['brand_error'] = 'Another brand with this name already exists.';
        $check->close();
        header("Location: ../edit-brand.php?id=$id");
        exit();
    }
    $check->close();

    // --- Handle Logo Upload ---
    $logo = $existing_logo; // Keep old logo by default

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {

        $file = $_FILES['logo'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        $max_size = 2 * 1024 * 1024;

        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['brand_error'] = 'Logo must be JPG, PNG, WEBP, or SVG.';
            header("Location: ../edit-brand.php?id=$id");
            exit();
        }

        if ($file['size'] > $max_size) {
            $_SESSION['brand_error'] = 'Logo must be less than 2MB.';
            header("Location: ../edit-brand.php?id=$id");
            exit();
        }

        $upload_dir = '../../assets/uploads/brands/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = $slug . '-' . time() . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
            if (!empty($existing_logo) && file_exists('../../' . $existing_logo)) {
                unlink('../../' . $existing_logo);
            }

            $logo = 'assets/uploads/brands/' . $new_filename;

        } else {
            $_SESSION['brand_error'] = 'Failed to upload logo.';
            header("Location: ../edit-brand.php?id=$id");
            exit();
        }
    }

    // --- Update Database ---
    $stmt = $conn->prepare("UPDATE brands SET name = ?, slug = ?, logo = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("sssii", $name, $slug, $logo, $is_active, $id);

    if ($stmt->execute()) {
        $_SESSION['brand_success'] = 'Brand updated successfully.';
    } else {
        $_SESSION['brand_error'] = 'Failed to update brand.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../brands.php');
    exit();
}

// DELETE BRAND
if ($action === 'delete') {

    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['brand_error'] = 'Invalid brand.';
        header('Location: ../brands.php');
        exit();
    }

    // Check if brand has products
    $check = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE brand_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();
    $check->close();

    if ($row['total'] > 0) {
        $_SESSION['brand_error'] = 'Cannot delete brand. It has ' . $row['total'] . ' product(s) linked to it.';
        header('Location: ../brands.php');
        exit();
    }

    // Get logo path before deleting
    $logo_stmt = $conn->prepare("SELECT logo FROM brands WHERE id = ?");
    $logo_stmt->bind_param("i", $id);
    $logo_stmt->execute();
    $logo_result = $logo_stmt->get_result();
    $brand_data = $logo_result->fetch_assoc();
    $logo_stmt->close();

    // Delete brand from database
    $stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if (!empty($brand_data['logo']) && file_exists('../../' . $brand_data['logo'])) {
            unlink('../../' . $brand_data['logo']);
        }

        $_SESSION['brand_success'] = 'Brand deleted successfully.';
    } else {
        $_SESSION['brand_error'] = 'Failed to delete brand.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../brands.php');
    exit();
}

// TOGGLE STATUS (Active / Inactive)
if ($action === 'toggle_status') {

    $id = intval($_POST['id'] ?? 0);
    $current_status = intval($_POST['current_status'] ?? 0);

    if ($id <= 0) {
        $_SESSION['brand_error'] = 'Invalid brand.';
        header('Location: ../brands.php');
        exit();
    }

    // Flip: 1 → 0 or 0 → 1
    $new_status = ($current_status === 1) ? 0 : 1;

    $stmt = $conn->prepare("UPDATE brands SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $id);

    if ($stmt->execute()) {
        $status_text = ($new_status === 1) ? 'activated' : 'deactivated';
        $_SESSION['brand_success'] = 'Brand ' . $status_text . ' successfully.';
    } else {
        $_SESSION['brand_error'] = 'Failed to update status.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../brands.php');
    exit();
}

// If action doesn't match anything above
header('Location: ../brands.php');
exit();
?>