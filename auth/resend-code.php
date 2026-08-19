<?php
require __DIR__.'/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: verify.php'); exit; }
check_csrf();
$id=(int)($_SESSION['verify_user_id']??0);
if(!$id){flash('error','Start by creating an account.');header('Location: register.php');exit;}
$last=db()->prepare('SELECT created_at FROM email_verifications WHERE user_id=? ORDER BY id DESC LIMIT 1');$last->execute([$id]);$at=$last->fetchColumn();
if($at&&strtotime($at)>time()-60){flash('warning','Wait one minute before requesting another code.');header('Location: verify.php');exit;}
$code=verification_code();
db()->prepare('UPDATE email_verifications SET expires_at=NOW(),verified_at=NOW() WHERE user_id=? AND verified_at IS NULL')->execute([$id]);
db()->prepare('INSERT INTO email_verifications(user_id,verification_code_hash,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 15 MINUTE))')->execute([$id,password_hash($code,PASSWORD_DEFAULT)]);
$_SESSION['dev_verification_code']=$code;flash('success','A new verification code was generated.');header('Location: verify.php');
