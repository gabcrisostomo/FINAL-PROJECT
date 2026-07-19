<?php
include 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $stock_check = mysqli_query($conn, "SELECT stock FROM products WHERE id = '$product_id'");
    $stock_row = mysqli_fetch_assoc($stock_check);
    $available_stock = $stock_row ? intval($stock_row['stock']) : 0;
    $current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
    if ($current_qty < $available_stock) {
        $_SESSION['cart'][$product_id] = $current_qty + 1;
        $success = "Item added to your cart!";
    } else {
        $error = "Not enough stock available.";
    }
    header("Location: cart.php");
    exit();
}

// Handle Update Cart
if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $product_id => $qty) {
        $product_id = intval($product_id);
        $qty = intval($qty);
        $stock_check = mysqli_query($conn, "SELECT stock FROM products WHERE id = '$product_id'");
        $stock_row = mysqli_fetch_assoc($stock_check);
        $available_stock = $stock_row ? intval($stock_row['stock']) : 0;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = min($qty, $available_stock);
        }
    }
    header("Location: cart.php");
    exit();
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Get cart products
$cart_products = [];
$subtotal = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($query)) {
        $row['qty'] = $_SESSION['cart'][$row['id']];
        $row['total_price'] = $row['price'] * $row['qty'];
        $subtotal += $row['total_price'];
        $cart_products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart — THREAD & TREND</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --black: #0a0a0a;
            --white: #ffffff;
            --gray-50: #f8f8f6;
            --gray-100: #f0f0ee;
            --gray-200: #e5e5e3;
            --gray-300: #d4d4d0;
            --gray-400: #a8a8a4;
            --gray-500: #8a8a86;
            --gray-600: #6a6a66;
            --gray-700: #4a4a46;
            --gray-800: #2a2a26;
            --gray-900: #1a1a16;
            --indigo: #6366f1;
            --indigo-light: #818cf8;
            --indigo-dark: #4f46e5;
            --green: #22c55e;
            --red: #ef4444;
            --yellow: #f59e0b;
            --transition: cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 32px rgba(0,0,0,0.06);
            --shadow-lg: 0 24px 64px rgba(0,0,0,0.08);
            --shadow-xl: 0 40px 80px rgba(0,0,0,0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--black);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }
        .container-luxury { max-width: 1200px; margin: 0 auto; padding: 0 2.5rem; }
        @media (max-width: 640px) { .container-luxury { padding: 0 1.25rem; } }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--gray-50); }
        ::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 100px; }

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
        .nav-back { color: var(--gray-400); text-decoration: none; font-size: 0.8rem; transition: color 0.3s; display: flex; align-items: center; gap: 0.5rem; }
        .nav-back:hover { color: var(--black); }

        .cart-item {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.03);
            transition: all 0.4s var(--transition);
        }
        .cart-item:hover { border-color: rgba(99,102,241,0.08); box-shadow: var(--shadow-md); }

        .btn-primary {
            background: var(--black);
            color: white;
            padding: 0.9rem 2.5rem;
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
        }
        .btn-primary:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
        .btn-secondary {
            background: transparent;
            color: var(--black);
            padding: 0.9rem 2rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border: 1px solid rgba(0,0,0,0.08);
            cursor: pointer;
            transition: all 0.4s var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }
        .btn-secondary:hover { border-color: var(--black); background: var(--gray-50); }

        .cart-empty { text-align: center; padding: 5rem 2rem; }
        .cart-empty i { font-size: 3.5rem; color: var(--gray-300); margin-bottom: 1.5rem; }
        .cart-empty h3 { font-size: 1.5rem; font-weight: 700; color: var(--gray-600); font-family: 'Playfair Display', serif; }
        .cart-empty p { color: var(--gray-400); margin-top: 0.5rem; }

        .qty-input {
            width: 56px;
            text-align: center;
            padding: 0.5rem;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            outline: none;
            background: transparent;
            font-family: 'Inter', sans-serif;
        }

        /* ===== MODAL / POPUP STYLES ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            animation: fadeIn 0.3s var(--transition);
        }
        .modal-overlay.active { display: flex; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-content {
            background: white;
            border-radius: 32px;
            max-width: 480px;
            width: 100%;
            padding: 2.5rem;
            position: relative;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.4s var(--transition);
            max-height: 90vh;
            overflow-y: auto;
        }
        @media (max-width: 480px) { .modal-content { padding: 2rem 1.5rem; } }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--gray-50);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.1rem;
            color: var(--gray-400);
            cursor: pointer;
            transition: all 0.3s var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-close:hover { background: var(--gray-200); color: var(--black); transform: rotate(90deg); }

        .modal-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1rem;
        }
        .modal-icon.success { background: rgba(34,197,94,0.1); color: var(--green); }
        .modal-icon.warning { background: rgba(245,158,11,0.1); color: var(--yellow); }
        .modal-icon.danger { background: rgba(239,68,68,0.1); color: var(--red); }
        .modal-icon.info { background: rgba(99,102,241,0.1); color: var(--indigo); }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: -0.02em;
        }
        .modal-subtitle {
            text-align: center;
            color: var(--gray-400);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        .modal-body { margin-top: 1.5rem; }

        .modal-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        .modal-actions .btn-modal {
            flex: 1;
            padding: 0.9rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border: none;
            cursor: pointer;
            transition: all 0.3s var(--transition);
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .modal-actions .btn-modal.primary { background: var(--black); color: white; }
        .modal-actions .btn-modal.primary:hover { background: var(--gray-800); transform: translateY(-2px); }
        .modal-actions .btn-modal.secondary { background: var(--gray-50); color: var(--gray-600); }
        .modal-actions .btn-modal.secondary:hover { background: var(--gray-200); }
        .modal-actions .btn-modal.danger { background: var(--red); color: white; }
        .modal-actions .btn-modal.danger:hover { background: #dc2626; transform: translateY(-2px); }
        .modal-actions .btn-modal.success { background: var(--green); color: white; }
        .modal-actions .btn-modal.success:hover { background: #16a34a; transform: translateY(-2px); }

        /* Product preview in modal */
        .modal-product-preview {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 16px;
            margin-top: 0.5rem;
        }
        .modal-product-preview .thumb {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .modal-product-preview .thumb img { max-width:100%; max-height:100%; object-fit:contain; }
        .modal-product-preview .info .name { font-weight:600; font-size:0.9rem; }
        .modal-product-preview .info .price { font-size:0.8rem; color:var(--gray-400); }

        .modal-qty-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        .modal-qty-control button {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid rgba(0,0,0,0.06);
            background: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
        }
        .modal-qty-control button:hover { border-color: var(--black); color: var(--black); }
        .modal-qty-control .qty-display {
            font-size: 1.5rem;
            font-weight: 600;
            min-width: 40px;
            text-align: center;
            font-family: 'Playfair Display', serif;
        }

        .modal-edit-field {
            margin-top: 1rem;
        }
        .modal-edit-field label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            margin-bottom: 0.3rem;
        }
        .modal-edit-field input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 12px;
            font-size: 0.95rem;
            background: var(--gray-50);
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.3s var(--transition);
        }
        .modal-edit-field input:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.08);
            background: white;
        }

        @media (max-width: 640px) {
            .cart-item { padding: 1rem; border-radius: 16px; }
            .btn-primary, .btn-secondary { width: 100%; justify-content: center; padding: 0.8rem 1.5rem; }
            .qty-input { width: 44px; font-size: 0.8rem; }
            .modal-content { padding: 1.5rem; }
            .modal-actions { flex-direction: column; }
        }
    </style>
