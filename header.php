<?php
include 'config.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thread and Trend Apparel Store</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .mix-blend-multiply { mix-blend-mode: multiply; }
        .text-xxs { font-size: 0.65rem; }
    </style>
</head>
<body class="bg-gray-50/50 min-h-screen flex flex-col justify-between antialiased font-sans">
    
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex justify-between items-center">
            
            <a href="index.php" class="flex items-center gap-2 group">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Thread and Trend logo">
                    <rect width="28" height="28" rx="7" class="fill-indigo-600"/>
                    <path d="M14 6L20 9.5V17L14 20.5L8 17V9.5L14 6Z" stroke="white" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M14 6V20.5" stroke="white" stroke-width="1.6"/>
                </svg>
                <span class="font-black text-xl tracking-wider text-indigo-600 uppercase transition">THREAD AND TREND</span>
            </a>

            <div class="flex items-center gap-6">
                <a href="index.php" class="text-xs font-bold uppercase tracking-wider transition <?= $current_page === 'index.php' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' ?>">Store Catalog</a>
                <a href="cart.php" class="text-xs font-bold uppercase tracking-wider transition <?= $current_page === 'cart.php' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' ?>">Cart Collection</a>
                <a href="about.php" class="text-xs font-bold uppercase tracking-wider transition <?= $current_page === 'about.php' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' ?>">About Us</a>

                <?php if (isset($_SESSION['role']) &&$_SESSION['role'] === 'admin'): ?>
                    <a href="dashboard.php" class="bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded hover:bg-indigo-100 transition">Admin Panel</a>
                    <a href="manage_users.php" class="bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded hover:bg-indigo-100 transition">Manage Users</a>
                <?php endif; ?>

                <div class="h-4 w-px bg-gray-200"></div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400 font-medium">Hello, <strong class="text-gray-700 font-semibold"><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
                        <a href="logout.php" class="text-xs font-bold uppercase tracking-wider text-red-500 hover:underline">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="text-xs font-bold uppercase tracking-wider text-indigo-600 hover:underline">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl w-full mx-auto px-4 py-8 flex-grow">