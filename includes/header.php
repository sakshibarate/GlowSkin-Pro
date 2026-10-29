<?php
$page_title = $page_title ?? 'GlowSkin Pro';
$active = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> | GlowSkin Pro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
  <a class="brand" href="index.php"><span class="brand-mark">G</span><span>GlowSkin <small>PRO</small></span></a>
  <button class="nav-toggle" aria-label="Toggle navigation" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
  <nav class="nav-links">
    <a href="index.php">Home</a>
    <?php if (is_logged_in()): ?>
      <a href="analysis.php">Skin profile</a><a href="recommendations.php">Recommendations</a><a href="history.php">My history</a>
      <a href="profile.php">Profile</a><a class="nav-cta" href="logout.php">Sign out</a>
    <?php else: ?>
      <a href="login.php">Sign in</a><a class="nav-cta" href="register.php">Get started</a>
    <?php endif; ?>
  </nav>
</header>
<main class="main-shell">
<?php show_flash(); ?>