</head>
<body>

<nav class="nav-premium">
    <a href="index.php" class="nav-brand"><span class="dot"></span> THREAD & TREND</a>
    <div style="display:flex; align-items:center; gap:1.5rem;">
        <a href="index.php" class="nav-back"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <span style="font-size:0.65rem; color:var(--gray-400);"><?= htmlspecialchars($_SESSION['name']) ?></span>
            <a href="logout.php" style="font-size:0.65rem; color:var(--gray-400);">Logout</a>
        <?php else: ?>
            <a href="login.php" style="font-size:0.65rem; color:var(--gray-400);">Sign In</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container-luxury" style="padding-top: 2.5rem; padding-bottom: 4rem;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2.5rem; border-bottom:1px solid rgba(0,0,0,0.04); padding-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-family:'Playfair Display',serif; font-size:2.25rem; font-weight:700; letter-spacing:-0.02em;">Your Cart</h1>
            <p style="color:var(--gray-400); font-size:0.8rem; margin-top:0.25rem;"><?= count($cart_products) ?> items</p>
        </div>
        <span style="font-size:0.9rem; color:var(--gray-500);">Total: <strong style="color:var(--black); font-size:1.1rem;">₱<?= number_format($subtotal, 2) ?></strong></span>
    </div>

    <?php if (empty($cart_products)): ?>
        <div class="cart-empty">
            <i class="fas fa-shopping-bag"></i>
            <h3>Your cart is empty</h3>
            <p>Discover our collection and find something you love.</p>
            <a href="index.php" class="btn-primary" style="margin-top:2rem; display:inline-flex;">Start Shopping</a>
        </div>
    <?php else: ?>
        <form method="POST" style="display:flex; flex-direction:column; gap:1.25rem;">
            <?php foreach ($cart_products as $item): 
                $image_found = false;
                $image_src = '';
                if (!empty($item['image'])) {
                    if (file_exists($item['image'])) { $image_src = $item['image']; $image_found = true; }
                    elseif (file_exists('images/' . basename($item['image']))) { $image_src = 'images/' . basename($item['image']); $image_found = true; }
                    elseif (file_exists(basename($item['image']))) { $image_src = basename($item['image']); $image_found = true; }
                }
            ?>
                <div class="cart-item" id="cart-item-<?= $item['id'] ?>">
                    <div style="display:flex; gap:1.5rem; align-items:center; flex-wrap:wrap;">
                        <div style="width:80px; height:80px; background:var(--gray-50); border-radius:16px; display:flex; align-items:center; justify-content:center; padding:0.5rem; flex-shrink:0; border:1px solid rgba(0,0,0,0.03);">
                            <?php if ($image_found): ?>
                                <img src="<?= htmlspecialchars($image_src) ?>" alt="" style="max-width:100%; max-height:100%; object-fit:contain;">
                            <?php else: ?>
                                <span style="font-size:0.5rem; color:var(--gray-300); text-transform:uppercase;">No img</span>
                            <?php endif; ?>
                        </div>
                        <div style="flex:1; min-width:150px;">
                            <div style="font-size:0.55rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--gray-400);"><?= htmlspecialchars($item['category']) ?></div>
                            <div style="font-weight:600; color:var(--black); font-size:0.95rem;"><?= htmlspecialchars($item['name']) ?></div>
                            <div style="font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; color:var(--black); margin-top:0.25rem;">₱<?= number_format($item['price'], 2) ?></div>
                        </div>
                        <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;">
                            <div style="display:flex; align-items:center; border:1px solid rgba(0,0,0,0.06); border-radius:100px; overflow:hidden; background:white;">
                                <button type="button" onclick="updateQty(this, -1, <?= $item['id'] ?>)" style="background:none; border:none; padding:0.4rem 0.8rem; cursor:pointer; color:var(--gray-400); font-size:0.8rem;">−</button>
                                <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1" max="<?= $item['stock'] ?>" class="qty-input" id="qty_<?= $item['id'] ?>">
                                <button type="button" onclick="updateQty(this, 1, <?= $item['id'] ?>)" style="background:none; border:none; padding:0.4rem 0.8rem; cursor:pointer; color:var(--gray-400); font-size:0.8rem;">+</button>
                            </div>
                            <span style="font-weight:700; color:var(--black); min-width:80px; text-align:right;">₱<?= number_format($item['total_price'], 2) ?></span>
                            
                            <!-- Edit Button - Opens Edit Modal -->
                            <button type="button" onclick="openEditModal(<?= $item['id'] ?>)" 
                                    style="background:none; border:none; color:var(--gray-400); transition:all 0.3s; font-size:0.9rem; cursor:pointer;" 
                                    onmouseover="this.style.color='var(--indigo)'" 
                                    onmouseout="this.style.color='var(--gray-400)'">
                                <i class="fas fa-pen"></i>
                            </button>
                            
                            <!-- Remove Button - Opens Delete Modal -->
                            <button type="button" onclick="openDeleteModal(<?= $item['id'] ?>, '<?= addslashes($item['name']) ?>')" 
                                    style="background:none; border:none; color:var(--gray-300); transition:all 0.3s; font-size:0.9rem; cursor:pointer;" 
                                    onmouseover="this.style.color='var(--red)'" 
                                    onmouseout="this.style.color='var(--gray-300)'">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div style="display:flex; flex-wrap:wrap; gap:1rem; justify-content:space-between; align-items:center; padding-top:1.5rem; border-top:1px solid rgba(0,0,0,0.04);">
                <button type="submit" name="update_cart" class="btn-secondary" style="padding:0.7rem 1.5rem;">
                    <i class="fas fa-sync-alt"></i> Update Cart
                </button>
                <div style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
                    <span style="font-size:0.9rem; color:var(--gray-400);">Subtotal: <strong style="color:var(--black);">₱<?= number_format($subtotal, 2) ?></strong></span>
                    <a href="checkout.php" class="btn-primary">Proceed to Checkout <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </form>
    <?php endif; ?>

