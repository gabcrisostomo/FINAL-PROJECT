<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — THREAD & TREND</title>
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

        .about-hero {
            background: var(--black);
            border-radius: 24px;
            padding: 4rem 3.5rem;
            color: white;
            margin: 2rem 0 3rem;
            position: relative;
            overflow: hidden;
        }
        .about-hero::after {
            content: '';
            position: absolute;
            right: -100px;
            top: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.06) 0%, transparent 70%);
        }
        .about-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            position: relative;
            z-index: 2;
            letter-spacing: -0.02em;
        }
        .about-hero p {
            color: rgba(255,255,255,0.4);
            max-width: 500px;
            line-height: 1.8;
            margin-top: 1rem;
            position: relative;
            z-index: 2;
        }
        @media (max-width: 640px) {
            .about-hero { padding: 2rem 1.5rem; border-radius: 16px; }
            .about-hero h1 { font-size: 2rem; }
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }
        @media (max-width: 1024px) { .team-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .team-grid { grid-template-columns: 1fr; } }

        .team-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.4s var(--transition);
            text-align: center;
        }
        .team-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.04);
            border-color: rgba(99,102,241,0.08);
        }
        .team-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--gray-50);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--indigo);
            border: 2px solid var(--gray-100);
        }
        .team-name { font-weight: 600; font-size: 0.95rem; }
        .team-role { font-size: 0.7rem; color: var(--gray-400); margin-top: 0.25rem; }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }
        @media (max-width: 768px) { .values-grid { grid-template-columns: 1fr; } }

        .value-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.04);
            text-align: center;
            transition: all 0.3s var(--transition);
        }
        .value-card:hover { border-color: rgba(99,102,241,0.1); transform: translateY(-2px); }
        .value-card .icon { font-size: 1.5rem; color: var(--indigo); margin-bottom: 0.75rem; }
        .value-card h4 { font-weight: 600; font-size: 0.95rem; }
        .value-card p { font-size: 0.8rem; color: var(--gray-400); margin-top: 0.25rem; line-height: 1.6; }

        .footer-premium {
            background: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.04);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 2.5rem;
        }
        @media (max-width: 768px) { .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; padding: 0 1.25rem; } }
        @media (max-width: 480px) { .footer-grid { grid-template-columns: 1fr; } }

        .footer-brand { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 700; color: var(--black); }
        .footer-desc { font-size: 0.8rem; color: var(--gray-400); line-height: 1.7; margin-top: 0.75rem; max-width: 320px; }
        .footer-social { display: flex; gap: 1rem; margin-top: 1.5rem; }
        .footer-social a { color: var(--gray-400); transition: color 0.3s; font-size: 1.1rem; }
        .footer-social a:hover { color: var(--black); }
        .footer-col h4 { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: var(--black); margin-bottom: 1rem; }
        .footer-col a { display: block; font-size: 0.8rem; color: var(--gray-400); text-decoration: none; padding: 0.35rem 0; transition: color 0.3s; }
        .footer-col a:hover { color: var(--black); }
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2.5rem;
            border-top: 1px solid rgba(0,0,0,0.04);
            font-size: 0.7rem;
            color: var(--gray-400);
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 2.5rem;
            padding-right: 2.5rem;
        }
        @media (max-width: 640px) { .footer-bottom { padding-left: 1.25rem; padding-right: 1.25rem; } }
    </style>
</head>
<body>

<nav class="nav-premium">
    <a href="index.php" class="nav-brand"><span class="dot"></span> THREAD & TREND</a>
    <div style="display:flex; align-items:center; gap:1.5rem;">
        <a href="index.php" style="color:var(--gray-400); text-decoration:none; font-size:0.8rem; transition:color 0.3s;"><i class="fas fa-arrow-left"></i> Home</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <span style="font-size:0.65rem; color:var(--gray-400);"><?= htmlspecialchars($_SESSION['name']) ?></span>
            <a href="logout.php" style="font-size:0.65rem; color:var(--gray-400);">Logout</a>
        <?php else: ?>
            <a href="login.php" style="font-size:0.65rem; color:var(--gray-400);">Sign In</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container-luxury">
    <div class="about-hero">
        <h1>Where vision <br>becomes reality.</h1>
        <p>Thread & Trend merges Japanese precision with New York energy. Each piece is designed to outlast seasons — crafted with intention, built to inspire.</p>
    </div>

    <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; letter-spacing:-0.02em; margin-bottom:0.5rem;">Our Values</h2>
    <p style="color:var(--gray-400); margin-bottom:1.5rem;">The principles that guide everything we do.</p>

    <div class="values-grid">
        <div class="value-card">
            <div class="icon"><i class="fas fa-leaf"></i></div>
            <h4>Sustainability</h4>
            <p>Responsibly sourced, ethically produced. Fashion that respects both people and planet.</p>
        </div>
        <div class="value-card">
            <div class="icon"><i class="fas fa-gem"></i></div>
            <h4>Quality First</h4>
            <p>Premium materials and meticulous craftsmanship in every piece we create.</p>
        </div>
        <div class="value-card">
            <div class="icon"><i class="fas fa-infinity"></i></div>
            <h4>Timeless Design</h4>
            <p>Beyond trends — pieces designed to be loved, worn, and passed down.</p>
        </div>
    </div>

    <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; letter-spacing:-0.02em; margin-top:3rem; margin-bottom:0.5rem;">The Team</h2>
    <p style="color:var(--gray-400); margin-bottom:1.5rem;">The minds behind Thread & Trend.</p>

    <div class="team-grid">
        <div class="team-card">
            <div class="team-avatar">LM</div>
            <div class="team-name">Lexus Medina</div>
            <div class="team-role">Backend Architecture</div>
        </div>
        <div class="team-card">
            <div class="team-avatar">JC</div>
            <div class="team-name">Jonas Crisostomo</div>
            <div class="team-role">Database & Security</div>
        </div>
        <div class="team-card">
            <div class="team-avatar">GC</div>
            <div class="team-name">Gabrielle Crisostomo</div>
            <div class="team-role">Lead UX Designer</div>
        </div>
        <div class="team-card">
            <div class="team-avatar">JB</div>
            <div class="team-name">Joshua Biglete</div>
            <div class="team-role">QA & Operations</div>
        </div>
    </div>
</div>

<footer class="footer-premium">
    <div class="footer-grid">
        <div>
            <div class="footer-brand">THREAD & TREND</div>
            <p class="footer-desc">Premium apparel for the modern individual. Crafted with intention, designed to inspire.</p>
            <div class="footer-social">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Shop</h4>
            <a href="index.php">All Collections</a>
            <a href="index.php?category=Shirts">Shirts</a>
            <a href="index.php?category=Outerwear">Outerwear</a>
            <a href="index.php?category=Hoodie">Hoodies</a>
        </div>
        <div class="footer-col">
            <h4>Company</h4>
            <a href="about.php">About Us</a>
            <a href="#">Careers</a>
            <a href="#">Sustainability</a>
            <a href="#">Press</a>
        </div>
        <div class="footer-col">
            <h4>Support</h4>
            <a href="#">FAQ</a>
            <a href="#">Shipping</a>
            <a href="#">Returns</a>
            <a href="#">Contact</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2026 THREAD & TREND — Luxury Apparel. All rights reserved.</p>
        <p style="margin-top:0.5rem; font-size:0.6rem; color:var(--gray-300);">Academic Project · TC21 · BSITCST · Group 8</p>
    </div>
</footer>

</body>
</html>