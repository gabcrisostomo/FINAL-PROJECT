<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$search_query = "";
$results = null;
if (isset($_GET['q'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['q']);
    $results = mysqli_query($conn, "SELECT * FROM products WHERE name LIKE '%$search_query%' OR category LIKE '%$search_query%' ORDER BY name ASC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search — THREAD & TREND</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --black: #0a0a0a;
            --gray-50: #f8f8f6;
            --gray-200: #e5e5e3;
            --gray-300: #d4d4d0;
            --gray-400: #a8a8a4;
            --gray-500: #8a8a86;
            --gray-600: #6a6a66;
            --indigo: #6366f1;
            --transition: cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-md: 0 8px 32px rgba(0,0,0,0.06);
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
        .nav-back { color: var(--gray-400); text-decoration: none; font-size: 0.8rem; transition: color 0.3s; display: flex; align-items: center; gap: 0.5rem; }
        .nav-back:hover { color: var(--black); }

        .search-box {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--shadow-md);
        }
        .search-box input {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s var(--transition);
            background: var(--gray-50);
            font-family: 'Inter', sans-serif;
            outline: none;
        }
        .search-box input:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.08);
            background: white;
        }
        .search-box button {
            padding: 1rem 2.5rem;
            background: var(--black);
            color: white;
            border: none;
            border-radius: 16px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            transition: all 0.3s var(--transition);
            white-space: nowrap;
        }
        .search-box button:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }

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
        }
        .table-premium tbody td {
            padding: 1rem 1.25rem;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(0,0,0,0.02);
            color: var(--gray-700);
        }
        .table-premium tbody tr:hover { background: rgba(0,0,0,0.01); }

        .btn-action {
            padding: 0.4rem 1rem;
            border-radius: 100px;
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: all 0.3s var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: none;
            cursor: pointer;
        }
        .btn-action.edit { background: rgba(99,102,241,0.08); color: var(--indigo); }
        .btn-action.edit:hover { background: var(--indigo); color: white; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(99,102,241,0.2); }
        .btn-action.delete { background: rgba(239,68,68,0.08); color: #ef4444; }
        .btn-action.delete:hover { background: #ef4444; color: white; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(239,68,68,0.2); }

        .card-premium {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.03);
            overflow: hidden;
            transition: all 0.4s var(--transition);
        }

        @media (max-width: 768px) {
            .search-box { padding: 1.25rem; }
            .search-box input { padding: 0.75rem 1rem; font-size: 0.85rem; }
            .search-box button { padding: 0.75rem 1.5rem; font-size: 0.65rem; }
            .table-premium thead th { padding: 0.75rem; font-size: 0.5rem; }
            .table-premium tbody td { padding: 0.75rem; font-size: 0.75rem; }
        }
    </style>
</head>
<body>

<nav class="nav-premium">
    <a href="index.php" class="nav-brand"><span class="dot"></span> THREAD & TREND</a>
    <div style="display:flex; align-items:center; gap:1.5rem;">
        <a href="dashboard.php" class="nav-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <span style="font-size:0.65rem; color:var(--gray-400);"><?= htmlspecialchars($_SESSION['name']) ?></span>
        <a href="logout.php" style="font-size:0.65rem; color:var(--gray-400);">Logout</a>
    </div>
</nav>

<div class="container-luxury" style="padding-top: 2.5rem; padding-bottom: 4rem;">

    <h1 style="font-family:'Playfair Display',serif; font-size:2.25rem; font-weight:700; letter-spacing:-0.02em; margin-bottom:1.5rem;">
        <i class="fas fa-search" style="color:var(--indigo); margin-right:0.5rem;"></i>
        Search Products
    </h1>

    <div class="search-box">
        <form method="GET" style="display:flex; gap:1rem; flex-wrap:wrap;">
            <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by product name or category..." required style="flex:1; min-width:200px;">
            <button type="submit"><i class="fas fa-search" style="margin-right:0.5rem;"></i> Search</button>
        </form>
    </div>

    <?php if (isset($_GET['q'])): ?>
        <div style="margin-top:2rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:0.5rem;">
                <span style="font-size:0.8rem; color:var(--gray-400);">
                    Results for: <strong style="color:var(--black);">"<?= htmlspecialchars($search_query) ?>"</strong>
                    <?php if ($results && mysqli_num_rows($results) > 0): ?>
                        · <?= mysqli_num_rows($results) ?> product<?= mysqli_num_rows($results) > 1 ? 's' : '' ?> found
                    <?php endif; ?>
                </span>
                <a href="search.php" style="font-size:0.7rem; color:var(--gray-400); text-decoration:none;">Clear search</a>
            </div>

            <div class="card-premium">
                <div style="overflow-x:auto;">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th style="text-align:right;">Price</th>
                                <th style="text-align:center;">Stock</th>
                                <th style="text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$results || mysqli_num_rows($results) == 0): ?>
                                <tr><td colspan="5" style="text-align:center; padding:3rem; color:var(--gray-400);">
                                    <i class="fas fa-search" style="display:block; font-size:2rem; margin-bottom:0.5rem; color:var(--gray-200);"></i>
                                    No products found matching "<strong><?= htmlspecialchars($search_query) ?></strong>"
                                </td></tr>
                            <?php else: ?>
                                <?php while ($prod = mysqli_fetch_assoc($results)): ?>
                                    <tr>
                                        <td class="product-name" style="font-weight:600; color:var(--black);"><?= htmlspecialchars($prod['name']) ?></td>
                                        <td style="color:var(--gray-500); font-size:0.75rem;"><?= htmlspecialchars($prod['category']) ?></td>
                                        <td style="text-align:right; font-weight:600; font-family:'Playfair Display',serif;">₱<?= number_format($prod['price'], 2) ?></td>
                                        <td style="text-align:center; font-weight:600;"><?= htmlspecialchars($prod['stock']) ?></td>
                                        <td style="text-align:center;">
                                            <div style="display:flex; gap:0.5rem; justify-content:center; flex-wrap:wrap;">
                                                <a href="edit.php?id=<?= $prod['id'] ?>" class="btn-action edit">
                                                    <i class="fas fa-pen"></i> Edit
                                                </a>
                                                <a href="delete.php?id=<?= $prod['id'] ?>" 
                                                   onclick="return confirm('Permanently delete this product?')" 
                                                   class="btn-action delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>