</div>

<!-- ============================================================
     MODAL 1: CONFIRM DELETE
     ============================================================ -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('deleteModal')"><i class="fas fa-times"></i></button>
        
        <div class="modal-icon danger">
            <i class="fas fa-trash"></i>
        </div>
        <h2 class="modal-title">Remove Item?</h2>
        <p class="modal-subtitle">This item will be removed from your cart.</p>
        
        <div class="modal-body">
            <div class="modal-product-preview" id="deleteProductPreview">
                <div class="thumb"><span style="font-size:0.5rem; color:var(--gray-300);">Product</span></div>
                <div class="info">
                    <div class="name" id="deleteProductName">Product Name</div>
                    <div class="price" id="deleteProductPrice">₱0.00</div>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <button class="btn-modal secondary" onclick="closeModal('deleteModal')">Cancel</button>
            <a href="#" id="deleteConfirmLink" class="btn-modal danger">
                <i class="fas fa-trash"></i> Remove
            </a>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL 2: EDIT QUANTITY
     ============================================================ -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
        
        <div class="modal-icon info">
            <i class="fas fa-pen"></i>
        </div>
        <h2 class="modal-title">Edit Quantity</h2>
        <p class="modal-subtitle">Adjust the quantity for this item.</p>
        
        <div class="modal-body">
            <div class="modal-product-preview" id="editProductPreview">
                <div class="thumb"><span style="font-size:0.5rem; color:var(--gray-300);">Product</span></div>
                <div class="info">
                    <div class="name" id="editProductName">Product Name</div>
                    <div class="price" id="editProductPrice">₱0.00 each</div>
                </div>
            </div>

            <div class="modal-qty-control">
                <button onclick="changeModalQty(-1)"><i class="fas fa-minus"></i></button>
                <span class="qty-display" id="modalQtyDisplay">1</span>
                <button onclick="changeModalQty(1)"><i class="fas fa-plus"></i></button>
            </div>
            
            <div style="text-align:center; margin-top:0.5rem; font-size:0.7rem; color:var(--gray-400);">
                Max stock: <span id="modalMaxStock">30</span>
            </div>

            <form method="POST" id="editQtyForm">
                <input type="hidden" name="product_id" id="editProductId">
                <input type="hidden" name="new_qty" id="editNewQty" value="1">
                
                <div class="modal-actions" style="margin-top:1rem;">
                    <button type="button" class="btn-modal secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" name="update_single_qty" class="btn-modal primary">
                        <i class="fas fa-check"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL 3: SUCCESS / ADDED TO CART
     ============================================================ -->
