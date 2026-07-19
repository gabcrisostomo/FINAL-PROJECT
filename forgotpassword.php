<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — THREAD & TREND</title>
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
        .forgot-container {
            max-width: 480px;
            width: 100%;
            background: white;
            border-radius: 32px;
            padding: 3rem 2.5rem;
            box-shadow: 0 40px 80px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.03);
        }
        @media (max-width: 480px) { .forgot-container { padding: 2rem 1.5rem; } }

        .forgot-brand {
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
        .forgot-brand .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--indigo); }

        .forgot-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; text-align: center; margin-top: 1.5rem; letter-spacing: -0.02em; }
        .forgot-subtitle { text-align: center; color: var(--gray-400); font-size: 0.85rem; margin-top: 0.5rem; }

        .input-group { margin-top: 1.5rem; }
        .input-group label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            margin-bottom: 0.4rem;
        }
        .input-group input {
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
        .input-group input:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.08);
            background: white;
        }

        .btn-forgot {
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
        .btn-forgot:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.08); }

        .forgot-footer { text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: var(--gray-400); }
        .forgot-footer a { color: var(--black); font-weight: 600; text-decoration: none; transition: color 0.3s; }
        .forgot-footer a:hover { color: var(--indigo); }

        .success-msg {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-top: 1rem;
        }
        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<?php
if (isset($_POST['recover'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        $temp_password = substr(bin2hex(random_bytes(4)), 0, 8);
        $hashed_temp = password_hash($temp_password, PASSWORD_DEFAULT);
        $update = mysqli_query($conn, "UPDATE users SET password = '$hashed_temp' WHERE id = '{$user['id']}'");

        if ($update) {
            $success = "A temporary password has been sent to your email address.";
        } else {
            $error = "Password reset failed. Please try again.";
        }
    } else {
        $error = "No account found with this email address.";
    }
}
?>

<div class="forgot-container">
    <div class="forgot-brand"><span class="dot"></span> THREAD & TREND</div>
    <h1 class="forgot-title">Reset Password</h1>
    <p class="forgot-subtitle">Enter your email to receive a temporary password</p>

    <?php if (isset($error)): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="success-msg"><i class="fas fa-check-circle" style="margin-right:0.5rem;"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="you@example.com">
        </div>
        <button type="submit" name="recover" class="btn-forgot">Send Recovery Email</button>
    </form>

    <div class="forgot-footer">
        <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Sign In</a>
    </div>
</div>

</body>
</html>