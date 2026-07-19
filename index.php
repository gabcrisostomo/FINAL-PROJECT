<?php
include 'config.php';

$cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM products");
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$where_clause = $category_filter ? "WHERE category = '$category_filter'" : '';
$products_query = mysqli_query($conn, "SELECT * FROM products $where_clause ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THREAD & TREND — Luxury Apparel</title>
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
            --gold: #d4a574;
            --transition: cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 32px rgba(0,0,0,0.06);
            --shadow-lg: 0 24px 64px rgba(0,0,0,0.08);
            --shadow-xl: 0 40px 80px rgba(0,0,0,0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-50);
            color: var(--black);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        .container-luxury { max-width: 1440px; margin: 0 auto; padding: 0 2.5rem; }
        @media (max-width: 1024px) { .container-luxury { padding: 0 2rem; } }
        @media (max-width: 640px) { .container-luxury { padding: 0 1.25rem; } }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--gray-50); }
        ::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 100px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gray-400); }

        ::selection { background: var(--indigo); color: white; }

        /* ===== NAVIGATION ===== */
        .nav-premium {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            transition: all 0.4s var(--transition);
            height: 76px;
        }
        .nav-premium.scrolled {
            background: rgba(255,255,255,0.96);
            box-shadow: var(--shadow-sm);
        }
        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 76px;
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 2.5rem;
        }
        @media (max-width: 640px) { .nav-inner { padding: 0 1.25rem; } }

        .nav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--black);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .nav-brand .mark {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--indigo);
            position: relative;
        }
        .nav-brand .mark::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            border: 1.5px solid var(--indigo);
            opacity: 0.3;
            animation: pulse-ring 2s ease-in-out infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.4); opacity: 0; }
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }
        .nav-links a {
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--gray-500);
            text-decoration: none;
            transition: all 0.3s var(--transition);
            position: relative;
            padding: 0.25rem 0;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1.5px;
            background: var(--black);
            transition: width 0.4s var(--transition);
        }
        .nav-links a:hover { color: var(--black); }
        .nav-links a:hover::after { width: 100%; }
        .nav-links a.active { color: var(--black); }
        .nav-links a.active::after { width: 100%; }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .nav-actions a {
            color: var(--gray-500);
            transition: all 0.3s var(--transition);
            font-size: 1.1rem;
            position: relative;
            text-decoration: none;
        }
        .nav-actions a:hover { color: var(--black); transform: translateY(-1px); }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: var(--indigo);
            color: white;
            font-size: 0.5rem;
            font-weight: 700;
            padding: 0.1rem 0.4rem;
            border-radius: 100px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(99,102,241,0.3);
        }

        .btn-nav-login {
            background: var(--black);
            color: white !important;
            padding: 0.45rem 1.25rem;
            border-radius: 100px;
            font-size: 0.6rem !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            transition: all 0.3s var(--transition);
        }
        .btn-nav-login:hover {
            background: var(--gray-800);
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .nav-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0.5rem;
        }
        .nav-toggle span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--black);
            transition: all 0.3s var(--transition);
            border-radius: 2px;
        }
        .nav-toggle.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .nav-toggle.active span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .nav-toggle.active span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }

        @media (max-width: 1024px) {
            .nav-links { gap: 1.5rem; }
            .nav-links a { font-size: 0.6rem; }
        }
        @media (max-width: 768px) {
            .nav-toggle { display: flex; }
            .nav-links {
                display: none;
                position: absolute;
                top: 76px;
                left: 0;
                right: 0;
                background: rgba(255,255,255,0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: 2rem 2.5rem;
                gap: 1.5rem;
                border-bottom: 1px solid rgba(0,0,0,0.04);
                box-shadow: var(--shadow-md);
            }
            .nav-links.open { display: flex; }
            .nav-links a { font-size: 0.85rem; }
            .nav-actions .btn-nav-login { padding: 0.4rem 1rem; font-size: 0.55rem !important; }
        }

        /* ===== HERO ===== */
        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(165deg, #080808 0%, #1a1a1a 45%, #282828 100%);
            overflow: hidden;
            margin-top: 76px;
            padding: 6rem 0 4rem;
        }
        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(99,102,241,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(99,102,241,0.04) 0%, transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(99,102,241,0.03) 0%, transparent 50%);
            pointer-events: none;
        }
        .hero-glow-orb {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.1) 0%, transparent 70%);
            top: -300px;
            right: -200px;
            animation: floatGlow 12s ease-in-out infinite;
        }
        @keyframes floatGlow {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
            33% { transform: translate(30px, -20px) scale(1.1); opacity: 0.8; }
            66% { transform: translate(-20px, 30px) scale(0.9); opacity: 0.6; }
        }

        .hero-content { position: relative; z-index: 2; width: 100%; padding: 2rem 0; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.06);
            padding: 0.5rem 1.5rem 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.6rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            margin-bottom: 2.5rem;
        }
        .hero-badge .badge-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--indigo);
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(0.8); }
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(3.5rem, 12vw, 8rem);
            font-weight: 700;
            line-height: 1.02;
            color: #ffffff;
            max-width: 900px;
            letter-spacing: -0.02em;
        }
        .hero-title .gradient-text {
            background: linear-gradient(135deg, #a5b4fc, #818cf8, #6366f1, #4f46e5);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 6s ease-in-out infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.35);
            max-width: 480px;
            line-height: 1.9;
            margin-top: 1.75rem;
            font-weight: 300;
            letter-spacing: 0.01em;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2.75rem;
        }
        .btn-hero-primary {
            background: #ffffff;
            color: var(--black);
            padding: 1rem 2.75rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            transition: all 0.4s var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(255,255,255,0.08);
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 24px 48px rgba(255,255,255,0.15);
        }
        .btn-hero-secondary {
            background: transparent;
            color: #ffffff;
            padding: 1rem 2.25rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            transition: all 0.4s var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .btn-hero-secondary:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.25);
            transform: translateY(-3px);
        }

        .hero-stats {
            display: flex;
            gap: 4.5rem;
            margin-top: 4rem;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(255,255,255,0.04);
        }
        .hero-stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .hero-stat-label {
            font-size: 0.6rem;
            color: rgba(255,255,255,0.25);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-top: 0.25rem;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .hero-section { min-height: auto; padding: 4rem 0 3rem; margin-top: 0; }
            .hero-title { font-size: clamp(2.5rem, 14vw, 3.5rem); }
            .hero-subtitle { font-size: 1rem; max-width: 100%; }
            .hero-stats { gap: 2rem; flex-wrap: wrap; }
            .hero-stat-number { font-size: 1.4rem; }
            .hero-actions .btn-hero-primary,
            .hero-actions .btn-hero-secondary {
                width: 100%;
                justify-content: center;
                padding: 0.9rem 1.5rem;
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.15);
            font-size: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            animation: bounceDown 2.5s ease-in-out infinite;
        }
        .scroll-indicator .mouse {
            width: 20px;
            height: 32px;
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 100px;
            position: relative;
        }
        .scroll-indicator .mouse::after {
            content: '';
            position: absolute;
            top: 6px;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 8px;
            border-radius: 100px;
            background: rgba(255,255,255,0.2);
            animation: scrollWheel 2s ease-in-out infinite;
        }
        @keyframes scrollWheel {
            0%, 100% { transform: translateX(-50%) translateY(0); opacity: 1; }
            50% { transform: translateX(-50%) translateY(10px); opacity: 0.2; }
        }
        @keyframes bounceDown {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(6px); }
        }
        @media (max-width: 768px) { .scroll-indicator { display: none; } }

        /* ===== SECTION HEADERS ===== */
        .section-header-premium {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.04);
        }
        .section-title-group h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--black);
            letter-spacing: -0.02em;
        }
        .section-title-group .sub {
            font-size: 0.8rem;
            color: var(--gray-400);
            font-weight: 300;
            margin-top: 0.25rem;
        }
        .section-link-premium {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--black);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.4s var(--transition);
            white-space: nowrap;
        }
        .section-link-premium:hover { color: var(--indigo); gap: 0.85rem; }
        @media (max-width: 640px) {
            .section-header-premium { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
            .section-title-group h2 { font-size: 1.75rem; }
        }

        /* ===== CATEGORY FILTER ===== */
        .filter-luxury {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 3rem;
            padding: 0.25rem 0;
        }
        .filter-luxury a {
            padding: 0.6rem 1.75rem;
            border-radius: 100px;
            border: 1px solid rgba(0,0,0,0.05);
            background: transparent;
            font-size: 0.65rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
            cursor: pointer;
            transition: all 0.3s var(--transition);
            text-decoration: none;
        }
        .filter-luxury a:hover {
            border-color: var(--black);
            color: var(--black);
            transform: translateY(-1px);
        }
        .filter-luxury a.active {
            background: var(--black);
            color: #ffffff;
            border-color: var(--black);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        @media (max-width: 768px) {
            .filter-luxury { gap: 0.4rem; }
            .filter-luxury a { padding: 0.4rem 1.25rem; font-size: 0.55rem; }
        }

        /* ===== PRODUCT GRID ===== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 1200px) { .product-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .product-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; } }
        @media (max-width: 480px) { .product-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; } }

        .product-card-premium {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.5s var(--transition);
            border: 1px solid rgba(0,0,0,0.03);
            position: relative;
            will-change: transform;
        }
        .product-card-premium:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(99,102,241,0.1);
        }

        .product-image-wrap {
            position: relative;
            aspect-ratio: 3/4;
            background: var(--gray-50);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow: hidden;
        }
        .product-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.7s var(--transition);
        }
        .product-card-premium:hover .product-image-wrap img {
            transform: scale(1.06);
        }

        .product-badge-premium {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            font-size: 0.5rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            z-index: 2;
        }
        .badge-low { background: #f59e0b; color: #ffffff; }
        .badge-sold { background: var(--gray-600); color: #ffffff; }
        .badge-new { background: var(--black); color: #ffffff; }

        .product-wishlist {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 2;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            transition: all 0.3s var(--transition);
        }
        .product-wishlist:hover {
            background: #ffffff;
            color: #ef4444;
            transform: scale(1.1);
            box-shadow: var(--shadow-sm);
        }

        .product-quick-add {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem 1.25rem;
            background: linear-gradient(transparent, rgba(0,0,0,0.55));
            opacity: 0;
            transform: translateY(12px);
            transition: all 0.5s var(--transition);
        }
        .product-card-premium:hover .product-quick-add {
            opacity: 1;
            transform: translateY(0);
        }
        .product-quick-add button {
            width: 100%;
            padding: 0.7rem;
            border: none;
            border-radius: 100px;
            background: #ffffff;
            color: var(--black);
            font-weight: 600;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            transition: all 0.3s var(--transition);
        }
        .product-quick-add button:hover {
            background: var(--black);
            color: #ffffff;
            transform: scale(1.02);
        }

        .product-info-premium {
            padding: 1rem 1.25rem 1.25rem;
        }
        .product-category-premium {
            font-size: 0.55rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--gray-400);
        }
        .product-name-premium {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--black);
            margin-top: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }
        .product-row-premium {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(0,0,0,0.04);
        }
        .product-price-premium {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--black);
        }
        .product-stock-premium {
            font-size: 0.55rem;
            color: var(--gray-400);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ===== FEATURED SECTION ===== */
        .featured-section {
            background: var(--black);
            border-radius: 24px;
            padding: 4rem 3.5rem;
            margin-top: 4rem;
            position: relative;
            overflow: hidden;
        }
        .featured-section::before {
            content: '';
            position: absolute;
            right: -200px;
            top: -200px;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.06) 0%, transparent 70%);
        }
        .featured-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .featured-text .label {
            font-size: 0.55rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: rgba(255,255,255,0.25);
        }
        .featured-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: #ffffff;
            margin-top: 0.5rem;
            line-height: 1.1;
        }
        .featured-text p {
            color: rgba(255,255,255,0.35);
            line-height: 1.8;
            margin-top: 1rem;
            max-width: 400px;
        }
        .featured-text .btn-featured {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--indigo-light);
            font-weight: 600;
            font-size: 0.75rem;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 1.5rem;
            transition: gap 0.3s var(--transition);
        }
        .featured-text .btn-featured:hover { gap: 1.1rem; }

        .featured-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .featured-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.04);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s var(--transition);
        }
        .featured-item:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.08);
            transform: translateY(-4px);
        }
        .featured-item .number {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--indigo-light);
        }
        .featured-item .desc {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.3);
            margin-top: 0.25rem;
        }

        @media (max-width: 1024px) {
            .featured-content { grid-template-columns: 1fr; gap: 2rem; }
            .featured-text h2 { font-size: 2.25rem; }
        }
        @media (max-width: 640px) {
            .featured-section { padding: 2rem 1.5rem; border-radius: 16px; }
            .featured-grid { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
            .featured-text h2 { font-size: 1.75rem; }
            .featured-item { padding: 1rem; }
            .featured-item .number { font-size: 1.5rem; }
        }

        /* ===== BRAND STORY ===== */
        .brand-story-premium {
            background: var(--gray-50);
            border-radius: 24px;
            padding: 4rem 3.5rem;
            margin-top: 4rem;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .brand-story-premium .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .brand-story-premium .label {
            font-size: 0.55rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--gray-400);
        }
        .brand-story-premium h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: var(--black);
            margin-top: 0.5rem;
            line-height: 1.1;
        }
        .brand-story-premium p {
            color: var(--gray-500);
            line-height: 1.8;
            margin-top: 1rem;
            max-width: 440px;
        }
        .brand-story-premium .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        .brand-story-premium .stat-item {
            border-top: 2px solid var(--black);
            padding-top: 1.25rem;
        }
        .brand-story-premium .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--black);
        }
        .brand-story-premium .stat-label {
            font-size: 0.65rem;
            color: var(--gray-400);
            font-weight: 400;
            margin-top: 0.25rem;
        }

        @media (max-width: 1024px) {
            .brand-story-premium .grid { grid-template-columns: 1fr; gap: 2rem; }
            .brand-story-premium h2 { font-size: 2.25rem; }
            .brand-story-premium .stats { grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        }
        @media (max-width: 640px) {
            .brand-story-premium { padding: 2rem 1.5rem; border-radius: 16px; }
            .brand-story-premium h2 { font-size: 1.75rem; }
            .brand-story-premium .stats { grid-template-columns: 1fr 1fr; gap: 1rem; }
            .brand-story-premium .stat-number { font-size: 1.5rem; }
        }

        /* ===== FOOTER ===== */
        .footer-premium {
            background: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.04);
            padding: 4rem 0 2rem;
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
        .footer-brand-premium {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--black);
        }
        .footer-desc-premium {
            font-size: 0.8rem;
            color: var(--gray-400);
            line-height: 1.7;
            margin-top: 0.75rem;
            max-width: 320px;
        }
        .footer-social-premium {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .footer-social-premium a {
            color: var(--gray-400);
            transition: all 0.3s var(--transition);
            font-size: 1.1rem;
        }
        .footer-social-premium a:hover { color: var(--black); transform: translateY(-2px); }

        .footer-col-premium h4 {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--black);
            margin-bottom: 1rem;
        }
        .footer-col-premium a {
            display: block;
            font-size: 0.8rem;
            color: var(--gray-400);
            text-decoration: none;
            padding: 0.35rem 0;
            transition: color 0.3s var(--transition);
        }
        .footer-col-premium a:hover { color: var(--black); }

        .footer-bottom-premium {
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

        @media (max-width: 1024px) {
            .footer-grid { grid-template-columns: 1fr 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; padding: 0 1.25rem; }
        }
        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .footer-bottom-premium { padding-left: 1.25rem; padding-right: 1.25rem; }
        }

        /* ===== ANIMATIONS ===== */
        .fade-up {
            opacity: 0;
            transform: translateY(40px);
            animation: fadeUp 0.9s var(--transition) forwards;
        }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up-d1 { animation-delay: 0.08s; }
        .fade-up-d2 { animation-delay: 0.16s; }
        .fade-up-d3 { animation-delay: 0.24s; }
        .fade-up-d4 { animation-delay: 0.32s; }
        .fade-up-d5 { animation-delay: 0.40s; }
        .fade-up-d6 { animation-delay: 0.48s; }
        .fade-up-d7 { animation-delay: 0.56s; }
        .fade-up-d8 { animation-delay: 0.64s; }
    </style>