<div class="modal-overlay" id="successModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('successModal')"><i class="fas fa-times"></i></button>
        
        <div class="modal-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="modal-title" id="successTitle">Added to Cart!</h2>
        <p class="modal-subtitle" id="successMessage">Item has been added to your cart.</p>
        
        <div class="modal-body">
            <div class="modal-product-preview" id="successProductPreview">
                <div class="thumb"><span style="font-size:0.5rem; color:var(--gray-300);">Product</span></div>
                <div class="info">
                    <div class="name" id="successProductName">Product Name</div>
                    <div class="price" id="successProductPrice">₱0.00</div>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <button class="btn-modal secondary" onclick="closeModal('successModal')">Continue Shopping</button>
            <a href="cart.php" class="btn-modal primary">
                <i class="fas fa-shopping-bag"></i> View Cart
            </a>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL 4: WARNING / ERROR
     ============================================================ -->
<div class="modal-overlay" id="warningModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('warningModal')"><i class="fas fa-times"></i></button>
        
        <div class="modal-icon warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h2 class="modal-title" id="warningTitle">Warning</h2>
        <p class="modal-subtitle" id="warningMessage">Something went wrong.</p>
        
        <div class="modal-actions">
            <button class="btn-modal primary" onclick="closeModal('warningModal')">OK</button>
        </div>
    </div>
