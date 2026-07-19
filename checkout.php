<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
$cart_products = [];

if (!empty($cart)) {
    $product_ids = implode(',', array_map('intval', array_keys($cart)));
    $products_query = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($product_ids)");
    while ($row = mysqli_fetch_assoc($products_query)) {
        $row['qty'] = $cart[$row['id']];
        $row['total_price'] = $row['price'] * $row['qty'];
        $subtotal += $row['total_price'];
        $cart_products[] = $row;
    }
}

if (isset($_POST['place_order'])) {
    if (empty($cart_products)) {
        $error_msg = "Your cart is empty.";
    } else {
        $stock_ok = true;
        foreach ($cart_products as $item) {
            if ($item['stock'] < $item['qty']) {
                $stock_ok = false;
                $error_msg = "Insufficient stock for: " . htmlspecialchars($item['name']);
                break;
            }
        }

        if ($stock_ok) {
            mysqli_begin_transaction($conn);
            try {
                foreach ($cart_products as $item) {
                    $new_stock = $item['stock'] - $item['qty'];
                    mysqli_query($conn, "UPDATE products SET stock = '$new_stock' WHERE id = '{$item['id']}'");
                }

                $activity = "Placed transaction order totaling PHP " . number_format($subtotal, 2);
                $stmt = mysqli_prepare($conn, "INSERT INTO audit_logs (user_id, user_name, activity) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iss", $user_id, $user['name'], $activity);
                mysqli_stmt_execute($stmt);

                mysqli_commit($conn);
                header("Location: payment.php");
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_msg = "Order processing failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — THREAD & TREND</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --black: #0a0a0a;
            --gray-50: #f8f8f6;
            --gray-100: #f0f0ee;
            --gray-300: #d4d4d0;
            --gray-400: #a8a8a4;
            --gray-500: #8a8a86;
            --gray-600: #6a6a66;
            --indigo: #6366f1;
            --transition: cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--black);
            -webkit-font-smoothing: antialiased;
        }
        .container-luxury { max-width: 1200px; margin: 0 auto; padding: 0 2.5rem; }
        @media (max-width: 640px) { .container-luxury { padding: 0 1.25rem; } }

        .nav-premium {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            padding: 0 2.5rem;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1440px;
            margin: 0 auto;
        }
        @media (max-width: 640px) { .nav-premium { padding: 0 1.25rem; } }
        .nav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--black);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .nav-brand .dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--indigo); }

        .checkout-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s var(--transition);
        }
        .checkout-card:hover { border-color: rgba(99,102,241,0.08); }

        .btn-primary {
            background: var(--black);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border: none;
            cursor: pointer;
            transition: all 0.4s var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            justify-content: center;
        }
        .btn-primary:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.08); }

        .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.03);
        }
        .order-item:last-child { border-bottom: none; }

        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        @media (max-width: 640px) {
            .checkout-card { padding: 1.25rem; }
        }
    </style>
</head>
<body>

<nav class="nav-premium">
    <a href="index.php" class="nav-brand"><span class="dot"></span> THREAD & TREND</a>
    <div style="display:flex; align-items:center; gap:1.5rem;">
        <a href="cart.php" class="nav-back" style="color:var(--gray-400); text-decoration:none; font-size:0.8rem; transition:color 0.3s;"><i class="fas fa-arrow-left"></i> Cart</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <span style="font-size:0.65rem; color:var(--gray-400);"><?= htmlspecialchars($_SESSION['name']) ?></span>
        <?php endif; ?>
    </div>
</nav>

<div class="container-luxury" style="padding-top: 2.5rem; padding-bottom: 4rem;">

    <h1 style="font-family:'Playfair Display',serif; font-size:2.25rem; font-weight:700; letter-spacing:-0.02em; margin-bottom:2rem;">Checkout</h1>

    <?php if (isset($error_msg)): ?>
        <div class="error-msg" style="margin-bottom:1.5rem;"><i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i> <?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Shipping Form -->
        <div class="lg:col-span-2">
            <form method="POST" class="checkout-card">
                <h3 style="font-weight:600; font-size:1rem; margin-bottom:1.5rem;">Shipping Information</h3>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label style="font-size:0.65rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--gray-500);">Full Name</label>
                        <input type="text" value="<?= htmlspecialchars($user['name']) ?>" disabled style="width:100%; padding:0.8rem 1rem; border:1px solid rgba(0,0,0,0.04); border-radius:12px; background:var(--gray-50); color:var(--gray-600); font-size:0.95rem; margin-top:0.3rem;">
                    </div>
                    <div>
                        <label style="font-size:0.65rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--gray-500);">Shipping Address</label>
                        <textarea disabled style="width:100%; padding:0.8rem 1rem; border:1px solid rgba(0,0,0,0.04); border-radius:12px; background:var(--gray-50); color:var(--gray-600); font-size:0.95rem; margin-top:0.3rem; resize:vertical; min-height:80px;"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label style="font-size:0.65rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--gray-500);">Email</label>
                            <input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled style="width:100%; padding:0.8rem 1rem; border:1px solid rgba(0,0,0,0.04); border-radius:12px; background:var(--gray-50); color:var(--gray-600); font-size:0.95rem; margin-top:0.3rem;">
                        </div>
                        <div>
                            <label style="font-size:0.65rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--gray-500);">Contact</label>
                            <input type="text" value="<?= htmlspecialchars($user['contact']) ?>" disabled style="width:100%; padding:0.8rem 1rem; border:1px solid rgba(0,0,0,0.04); border-radius:12px; background:var(--gray-50); color:var(--gray-600); font-size:0.95rem; margin-top:0.3rem;">
                        </div>
                    </div>
                </div>

                <button type="submit" name="place_order" class="btn-primary" style="margin-top:1.5rem;">
                    <i class="fas fa-lock" style="font-size:0.7rem;"></i>
                    Place Order
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="checkout-card" style="position:sticky; top:100px;">
                <h3 style="font-weight:600; font-size:1rem; margin-bottom:1rem;">Order Summary</h3>

                <?php foreach ($cart_products as $item): ?>
                    <div class="order-item">
                        <div style="width:44px; height:44px; background:var(--gray-50); border-radius:8px; display:flex; align-items:center; justify-content:center; padding:0.25rem; flex-shrink:0;">
                            <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="" style="max-width:100%; max-height:100%; object-fit:contain; mix-blend-mode:multiply;">
                            <?php else: ?>
                                <span style="font-size:0.4rem; color:var(--gray-300);">img</span>
                            <?php endif; ?>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:0.7rem; font-weight:500; color:var(--black); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($item['name']) ?></div>
                            <div style="font-size:0.6rem; color:var(--gray-400);">Qty: <?= $item['qty'] ?></div>
                        </div>
                        <div style="font-weight:600; font-size:0.85rem; white-space:nowrap;">₱<?= number_format($item['total_price'], 2) ?></div>
                    </div>
                <?php endforeach; ?>

                <div style="border-top:1px solid rgba(0,0,0,0.04); padding-top:1rem; margin-top:0.5rem;">
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:var(--gray-500);">
                        <span>Subtotal</span>
                        <span>₱<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:var(--gray-500); margin-top:0.25rem;">
                        <span>Shipping</span>
                        <span style="color:var(--gray-300);">Free</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:1.1rem; font-weight:700; margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid rgba(0,0,0,0.04);">
                        <span>Total</span>
                        <span style="font-family:'Playfair Display',serif;">₱<?= number_format($subtotal, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>