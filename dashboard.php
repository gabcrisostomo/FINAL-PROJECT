<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$inventory_query = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
$logs_query = mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY logged_at DESC LIMIT 50");

$total_products = mysqli_num_rows($inventory_query);
$low_stock_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock <= 5");
$low_stock_count = mysqli_fetch_assoc($low_stock_result)['total'];
$total_logs_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM audit_logs");
$total_logs = mysqli_fetch_assoc($total_logs_result)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio — THREAD & TREND</title>
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
            --indigo: #6366f1;
            --green: #22c55e;
            --red: #ef4444;
            --yellow: #f59e0b;
            --transition: cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 32px rgba(0,0,0,0.06);
            --shadow-lg: 0 24px 64px rgba(0,0,0,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--black);
            -webkit-font-smoothing: antialiased;
        }
        .container-luxury { max-width: 1440px; margin: 0 auto; padding: 0 2.5rem; }
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

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem 1.75rem;
            border: 1px solid rgba(0,0,0,0.03);
            transition: all 0.4s var(--transition);
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
        .stat-card .icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        .stat-card .icon.blue { background: rgba(99,102,241,0.1); color: var(--indigo); }
        .stat-card .icon.yellow { background: rgba(245,158,11,0.1); color: var(--yellow); }
        .stat-card .icon.green { background: rgba(34,197,94,0.1); color: var(--green); }
        .stat-card .number { font-family: 'Playfair Display', serif; font-size: 2.25rem; font-weight: 700; margin-top: 0.5rem; }
        .stat-card .label { font-size: 0.7rem; color: var(--gray-400); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }

        .btn-action {
            padding: 0.5rem 1.25rem;
            border-radius: 100px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: all 0.3s var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }
        .btn-action.primary { background: var(--black); color: white; }
        .btn-action.primary:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .btn-action.edit { background: rgba(99,102,241,0.08); color: var(--indigo); }
        .btn-action.edit:hover { background: var(--indigo); color: white; transform: translateY(-2px); }
        .btn-action.delete { background: rgba(239,68,68,0.08); color: var(--red); }
        .btn-action.delete:hover { background: var(--red); color: white; transform: translateY(-2px); }
        .btn-action.view { background: rgba(0,0,0,0.04); color: var(--gray-600); }
        .btn-action.view:hover { background: var(--gray-600); color: white; transform: translateY(-2px); }

        .card-premium {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.03);
            overflow: hidden;
            transition: all 0.4s var(--transition);
        }
        .card-premium:hover { border-color: rgba(99,102,241,0.06); }

        .table-premium {
            width: 100%;
            border-collapse: collapse;
        }
        .table-premium thead th {
            text-align: left;
            padding: 1rem 1.25rem;
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-400);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            white-space: nowrap;
        }
        .table-premium tbody td {
            padding: 1rem 1.25rem;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(0,0,0,0.02);
            color: var(--gray-700);
            vertical-align: middle;
        }
        .table-premium tbody tr { transition: background 0.3s; }
        .table-premium tbody tr:hover { background: rgba(0,0,0,0.01); }
        .table-premium .product-name { font-weight: 600; color: var(--black); }

        .status-badge {
            padding: 0.2rem 0.7rem;
            border-radius: 100px;
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-badge.low { background: rgba(245,158,11,0.1); color: var(--yellow); }
        .status-badge.out { background: rgba(239,68,68,0.1); color: var(--red); }
        .status-badge.in { background: rgba(34,197,94,0.1); color: var(--green); }

        .product-thumb {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(0,0,0,0.04);
            background: var(--gray-50);
        }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s var(--transition) forwards;
        }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        .fade-up-d1 { animation-delay: 0.05s; }
        .fade-up-d2 { animation-delay: 0.1s; }
        .fade-up-d3 { animation-delay: 0.15s; }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-400);
        }
        .empty-state i { font-size: 3rem; color: var(--gray-200); margin-bottom: 1.5rem; display: block; }
        .empty-state h4 { font-size: 1.25rem; font-weight: 600; color: var(--gray-600); font-family: 'Playfair Display', serif; }
        .empty-state p { font-size: 0.9rem; margin-top: 0.5rem; }

        @media (max-width: 768px) {
            .table-premium thead th { padding: 0.5rem; font-size: 0.5rem; }
            .table-premium tbody td { padding: 0.5rem; font-size: 0.7rem; }
            .btn-action { padding: 0.3rem 0.75rem; font-size: 0.5rem; }
            .stat-card .number { font-size: 1.75rem; }
            .product-thumb { width: 35px; height: 35px; }
        }
    </style>
</head>
<body>

<nav class="nav-premium">
    <a href="index.php" class="nav-brand"><span class="dot"></span> THREAD & TREND</a>
    <div style="display:flex; align-items:center; gap:1.5rem;">
        <a href="index.php" class="nav-back"><i class="fas fa-arrow-left"></i> Store</a>
        <span style="font-size:0.65rem; color:var(--gray-400);"><?= htmlspecialchars($_SESSION['name']) ?></span>
        <a href="logout.php" style="font-size:0.65rem; color:var(--gray-400);">Logout</a>
    </div>
</nav>