</div>

<script>
// ============================================================
// QUANTITY UPDATE (inline)
// ============================================================
function updateQty(btn, delta, productId) {
    const input = btn.parentElement.querySelector('.qty-input');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    const max = parseInt(input.getAttribute('max'));
    if (val > max) val = max;
    input.value = val;
    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
}

// ============================================================
// MODAL CONTROLS
// ============================================================
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
});

// ============================================================
// DELETE MODAL
// ============================================================
let deleteProductId = null;

function openDeleteModal(productId, productName) {
    deleteProductId = productId;
    document.getElementById('deleteProductName').textContent = productName;
    document.getElementById('deleteConfirmLink').href = 'cart.php?remove=' + productId;
    
    // Try to get product image and price
    const item = document.getElementById('cart-item-' + productId);
    if (item) {
        const img = item.querySelector('img');
        const thumb = document.querySelector('#deleteProductPreview .thumb');
        if (img) {
            thumb.innerHTML = '<img src="' + img.src + '" style="max-width:100%; max-height:100%; object-fit:contain;">';
        } else {
            thumb.innerHTML = '<span style="font-size:0.5rem; color:var(--gray-300);">No img</span>';
        }
        const priceEl = item.querySelector('.cart-item .product-price') || 
                        item.querySelector('[style*="font-family"]:last-child');
        if (priceEl) {
            document.getElementById('deleteProductPrice').textContent = priceEl.textContent.trim();
        }
    }
    
    openModal('deleteModal');
}

// ============================================================
// EDIT MODAL
// ============================================================
let editProductId = null;
let editCurrentQty = 1;
let editMaxStock = 30;