</head>
<body>

<!-- ===== NAVIGATION ===== -->
<nav class="nav-premium" id="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-brand">
            <span class="mark"></span>
            THREAD & TREND
        </a>

        <div class="nav-links" id="navLinks">
            <a href="index.php" class="active">Home</a>
            <a href="#catalog">Catalog</a>
            <a href="about.php">About</a>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="dashboard.php">Studio</a>
            <?php endif; ?>
        </div>

        <div class="nav-actions">
            <a href="cart.php" class="cart-count">
                <i class="fas fa-shopping-bag"></i>
                <?php 
                $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                if($cart_count > 0): 
                ?>
                    <span class="cart-badge"><?= $cart_count ?></span>
                <?php endif; ?>
            </a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span style="font-size:0.65rem; color:var(--gray-500); font-weight:500;"><?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="logout.php" style="font-size:0.65rem; color:var(--gray-400);">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-nav-login">Sign In</a>
            <?php endif; ?>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero-section">
    <div class="hero-pattern"></div>
    <div class="hero-glow-orb"></div>
    <div class="container-luxury hero-content">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <div class="hero-badge">
                    <span class="badge-dot"></span>
                    Spring / Summer 2026 Collection
                </div>
                <h1 class="hero-title">
                    Where <br><span class="gradient-text">Craft</span> Meets <br>Expression
                </h1>
                <p class="hero-subtitle">
                    Premium apparel designed for the modern individual. 
                    Each piece tells a story of quality, comfort, and timeless style.
                </p>
                <div class="hero-actions">
                    <a href="#catalog" class="btn-hero-primary">
                        Explore Collection <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
                    </a>
                    <a href="about.php" class="btn-hero-secondary">
                        Our Story <i class="fas fa-chevron-right" style="font-size:0.55rem;"></i>
                    </a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-number">6+</div>
                        <div class="hero-stat-label">Premium Collections</div>
                    </div>
                    <div>
                        <div class="hero-stat-number">100%</div>
                        <div class="hero-stat-label">Ethically Sourced</div>
                    </div>
                    <div>
                        <div class="hero-stat-number">24/7</div>
                        <div class="hero-stat-label">Concierge Support</div>
                    </div>
                </div>
            </div>
            <div class="hidden lg:flex justify-center">
                <div style="width:300px; height:300px; border-radius:50%; border:1px solid rgba(255,255,255,0.04); display:flex; align-items:center; justify-content:center; animation: spin 30s linear infinite;">
                    <div style="width:200px; height:200px; border-radius:50%; border:1px solid rgba(255,255,255,0.03); display:flex; align-items:center; justify-content:center;">
                        <span style="font-family:'Playfair Display',serif; font-size:4rem; font-weight:700; color:rgba(255,255,255,0.04); letter-spacing:0.1em;">T&T</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <div class="mouse"></div>
        <span>Scroll</span>
    </div>