<div class="container-luxury" style="padding-top: 2rem; padding-bottom: 4rem;">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:2.5rem;">
        <div>
            <h1 style="font-family:'Playfair Display',serif; font-size:2.5rem; font-weight:700; letter-spacing:-0.02em;">Studio Dashboard</h1>
            <p style="color:var(--gray-400); font-size:0.85rem; margin-top:0.25rem;">Manage your product catalog and monitor performance.</p>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <a href="add.php" class="btn-action primary" style="padding:0.7rem 1.75rem;">
                <i class="fas fa-plus"></i> Add Product
            </a>
            <a href="search.php" class="btn-action edit" style="padding:0.7rem 1.75rem;">
                <i class="fas fa-search"></i> Search
            </a>
            <a href="manage_users.php" class="btn-action view" style="padding:0.7rem 1.75rem;">
                <i class="fas fa-users"></i> Users
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="stat-card fade-up fade-up-d1">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div class="icon blue"><i class="fas fa-tshirt"></i></div>
                <div>
                    <div class="number"><?= $total_products ?></div>
                    <div class="label">Total Products</div>
                </div>
            </div>
        </div>
        <div class="stat-card fade-up fade-up-d2">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div class="icon yellow"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <div class="number" style="color:<?= $low_stock_count > 0 ? 'var(--yellow)' : 'var(--black)' ?>;"><?= $low_stock_count ?></div>
                    <div class="label">Low Stock Items</div>
                </div>
            </div>
        </div>
        <div class="stat-card fade-up fade-up-d3">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div class="icon green"><i class="fas fa-history"></i></div>
                <div>
                    <div class="number"><?= $total_logs ?></div>
                    <div class="label">Logged Activities</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card-premium fade-up" style="animation-delay:0.2s;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid rgba(0,0,0,0.04); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
            <h3 style="font-weight:600; font-size:0.9rem; display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-boxes" style="color:var(--indigo);"></i>
                Inventory Management
            </h3>
            <span style="font-size:0.7rem; color:var(--gray-400);"><?= $total_products ?> products</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th style="width:60px;">Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th style="text-align:right;">Price</th>
                        <th style="text-align:center;">Stock</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($inventory_query) == 0): ?>
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-tshirt"></i>
                                <h4>No products yet</h4>
                                <p>Start building your catalog by adding your first product.</p>
                                <a href="add.php" class="btn-action primary" style="margin-top:1rem; display:inline-flex;">
                                    <i class="fas fa-plus"></i> Add Product
                                </a>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                    <?php while ($prod = mysqli_fetch_assoc($inventory_query)): 
                        $status = $prod['stock'] == 0 ? 'out' : ($prod['stock'] <= 5 ? 'low' : 'in');
                        $status_label = $prod['stock'] == 0 ? 'Out of Stock' : ($prod['stock'] <= 5 ? 'Low Stock' : 'In Stock');
                        
                        // Get first image
                        $images = explode(',', $prod['image']);
                        $first_image = $images[0] ?? '';
                        $has_image = !empty($first_image) && file_exists($first_image);
                    ?>
                        <tr>
                            <td>
                                <?php if ($has_image): ?>
                                    <img src="<?= htmlspecialchars($first_image) ?>" alt="" class="product-thumb">
                                <?php else: ?>
                                    <div class="product-thumb" style="display:flex; align-items:center; justify-content:center; font-size:0.5rem; color:var(--gray-300);">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="product-name"><?= htmlspecialchars($prod['name']) ?></td>
                            <td style="color:var(--gray-500); font-size:0.75rem;"><?= htmlspecialchars($prod['category']) ?></td>
                            <td style="text-align:right; font-weight:600; font-family:'Playfair Display',serif;">₱<?= number_format($prod['price'], 2) ?></td>
                            <td style="text-align:center; font-weight:600;"><?= htmlspecialchars($prod['stock']) ?></td>
                            <td style="text-align:center;">
                                <span class="status-badge <?= $status ?>"><?= $status_label ?></span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex; gap:0.4rem; justify-content:center; flex-wrap:wrap;">
                                    <a href="view.php?id=<?= $prod['id'] ?>" class="btn-action view" title="View Product">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $prod['id'] ?>" class="btn-action edit" title="Edit Product">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $prod['id'] ?>" 
                                       onclick="return confirm('Permanently delete this product? This action cannot be undone.')" 
                                       class="btn-action delete" title="Delete Product">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="card-premium fade-up" style="animation-delay:0.3s; margin-top:2rem;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid rgba(0,0,0,0.04);">
            <h3 style="font-weight:600; font-size:0.9rem; display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-clipboard-list" style="color:var(--indigo);"></i>
                Activity Log
            </h3>
        </div>
        <div style="overflow-x:auto; max-height:300px; overflow-y:auto;">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th style="width:160px;">Timestamp</th>
                        <th>User</th>
                        <th>Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($logs_query) == 0): ?>
                        <tr><td colspan="3" style="text-align:center; padding:2rem; color:var(--gray-400);">No activities logged yet.</td></tr>
                    <?php endif; ?>
                    <?php while ($log = mysqli_fetch_assoc($logs_query)): ?>
                        <tr>
                            <td style="font-size:0.7rem; color:var(--gray-400); white-space:nowrap;"><?= $log['logged_at'] ?></td>
                            <td style="font-weight:500; font-size:0.8rem;"><?= htmlspecialchars($log['user_name']) ?></td>
                            <td style="font-size:0.8rem; color:var(--gray-600);"><?= htmlspecialchars($log['activity']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>