function openEditModal(productId) {
    editProductId = productId;
    const item = document.getElementById('cart-item-' + productId);
    
    if (item) {
        // Get product name
        const nameEl = item.querySelector('.cart-item .name') || 
                       item.querySelector('[style*="font-weight:600"]');
        if (nameEl) {
            document.getElementById('editProductName').textContent = nameEl.textContent;
        }
        
        // Get product price
        const priceEl = item.querySelector('.cart-item .product-price') || 
                        item.querySelector('[style*="font-family"]:last-child');
        if (priceEl) {
            document.getElementById('editProductPrice').textContent = priceEl.textContent.trim() + ' each';
        }
        
        // Get current quantity
        const qtyInput = item.querySelector('.qty-input');
        if (qtyInput) {
            editCurrentQty = parseInt(qtyInput.value) || 1;
            editMaxStock = parseInt(qtyInput.getAttribute('max')) || 30;
            document.getElementById('modalQtyDisplay').textContent = editCurrentQty;
            document.getElementById('modalMaxStock').textContent = editMaxStock;
            document.getElementById('editNewQty').value = editCurrentQty;
        }
        
        // Get image
        const img = item.querySelector('img');
        const thumb = document.querySelector('#editProductPreview .thumb');
        if (img) {
            thumb.innerHTML = '<img src="' + img.src + '" style="max-width:100%; max-height:100%; object-fit:contain;">';
        } else {
            thumb.innerHTML = '<span style="font-size:0.5rem; color:var(--gray-300);">No img</span>';
        }
        
        // Set form action
        document.getElementById('editProductId').value = productId;
    }
    
    openModal('editModal');
}

function changeModalQty(delta) {
    let current = parseInt(document.getElementById('modalQtyDisplay').textContent) || 1;
    let newVal = current + delta;
    if (newVal < 1) newVal = 1;
    if (newVal > editMaxStock) newVal = editMaxStock;
    document.getElementById('modalQtyDisplay').textContent = newVal;
    document.getElementById('editNewQty').value = newVal;
}

// Handle single item update via modal
document.getElementById('editQtyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const productId = document.getElementById('editProductId').value;
    const newQty = document.getElementById('editNewQty').value;
    
    // Create hidden form to update
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'cart.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'qty[' + productId + ']';
    input.value = newQty;
    form.appendChild(input);
    
    // Add update cart flag
    const updateBtn = document.createElement('input');
    updateBtn.type = 'hidden';
    updateBtn.name = 'update_cart';
    updateBtn.value = '1';
    form.appendChild(updateBtn);
    
    document.body.appendChild(form);
    form.submit();
});

// ============================================================
// SUCCESS MODAL (triggered from add to cart)
// ============================================================
function showSuccessModal(productName, productPrice, productImage) {
    document.getElementById('successProductName').textContent = productName;
    document.getElementById('successProductPrice').textContent = productPrice;
    
    const thumb = document.querySelector('#successProductPreview .thumb');
    if (productImage) {
        thumb.innerHTML = '<img src="' + productImage + '" style="max-width:100%; max-height:100%; object-fit:contain;">';
    } else {
        thumb.innerHTML = '<span style="font-size:0.5rem; color:var(--gray-300);">No img</span>';
    }
    
    openModal('successModal');
}

// ============================================================
// WARNING MODAL
// ============================================================
function showWarningModal(title, message) {
    document.getElementById('warningTitle').textContent = title || 'Warning';
    document.getElementById('warningMessage').textContent = message || 'Something went wrong.';
    openModal('warningModal');
}

// ============================================================
// CHECK FOR URL PARAMETERS (for success/warning messages)
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    
    if (success) {
        showSuccessModal('Item Added!', 'Your item has been added to the cart.', null);
        // Remove param from URL
        window.history.replaceState({}, '', window.location.pathname);
    }
    
    if (error) {
        showWarningModal('Error', error);
        window.history.replaceState({}, '', window.location.pathname);
    }
});

console.log('🛒 Cart with Luxury Modals loaded successfully!');
</script>

</body>
</html>