</section>

<!-- ===== CATALOG ===== -->
<div class="container-luxury" id="catalog" style="padding-top: 4rem;">

    <!-- Section Header -->
    <div class="section-header-premium">
        <div class="section-title-group">
            <h2>The Collection</h2>
            <div class="sub">Curated essentials for the discerning</div>
        </div>
        <a href="index.php" class="section-link-premium">
            View All <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Category Filter -->
    <div class="filter-luxury">
        <a href="index.php" class="<?= !$category_filter ? 'active' : '' ?>">All</a>
        <?php 
        mysqli_data_seek($cat_query, 0);
        while($cat = mysqli_fetch_assoc($cat_query)): 
        ?>
            <a href="index.php?category=<?= urlencode($cat['category']) ?>" 
               class="<?= $category_filter === $cat['category'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['category']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <!-- Product Grid -->
    <?php if (mysqli_num_rows($products_query) == 0): ?>
        <div class="text-center py-24 bg-white rounded-3xl border border-gray-100">
            <i class="fas fa-tshirt" style="font-size:3rem; color:var(--gray-300); margin-bottom:1.5rem;"></i>
            <h3 style="font-size:1.25rem; font-weight:700; color:var(--gray-600);">No products in this category</h3>
            <p style="color:var(--gray-400); margin-top:0.5rem;">Explore our full collection</p>
            <a href="index.php" style="display:inline-block; margin-top:1.5rem; background:var(--black); color:white; padding:0.75rem 2.5rem; border-radius:100px; font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; text-decoration:none; transition:background 0.3s;">View All</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php 
            $delay = 0;
            while($product = mysqli_fetch_assoc($products_query)): 
                $delay += 0.06;
                $is_low = $product['stock'] <= 5 && $product['stock'] > 0;
                $is_sold = $product['stock'] == 0;
                
                // Find the correct image path
                $image_found = false;
                $image_src = '';
                if (!empty($product['image'])) {
                    if (file_exists($product['image'])) {
                        $image_src = $product['image'];
                        $image_found = true;
                    } elseif (file_exists('images/' . basename($product['image']))) {
                        $image_src = 'images/' . basename($product['image']);
                        $image_found = true;
                    } elseif (file_exists(basename($product['image']))) {
                        $image_src = basename($product['image']);
                        $image_found = true;
                    }
                }
            ?>
                <div class="product-card-premium fade-up" style="animation-delay: <?= min($delay, 0.5) ?>s;">
                    <div class="product-image-wrap">
                        <?php if ($image_found): ?>
                            <img src="<?= htmlspecialchars($image_src) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <div style="text-align:center; color:var(--gray-300); font-size:0.6rem; text-transform:uppercase; letter-spacing:0.1em;">
                                <i class="fas fa-image" style="display:block; font-size:2rem; margin-bottom:0.5rem; color:var(--gray-200);"></i>
                                No Image
                            </div>
                        <?php endif; ?>

                        <?php if ($is_sold): ?>
                            <span class="product-badge-premium badge-sold">Sold Out</span>
                        <?php elseif ($is_low): ?>
                            <span class="product-badge-premium badge-low">Low Stock</span>
                        <?php else: ?>
                            <span class="product-badge-premium badge-new">New</span>
                        <?php endif; ?>

                        <button class="product-wishlist" aria-label="Add to wishlist">
                            <i class="far fa-heart"></i>
                        </button>

                        <div class="product-quick-add">
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <?php if (!$is_sold): ?>
                                    <button type="submit" name="add_to_cart">
                                        <i class="fas fa-plus" style="margin-right:0.5rem; font-size:0.5rem;"></i>
                                        Quick Add
                                    </button>
                                <?php else: ?>
                                    <button type="button" disabled style="background:#e5e7eb; color:#9ca3af; cursor:not-allowed;">
                                        Unavailable
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <div class="product-info-premium">
                        <div class="product-category-premium"><?= htmlspecialchars($product['category']) ?></div>
                        <div class="product-name-premium"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-row-premium">
                            <span class="product-price-premium">₱<?= number_format($product['price'], 2) ?></span>
                            <span class="product-stock-premium">
                                <i class="fas fa-box" style="margin-right:0.3rem;"></i>
                                <?= htmlspecialchars($product['stock']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <!-- ===== FEATURED SECTION ===== -->
    <div class="featured-section">
        <div class="featured-content">
            <div class="featured-text">
                <div class="label">Editor's Pick</div>
                <h2>Designed for <br>the long run.</h2>
                <p>Every garment is crafted with intention — using premium materials, timeless silhouettes, and an unwavering commitment to quality.</p>
                <a href="about.php" class="btn-featured">
                    Discover More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="featured-grid">
                <div class="featured-item">
                    <div class="number">100+</div>
                    <div class="desc">Premium Fabrics</div>
                </div>
                <div class="featured-item">
                    <div class="number">4.9★</div>
                    <div class="desc">Customer Rating</div>
                </div>
                <div class="featured-item">
                    <div class="number">1,200+</div>
                    <div class="desc">Happy Clients</div>
                </div>
                <div class="featured-item">
                    <div class="number">100%</div>
                    <div class="desc">Ethical Sourcing</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== BRAND STORY ===== -->
    <div class="brand-story-premium">
        <div class="grid">
            <div>
                <div class="label">Why Thread & Trend</div>
                <h2>Where vision <br>becomes reality.</h2>
                <p>We believe that great design transcends trends. Our pieces are created to be lived in, loved, and passed down. This is fashion with purpose.</p>
                <a href="about.php" class="section-link-premium" style="margin-top:1.5rem; display:inline-flex;">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div>
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number">6+</div>
                        <div class="stat-label">Collections</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Concierge</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Sustainable</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer-premium">
    <div class="footer-grid">
        <div>
            <div class="footer-brand-premium">THREAD & TREND</div>
            <p class="footer-desc-premium">Premium apparel for the modern individual. Crafted with intention, designed to inspire.</p>
            <div class="footer-social-premium">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-x-twitter"></i></a>
            </div>
        </div>
        <div class="footer-col-premium">
            <h4>Shop</h4>
            <a href="index.php">All Collections</a>
            <a href="index.php?category=Shirts">Shirts</a>
            <a href="index.php?category=Outerwear">Outerwear</a>
            <a href="index.php?category=Hoodie">Hoodies</a>
        </div>
        <div class="footer-col-premium">
            <h4>Company</h4>
            <a href="about.php">About Us</a>
            <a href="#">Careers</a>
            <a href="#">Sustainability</a>
            <a href="#">Press</a>
        </div>
        <div class="footer-col-premium">
            <h4>Support</h4>
            <a href="#">FAQ</a>
            <a href="#">Shipping</a>
            <a href="#">Returns</a>
            <a href="#">Contact</a>
        </div>
    </div>
    <div class="footer-bottom-premium">
        <p>© 2026 THREAD & TREND — Luxury Apparel. All rights reserved.</p>
        <p style="margin-top:0.5rem; font-size:0.6rem; color:var(--gray-300);">Academic Project · TC21 · BSITCST · Group 8</p>
    </div>
</footer>

<!-- ===== SCRIPTS ===== -->
<script>
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');
    toggle.addEventListener('click', function() {
        links.classList.toggle('open');
        this.classList.toggle('active');
    });
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            links.classList.remove('open');
            toggle.classList.remove('active');
        });
    });

    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    });

    document.querySelectorAll('.product-wishlist').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
            icon.style.color = icon.classList.contains('fas') ? '#ef4444' : '';
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>

</body>
</html>