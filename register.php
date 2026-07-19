<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : 'customer';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO users (name, email, password, address, contact, role) 
                                           VALUES ('$name', '$email', '$hashed_password', '$address', '$contact', '$role')");
            if ($insert) {
                echo "<script>alert('Account created successfully! Welcome to Thread & Trend.'); window.location='login.php';</script>";
                exit();
            } else {
                $error = "Registration failed. Please try again.";
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
    <title>Create Account — THREAD & TREND</title>
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
        .register-container {
            max-width: 520px;
            width: 100%;
            background: white;
            border-radius: 32px;
            padding: 3rem 2.5rem;
            box-shadow: 0 40px 80px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.03);
        }
        @media (max-width: 480px) { .register-container { padding: 2rem 1.5rem; } }

        .register-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--black);
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .register-brand .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--indigo); }

        .register-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; text-align: center; margin-top: 1.5rem; letter-spacing: -0.02em; }
        .register-subtitle { text-align: center; color: var(--gray-400); font-size: 0.85rem; margin-top: 0.5rem; }

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
        .input-group input, .input-group textarea, .input-group select {
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
        .input-group input:focus, .input-group textarea:focus, .input-group select:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.08);
            background: white;
        }
        .input-group textarea { resize: vertical; min-height: 80px; }

        .btn-register {
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
        .btn-register:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.08); }

        .register-footer { text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: var(--gray-400); }
        .register-footer a { color: var(--black); font-weight: 600; text-decoration: none; transition: color 0.3s; }
        .register-footer a:hover { color: var(--indigo); }

        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-top: 1rem;
        }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 480px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-brand"><span class="dot"></span> THREAD & TREND</div>
    <h1 class="register-title">Create Account</h1>
    <p class="register-subtitle">Join the Thread & Trend community</p>

    <?php if (isset($error)): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="name" required placeholder="John Doe">
        </div>
        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="you@example.com">
        </div>
        <div class="grid-2">
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="••••••••">
            </div>
        </div>
        <div class="input-group">
            <label>Contact Number</label>
            <input type="text" name="contact" required placeholder="+63 912 345 6789">
        </div>
        <div class="input-group">
            <label>Shipping Address</label>
            <textarea name="address" required placeholder="Enter your complete address"></textarea>
        </div>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="input-group">
                <label>Account Role</label>
                <select name="role">
                    <option value="customer">Customer</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
        <?php endif; ?>

        <button type="submit" name="register" class="btn-register">Create Account</button>
    </form>

    <div class="register-footer">
        Already have an account? <a href="login.php">Sign In</a>
    </div>
    <div class="register-footer" style="margin-top:0.75rem; font-size:0.65rem; color:var(--gray-300);">
        <a href="index.php" style="color:var(--gray-300);"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
    </div>
</div>

</body>
</html>