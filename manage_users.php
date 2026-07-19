<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert = mysqli_query($conn, "INSERT INTO users (name, email, password, address, contact, role) 
                                   VALUES ('$name', '$email', '$hashed_password', '$address', '$contact', '$role')");
    if ($insert) {
        $success = "User added successfully!";
    } else {
        $error = "Failed to add user.";
    }
}

// Handle Edit User
if (isset($_POST['edit_user'])) {
    $id = intval($_POST['user_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $update = mysqli_query($conn, "UPDATE users SET name='$name', email='$email', address='$address', contact='$contact', role='$role' WHERE id='$id'");
    if ($update) {
        $success = "User updated successfully!";
    } else {
        $error = "Failed to update user.";
    }
}

// Handle Reset Password
if (isset($_POST['reset_password'])) {
    $id = intval($_POST['user_id']);
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $update = mysqli_query($conn, "UPDATE users SET password = '$hashed_password' WHERE id = '$id'");
    if ($update) {
        $success = "Password reset successfully! New password: " . htmlspecialchars($new_password);
    } else {
        $error = "Failed to reset password.";
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
        $success = "User deleted successfully!";
    } else {
        $error = "You cannot delete your own account.";
    }
}

// Get all users
$users_query = mysqli_query($conn, "SELECT id, name, email, password, address, contact, role, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users — THREAD & TREND</title>
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
            --indigo: #6366f1;
            --indigo-light: #818cf8;
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
        .btn-action.primary { background: var(--black); color: white; }
        .btn-action.primary:hover { background: var(--gray-800); transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .btn-action.edit { background: rgba(99,102,241,0.08); color: var(--indigo); }
        .btn-action.edit:hover { background: var(--indigo); color: white; transform: translateY(-2px); }
        .btn-action.delete { background: rgba(239,68,68,0.08); color: var(--red); }
        .btn-action.delete:hover { background: var(--red); color: white; transform: translateY(-2px); }
        .btn-action.reset { background: rgba(245,158,11,0.08); color: var(--yellow); }
        .btn-action.reset:hover { background: var(--yellow); color: white; transform: translateY(-2px); }

        .card-premium {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.03);
            overflow: hidden;
            transition: all 0.4s var(--transition);
        }

        .table-premium {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
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
            border-bottom: 1px solid rgba(0,0,0,0.02);
            color: var(--gray-700);
            vertical-align: middle;
        }
        .table-premium tbody tr { transition: background 0.3s; }
        .table-premium tbody tr:hover { background: rgba(0,0,0,0.01); }

        .password-text {
            font-family: 'Courier New', monospace;
            font-size: 0.75rem;
            background: var(--gray-50);
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            color: var(--green);
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .password-hash {
            font-family: 'Courier New', monospace;
            font-size: 0.6rem;
            color: var(--gray-400);
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        .role-badge {
            padding: 0.2rem 0.7rem;
            border-radius: 100px;
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .role-badge.admin { background: rgba(99,102,241,0.1); color: var(--indigo); }
        .role-badge.customer { background: rgba(0,0,0,0.04); color: var(--gray-600); }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(12px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            animation: fadeIn 0.3s var(--transition);
        }
        .modal-overlay.active { display: flex; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .modal-content {
            background: white;
            border-radius: 32px;
            max-width: 520px;
            width: 100%;
            padding: 2.5rem;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.4s var(--transition);
            max-height: 90vh;
            overflow-y: auto;
        }
        @media (max-width: 480px) { .modal-content { padding: 1.5rem; } }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--gray-50);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 1rem;
            color: var(--gray-400);
            cursor: pointer;
            transition: all 0.3s;
        }
        .modal-close:hover { background: var(--gray-200); color: var(--black); transform: rotate(90deg); }

        .modal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }
        .modal-icon.info { background: rgba(99,102,241,0.1); color: var(--indigo); }
        .modal-icon.success { background: rgba(34,197,94,0.1); color: var(--green); }

        .input-group { margin-top: 1rem; }
        .input-group label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            margin-bottom: 0.3rem;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 12px;
            font-size: 0.9rem;
            background: var(--gray-50);
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.3s var(--transition);
        }
        .input-group input:focus, .input-group select:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.08);
            background: white;
        }

        .btn-modal {
            padding: 0.9rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border: none;
            cursor: pointer;
            transition: all 0.3s var(--transition);
            flex: 1;
        }
        .btn-modal.primary { background: var(--black); color: white; }
        .btn-modal.primary:hover { background: var(--gray-800); transform: translateY(-2px); }
        .btn-modal.secondary { background: var(--gray-50); color: var(--gray-600); }
        .btn-modal.secondary:hover { background: var(--gray-200); }

        .modal-actions { display: flex; gap: 0.75rem; margin-top: 1.5rem; }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s var(--transition) forwards;
        }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        .fade-up-d1 { animation-delay: 0.05s; }
        .fade-up-d2 { animation-delay: 0.1s; }

        .alert {
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
        .alert.error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

        @media (max-width: 768px) {
            .table-premium { font-size: 0.7rem; }
            .table-premium thead th { padding: 0.5rem; font-size: 0.5rem; }
            .table-premium tbody td { padding: 0.5rem; }
            .btn-action { padding: 0.2rem 0.5rem; font-size: 0.5rem; }
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

<div class="container-luxury" style="padding-top: 2rem; padding-bottom: 4rem;">

    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:2rem;">
        <div>
            <h1 style="font-family:'Playfair Display',serif; font-size:2.25rem; font-weight:700; letter-spacing:-0.02em;">
                <i class="fas fa-users" style="color:var(--indigo); margin-right:0.5rem;"></i>
                User Management
            </h1>
            <p style="color:var(--gray-400); font-size:0.85rem; margin-top:0.25rem;">Manage all registered users and their credentials.</p>
        </div>
        <button onclick="openAddModal()" class="btn-action primary" style="padding:0.7rem 1.75rem; font-size:0.7rem;">
            <i class="fas fa-plus"></i> Add User
        </button>
    </div>

    <!-- Alerts -->
    <?php if (isset($success)): ?>
        <div class="alert success fade-up fade-up-d1">
            <i class="fas fa-check-circle"></i> <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert error fade-up fade-up-d1">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="card-premium fade-up fade-up-d2">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid rgba(0,0,0,0.04); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
            <span style="font-size:0.8rem; color:var(--gray-400);">
                <i class="fas fa-database" style="margin-right:0.4rem;"></i>
                <?= mysqli_num_rows($users_query) ?> users found
            </span>
            <span style="font-size:0.6rem; color:var(--gray-300);">
                <i class="fas fa-lock"></i> Passwords are stored securely (hashed)
            </span>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Password (Hash)</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    while ($user = mysqli_fetch_assoc($users_query)): 
                        $is_self = $user['id'] == $_SESSION['user_id'];
                    ?>
                        <tr>
                            <td style="font-weight:500; color:var(--gray-400);"><?= $counter++ ?></td>
                            <td style="font-weight:600; color:var(--black);">
                                <?= htmlspecialchars($user['name']) ?>
                                <?php if ($is_self): ?>
                                    <span style="font-size:0.5rem; background:var(--indigo); color:white; padding:0.1rem 0.5rem; border-radius:100px; margin-left:0.3rem;">YOU</span>
                                <?php endif; ?>
                            </td>
                            <td style="color:var(--gray-600);"><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="password-hash"><?= htmlspecialchars(substr($user['password'], 0, 30)) ?>...</span>
                                <button onclick="showPassword('<?= addslashes($user['password']) ?>', '<?= addslashes($user['name']) ?>')" 
                                        style="background:none; border:none; color:var(--indigo); cursor:pointer; font-size:0.6rem; margin-left:0.3rem;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td style="max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--gray-500); font-size:0.75rem;">
                                <?= htmlspecialchars($user['address']) ?>
                            </td>
                            <td style="color:var(--gray-500); font-size:0.75rem;"><?= htmlspecialchars($user['contact']) ?></td>
                            <td>
                                <span class="role-badge <?= $user['role'] ?>"><?= htmlspecialchars($user['role']) ?></span>
                            </td>
                            <td style="font-size:0.7rem; color:var(--gray-400); white-space:nowrap;"><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <div style="display:flex; gap:0.3rem; justify-content:center; flex-wrap:wrap;">
                                    <button onclick="openEditModal(<?= $user['id'] ?>, '<?= addslashes($user['name']) ?>', '<?= addslashes($user['email']) ?>', '<?= addslashes($user['address']) ?>', '<?= addslashes($user['contact']) ?>', '<?= $user['role'] ?>')" 
                                            class="btn-action edit" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button onclick="openResetModal(<?= $user['id'] ?>, '<?= addslashes($user['name']) ?>')" 
                                            class="btn-action reset" title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <?php if (!$is_self): ?>
                                        <a href="?delete=<?= $user['id'] ?>" 
                                           onclick="return confirm('Delete user <?= addslashes($user['name']) ?>? This cannot be undone.')" 
                                           class="btn-action delete" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Note -->
    <div style="margin-top:1.5rem; padding:1rem 1.5rem; background:rgba(99,102,241,0.04); border-radius:16px; border:1px solid rgba(99,102,241,0.06); display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
        <i class="fas fa-info-circle" style="color:var(--indigo); font-size:1.1rem;"></i>
        <span style="font-size:0.8rem; color:var(--gray-500);">
            <strong>Note:</strong> Passwords are stored using bcrypt hashing for security. 
            Click the <i class="fas fa-eye" style="color:var(--indigo);"></i> icon to view the full hash.
            Use the <i class="fas fa-key" style="color:var(--yellow);"></i> button to reset a user's password.
        </span>
    </div>

</div>

<!-- ============================================================
     MODAL 1: ADD USER
     ============================================================ -->
<div class="modal-overlay" id="addModal">
    <div class="modal-content" style="position:relative;">
        <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
        <div class="modal-icon success"><i class="fas fa-user-plus"></i></div>
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; text-align:center;">Add New User</h2>
        <p style="text-align:center; color:var(--gray-400); font-size:0.85rem; margin-top:0.25rem;">Create a new user account</p>

        <form method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe">
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="john@example.com">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="text" name="password" required placeholder="Enter password">
            </div>
            <div class="input-group">
                <label>Address</label>
                <input type="text" name="address" required placeholder="123 Main St">
            </div>
            <div class="input-group">
                <label>Contact Number</label>
                <input type="text" name="contact" required placeholder="09123456789">
            </div>
            <div class="input-group">
                <label>Role</label>
                <select name="role">
                    <option value="customer">Customer</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal secondary" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" name="add_user" class="btn-modal primary"><i class="fas fa-plus"></i> Add User</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL 2: EDIT USER
     ============================================================ -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content" style="position:relative;">
        <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
        <div class="modal-icon info"><i class="fas fa-user-edit"></i></div>
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; text-align:center;">Edit User</h2>
        <p style="text-align:center; color:var(--gray-400); font-size:0.85rem; margin-top:0.25rem;">Update user information</p>

        <form method="POST">
            <input type="hidden" name="user_id" id="editUserId">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" id="editName" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" id="editEmail" required>
            </div>
            <div class="input-group">
                <label>Address</label>
                <input type="text" name="address" id="editAddress" required>
            </div>
            <div class="input-group">
                <label>Contact Number</label>
                <input type="text" name="contact" id="editContact" required>
            </div>
            <div class="input-group">
                <label>Role</label>
                <select name="role" id="editRole">
                    <option value="customer">Customer</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" name="edit_user" class="btn-modal primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL 3: RESET PASSWORD
     ============================================================ -->
<div class="modal-overlay" id="resetModal">
    <div class="modal-content" style="position:relative;">
        <button class="modal-close" onclick="closeModal('resetModal')"><i class="fas fa-times"></i></button>
        <div class="modal-icon" style="background:rgba(245,158,11,0.1); color:var(--yellow);"><i class="fas fa-key"></i></div>
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; text-align:center;">Reset Password</h2>
        <p style="text-align:center; color:var(--gray-400); font-size:0.85rem; margin-top:0.25rem;">Set a new password for <span id="resetUserName" style="font-weight:600; color:var(--black);"></span></p>

        <form method="POST">
            <input type="hidden" name="user_id" id="resetUserId">
            <div class="input-group">
                <label>New Password</label>
                <input type="text" name="new_password" required placeholder="Enter new password">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal secondary" onclick="closeModal('resetModal')">Cancel</button>
                <button type="submit" name="reset_password" class="btn-modal primary"><i class="fas fa-key"></i> Reset Password</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL 4: VIEW PASSWORD HASH
     ============================================================ -->
<div class="modal-overlay" id="passwordModal">
    <div class="modal-content" style="position:relative;">
        <button class="modal-close" onclick="closeModal('passwordModal')"><i class="fas fa-times"></i></button>
        <div class="modal-icon info"><i class="fas fa-lock"></i></div>
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; text-align:center;">Password Hash</h2>
        <p style="text-align:center; color:var(--gray-400); font-size:0.85rem; margin-top:0.25rem;">Hashed password for <span id="passwordUserName" style="font-weight:600; color:var(--black);"></span></p>

        <div style="margin-top:1rem; background:var(--gray-50); border-radius:12px; padding:1rem; border:1px solid rgba(0,0,0,0.03);">
            <div style="font-size:0.6rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.3rem;">Bcrypt Hash</div>
            <code id="passwordHashDisplay" style="font-family:'Courier New',monospace; font-size:0.75rem; word-break:break-all; color:var(--gray-700);"></code>
        </div>

        <div style="margin-top:0.5rem; font-size:0.65rem; color:var(--gray-400);">
            <i class="fas fa-info-circle"></i> This is the hashed version stored in the database. 
            Passwords cannot be decrypted.
        </div>

        <div class="modal-actions">
            <button class="btn-modal primary" onclick="closeModal('passwordModal')">Close</button>
        </div>
    </div>
</div>

<script>
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

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
});

// ============================================================
// ADD MODAL
// ============================================================
function openAddModal() {
    openModal('addModal');
}

// ============================================================
// EDIT MODAL
// ============================================================
function openEditModal(id, name, email, address, contact, role) {
    document.getElementById('editUserId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editAddress').value = address;
    document.getElementById('editContact').value = contact;
    document.getElementById('editRole').value = role;
    openModal('editModal');
}

// ============================================================
// RESET PASSWORD MODAL
// ============================================================
function openResetModal(id, name) {
    document.getElementById('resetUserId').value = id;
    document.getElementById('resetUserName').textContent = name;
    openModal('resetModal');
}

// ============================================================
// VIEW PASSWORD HASH
// ============================================================
function showPassword(hash, name) {
    document.getElementById('passwordHashDisplay').textContent = hash;
    document.getElementById('passwordUserName').textContent = name;
    openModal('passwordModal');
}
</script>

</body>
</html>