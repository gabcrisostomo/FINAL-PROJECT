<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['confirm_payment'])) {
    unset($_SESSION['cart']);
    echo "<script>alert('Payment successful! Your order is being processed.'); window.location='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment — THREAD & TREND</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --black: #0a0a0a;
            --gray-50: #f8f8f6;
            --gray-400: #a8a8a4;
            --gray-500: #8a8a86;
            --indigo: #6366f1;
            --transition: cubic-bezier(0.4, 0, 0.2, 1);
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
        .payment-container {
            max-width: 480px;
            width: 100%;
            background: white;
            border-radius: 32px;
            padding: 3rem 2.5rem;
            box-shadow: 0 40px 80px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.03);
            text-align: center;
        }
        @media (max-width: 480px) { .payment-container { padding: 2rem 1.5rem; } }

        .payment-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--gray-50);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.75rem;
            color: var(--indigo);
        }

        .payment-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .payment-subtitle {
            color: var(--gray-400);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 16px;
            margin-top: 0.75rem;
            cursor: pointer;
            transition: all 0.3s var(--transition);
            background: var(--gray-50);
        }
        .payment-method:hover { border-color: var(--indigo); background: white; }
        .payment-method.selected { border-color: var(--indigo); background: white; box-shadow: 0 0 0 4px rgba(99,102,241,0.06); }
        .payment-method .icon { font-size: 1.25rem; color: var(--gray-400); width: 32px; }
        .payment-method .name { font-weight: 500; font-size: 0.9rem; flex:1; text-align:left; }

        .btn-payment {
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
        }
        .btn-payment:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.08); }

        .notice {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 1rem;
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 1.5rem;
            line-height: 1.6;
        }
        .notice i { color: var(--indigo); margin-right: 0.5rem; }

        .back-link {
            display: inline-block;
            margin-top: 1rem;
            font-size: 0.7rem;
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover { color: var(--black); }
    </style>
</head>
<body>

<div class="payment-container">
    <div class="payment-icon">
        <i class="fas fa-credit-card"></i>
    </div>

    <h1 class="payment-title">Payment</h1>
    <p class="payment-subtitle">Select your preferred payment method</p>

    <form method="POST" style="margin-top:1.5rem;">
        <div class="payment-method selected" onclick="selectMethod(this)">
            <span class="icon"><i class="fas fa-truck"></i></span>
            <span class="name">Cash on Delivery</span>
            <span style="font-size:0.65rem; color:var(--gray-300);">COD</span>
        </div>
        <div class="payment-method" onclick="selectMethod(this)">
            <span class="icon"><i class="fas fa-mobile-alt"></i></span>
            <span class="name">GCash</span>
            <span style="font-size:0.65rem; color:var(--gray-300);">Digital Wallet</span>
        </div>
        <div class="payment-method" onclick="selectMethod(this)">
            <span class="icon"><i class="fas fa-wallet"></i></span>
            <span class="name">Maya</span>
            <span style="font-size:0.65rem; color:var(--gray-300);">Digital Wallet</span>
        </div>
        <div class="payment-method" onclick="selectMethod(this)">
            <span class="icon"><i class="fas fa-university"></i></span>
            <span class="name">Bank Transfer</span>
            <span style="font-size:0.65rem; color:var(--gray-300);">Wire</span>
        </div>

        <div class="notice">
            <i class="fas fa-info-circle"></i>
            This is a sandbox environment. No real payments are processed.
        </div>

        <button type="submit" name="confirm_payment" class="btn-payment">
            <i class="fas fa-check" style="margin-right:0.5rem;"></i>
            Confirm Payment
        </button>
    </form>

    <a href="checkout.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Checkout</a>
</div>

<script>
function selectMethod(el) {
    document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
    el.classList.add('selected');
}
</script>

</body>
</html>