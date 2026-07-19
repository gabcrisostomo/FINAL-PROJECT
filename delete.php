<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get product info before deleting
    $fetch = mysqli_query($conn, "SELECT name, image FROM products WHERE id = '$id'");
    if (mysqli_num_rows($fetch) === 1) {
        $prod = mysqli_fetch_assoc($fetch);
        $prod_name = $prod['name'];
        $image_path = $prod['image'];
        
        // Delete image file if exists
        if (!empty($image_path) && $image_path != 'uploads/default.jpg' && file_exists($image_path)) {
            unlink($image_path);
        }
        
        // Delete from database
        $delete = mysqli_query($conn, "DELETE FROM products WHERE id = '$id'");
        
        if ($delete) {
            $activity = "Deleted product: '$prod_name' (ID: $id)";
            $stmt = mysqli_prepare($conn, "INSERT INTO audit_logs (user_id, user_name, activity) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iss", $_SESSION['user_id'], $_SESSION['name'], $activity);
            mysqli_stmt_execute($stmt);
            
            echo "<!DOCTYPE html>
            <html>
            <head>
                <title>Deleted — THREAD & TREND</title>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'>
                <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700;800&display=swap' rel='stylesheet'>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body {
                        font-family: 'Inter', sans-serif;
                        background: #f8f8f6;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .success-box {
                        background: white;
                        padding: 3rem;
                        border-radius: 32px;
                        text-align: center;
                        max-width: 420px;
                        box-shadow: 0 40px 80px rgba(0,0,0,0.06);
                        border: 1px solid rgba(0,0,0,0.03);
                    }
                    .success-box .icon {
                        width: 64px;
                        height: 64px;
                        border-radius: 50%;
                        background: rgba(34,197,94,0.1);
                        color: #22c55e;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 2rem;
                        margin: 0 auto 1rem;
                    }
                    .success-box h2 {
                        font-family: 'Playfair Display', serif;
                        font-size: 1.5rem;
                        font-weight: 700;
                    }
                    .success-box p {
                        color: #8a8a86;
                        margin-top: 0.5rem;
                        font-size: 0.9rem;
                    }
                    .success-box .product-name {
                        font-weight: 600;
                        color: #0a0a0a;
                    }
                    .success-box a {
                        display: inline-block;
                        margin-top: 1.5rem;
                        background: #0a0a0a;
                        color: white;
                        padding: 0.75rem 2rem;
                        border-radius: 100px;
                        text-decoration: none;
                        font-weight: 600;
                        font-size: 0.75rem;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                        transition: all 0.3s;
                    }
                    .success-box a:hover {
                        background: #2a2a26;
                        transform: translateY(-2px);
                        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
                    }
                </style>
            </head>
            <body>
                <div class='success-box'>
                    <div class='icon'><i class='fas fa-check'></i></div>
                    <h2>Product Deleted</h2>
                    <p><span class='product-name'>" . htmlspecialchars($prod_name) . "</span> has been permanently removed.</p>
                    <a href='dashboard.php'>Return to Dashboard</a>
                </div>
            </body>
            </html>";
            exit();
        }
    }
}

header("Location: dashboard.php");
exit();
?>