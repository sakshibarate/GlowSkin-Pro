<?php
require 'config.php';
if(is_logged_in()) redirect('analysis.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$email=strtolower(trim($_POST['email']??''));$password=$_POST['password']??'';
 $s=db()->prepare("SELECT id,name,email,password_hash,role,status FROM users WHERE email=? LIMIT 1");$s->execute([$email]);$u=$s->fetch();
 if($u && password_verify($password,$u['password_hash']) && $u['status']==='active'){session_regenerate_id(true);$_SESSION['user_id']=(int)$u['id'];$_SESSION['user_name']=$u['name'];$_SESSION['user_email']=$u['email'];$_SESSION['role']=$u['role'];redirect('analysis.php');}
 $error='Email or password is incorrect, or this account is inactive.';
}
$page_title='Sign in';require 'includes/header.php';?>
<div class="auth-wrap"><section class="auth-card"><div class="eyebrow">Your skincare space</div><h1>Welcome back.</h1><p class="muted">Sign in to continue your skin profile.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="field"><label>Email address</label><input type="email" name="email" required value="<?=e($_POST['email']??'')?>"></div><div class="field"><label>Password</label><input type="password" name="password" required></div><button class="btn btn-primary full">Sign in</button></form><p class="helper">New here? <a href="register.php"><u>Create an account</u></a></p><p class="helper"><a href="admin_login.php"><u>Administrator sign in</u></a></p></section></div>
<?php require 'includes/footer.php';?>
