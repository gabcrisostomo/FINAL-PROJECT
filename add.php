<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads/')) {
    mkdir('uploads/', 0777, true);
}

if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_path = '';
    
    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['product_image']['tmp_name'];
        $original_name = $_FILES['product_image']['name'];
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $extension;
        $destination = 'uploads/' . $new_filename;
        
        // Validate image
        $image_info = getimagesize($tmp_name);
        if ($image_info !== false) {
            if (move_uploaded_file($tmp_name, $destination)) {
                $image_path = $destination;
            }
        }
    }
    
    // If no image uploaded, use default
    if (empty($image_path)) {
        $image_path = 'uploads/default.jpg';
    }

    $insert = mysqli_query($conn, "INSERT INTO products (name, category, price, stock, image) 
                                   VALUES ('$name', '$category', '$price', '$stock', '$image_path')");
    
    if ($insert) {
        $activity = "Added new product: '$name' in category '$category'";
        $stmt = mysqli_prepare($conn, "INSERT INTO audit_logs (user_id, user_name, activity) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $_SESSION['user_id'], $_SESSION['name'], $activity);
        mysqli_stmt_execute($stmt);

        echo "<script>alert('Product added successfully!'); window.location='dashboard.php';</script>";
        exit();
    } else {
        $error = "Failed to add product. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product — THREAD & TREND</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --black: #0a0a0a;
            --gray-50: #f8f8f6;
            --gray-300: #d4d4d0;
            --gray-400: #a8a8a4;
            --gray-500: #8a8a86;
            --indigo: #6366f1;
            --red: #ef4444;
            --transition: cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-lg: 0 24px 64px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--black);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .form-container {
            max-width: 560px;
            width: 100%;
            background: white;
            border-radius: 32px;
            padding: 3rem 2.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(0,0,0,0.03);
        }
        @media (max-width: 480px) { .form-container { padding: 2rem 1.5rem; } }

        .form-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--black);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.25rem;
        }
        .form-brand .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--indigo); }
        .form-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; letter-spacing: -0.02em; }
        .form-subtitle { color: var(--gray-400); font-size: 0.85rem; margin-top: 0.25rem; }

        .input-group { margin-top: 1.25rem; }
        .input-group label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            margin-bottom: 0.4rem;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 0.9rem 1.25rem;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 16px;
            font-size: 0.95rem;
            transition: all 0.3s var(--transition);
            background: var(--gray-50);
            font-family: 'Inter', sans-serif;
            outline: none;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.08);
            background: white;
        }
        .input-group select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236a6a66' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1.25rem center; padding-right: 2.5rem; cursor: pointer; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 480px) { .grid-2 { grid-template-columns: 1fr; } }

        .upload-area {
            border: 2px dashed rgba(0,0,0,0.08);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            background: var(--gray-50);
            transition: all 0.3s var(--transition);
            cursor: pointer;
            position: relative;
        }
        .upload-area:hover { border-color: var(--indigo); background: rgba(99,102,241,0.02); }
        .upload-area .icon { font-size: 2rem; color: var(--gray-300); display: block; margin-bottom: 0.5rem; }
        .upload-area .title { font-weight: 500; font-size: 0.85rem; color: var(--gray-600); }
        .upload-area .subtitle { font-size: 0.7rem; color: var(--gray-400); }
        .upload-area input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }

        .image-preview {
            margin-top: 1rem;
            display: none;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1/1;
            max-height: 200px;
            background: var(--gray-50);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .image-preview img { width: 100%; height: 100%; object-fit: contain; }
        .image-preview .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .image-preview .remove-btn:hover { background: var(--red); }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--black);
            color: white;
            border: none;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            transition: all 0.4s var(--transition);
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .btn-submit:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }

        .btn-cancel {
            width: 100%;
            padding: 1rem;
            background: transparent;
            color: var(--gray-500);
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            transition: all 0.4s var(--transition);
            margin-top: 0.75rem;
            text-decoration: none;
            text-align: center;
            display: block;
        }
        .btn-cancel:hover { border-color: var(--black); color: var(--black); }

        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-top: 1rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.25rem;
            font-size: 0.7rem;
            color: var(--gray-400);
            text-decoration: none;
            transition: all 0.3s var(--transition);
        }
        .back-link:hover { color: var(--black); gap: 0.75rem; }

        .required { color: var(--red); font-weight: 700; margin-left: 0.2rem; }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s var(--transition) forwards;
        }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        .fade-up-d1 { animation-delay: 0.05s; }
        .fade-up-d2 { animation-delay: 0.1s; }
        .fade-up-d3 { animation-delay: 0.15s; }
        .fade-up-d4 { animation-delay: 0.2s; }
        .fade-up-d5 { animation-delay: 0.25s; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="form-brand fade-up fade-up-d1"><span class="dot"></span> THREAD & TREND</div>
    <h1 class="form-title fade-up fade-up-d1">Add New Product</h1>
    <p class="form-subtitle fade-up fade-up-d1">Create a new product for your catalog.</p>

    <?php if (isset($error)): ?>
        <div class="error-msg fade-up fade-up-d1">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="input-group fade-up fade-up-d2">
            <label>Product Name <span class="required">*</span></label>
            <input type="text" name="name" required placeholder="e.g. Classic Oxford Button-Down" autofocus>
        </div>

        <div class="input-group fade-up fade-up-d3">
            <label>Category <span class="required">*</span></label>
            <select name="category" required>
                <option value="">Select a category</option>
                <option value="Shirts">Shirts</option>
                <option value="Outerwear">Outerwear</option>
                <option value="Hoodie">Hoodie</option>
                <option value="Pants">Pants</option>
                <option value="Accessories">Accessories</option>
            </select>
        </div>

        <div class="grid-2">
            <div class="input-group fade-up fade-up-d4">
                <label>Price (₱) <span class="required">*</span></label>
                <input type="number" step="0.01" name="price" required placeholder="0.00">
            </div>
            <div class="input-group fade-up fade-up-d4">
                <label>Stock Quantity <span class="required">*</span></label>
                <input type="number" name="stock" required placeholder="0">
            </div>
        </div>

        <div class="input-group fade-up fade-up-d5">
            <label>Product Image</label>
            <div class="upload-area" id="dropZone">
                <i class="fas fa-cloud-upload-alt icon"></i>
                <div class="title">Click to upload product image</div>
                <div class="subtitle">JPG, PNG, WEBP • Max 5MB</div>
                <input type="file" name="product_image" id="fileInput" accept="image/*">
            </div>
            <div class="image-preview" id="imagePreview">
                <img id="previewImg" src="" alt="Product preview">
                <button class="remove-btn" onclick="removeImage()"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <button type="submit" name="add_product" class="btn-submit fade-up" style="animation-delay:0.3s;">
            <i class="fas fa-save"></i> Save Product
        </button>
        <a href="dashboard.php" class="btn-cancel fade-up" style="animation-delay:0.35s;">
            <i class="fas fa-times"></i> Cancel
        </a>
    </form>

    <a href="dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Return to Dashboard
    </a>
</div>

<script>
// Image preview
const fileInput = document.getElementById('fileInput');
const preview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');

fileInput.addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    fileInput.value = '';
    preview.style.display = 'none';
    previewImg.src = '';
}

// Drag and drop
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--indigo)';
    this.style.background = 'rgba(99,102,241,0.02)';
});
dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = 'rgba(0,0,0,0.08)';
    this.style.background = 'var(--gray-50)';
});
dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = 'rgba(0,0,0,0.08)';
    this.style.background = 'var(--gray-50)';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
});

// Keyboard shortcut
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.location.href = 'dashboard.php';
    }
});
</script>

</body>
</html>