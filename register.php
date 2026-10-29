<?php
require 'config.php';
if (is_logged_in()) redirect('analysis.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();
 $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
 if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Please enter a valid name and email.';
 elseif (strlen($password)<8) $error='Use a password with at least 8 characters.';
 elseif ($password!==$confirm) $error='Passwords do not match.';
 else {
  try {$s=db()->prepare("INSERT INTO users(name,email,password_hash) VALUES(?,?,?)");$s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);flash('Account created. Please sign in.');redirect('login.php');}
  catch(PDOException $ex){$error=$ex->getCode()==='23000'?'That email is already registered.':'Could not create your account. Please try again.';}
 }
}
$page_title='Create account'; require 'includes/header.php';
?>
<div class="auth-wrap"><section class="auth-card"><div class="eyebrow">Welcome to GlowSkin Pro</div><h1>Create your account</h1><p class="muted">Save your skin profile and revisit your results anytime.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="field"><label for="name">Full name</label><input id="name" name="name" maxlength="100" required value="<?=e($_POST['name']??'')?>"></div><div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" required value="<?=e($_POST['email']??'')?>"></div><div class="field"><label for="password">Password</label><input id="password" type="password" name="password" minlength="8" required><div class="helper">At least 8 characters.</div></div><div class="field"><label for="confirm_password">Confirm password</label><input id="confirm_password" type="password" name="confirm_password" minlength="8" required></div><button class="btn btn-primary full" type="submit">Create account</button></form><p class="helper">Already registered? <a href="login.php"><u>Sign in</u></a></p></section></div>
<?php require 'includes/footer.php'; ?>
