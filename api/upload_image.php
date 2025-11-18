<?php
// Simple image upload helper for products
// Usage: $res = upload_product_image($_FILES['image']); if ($res['success']) $filename = $res['filename'];

function upload_product_image($file, $uploadDir = null) {
    // ... set default upload directory to project images folder ...
    if ($uploadDir === null) {
        $uploadDir = __DIR__ . '/../images/';
    }

    // normalize path
    $uploadDir = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    // basic checks
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
    }

    // validate size (5 MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large (max 5MB)'];
    }

    // validate mime / extension
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        'image/webp' => '.webp'
    ];

    if (!isset($allowed[$mime])) {
        return ['success' => false, 'error' => 'Unsupported image type'];
    }

    // ensure directory exists
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        return ['success' => false, 'error' => 'Failed to create images directory'];
    }

    // generate unique filename
    $ext = $allowed[$mime];
    $safeName = bin2hex(random_bytes(8)) . '_' . time() . $ext;
    $dest = $uploadDir . $safeName;

    // move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }

    // optionally set permissions
    @chmod($dest, 0644);

    // return relative filename for DB (images/filename.ext)
    return ['success' => true, 'filename' => 'images/' . $safeName];
}
?>
