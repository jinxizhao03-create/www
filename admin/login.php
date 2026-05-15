<?php
session_start();
require __DIR__ . '/../config/functions.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
$db=get_db();$stmt=$db->prepare('SELECT * FROM admins WHERE username=?');$stmt->execute(array($_POST['username']));$u=$stmt->fetch(PDO::FETCH_ASSOC);
if($u && password_verify($_POST['password'],$u['password'])){$_SESSION['admin']=$u['username'];header('Location: /admin/index.php');exit;} else $msg='登录失败';
}
?><h1>后台登录</h1><p><?php echo esc($msg);?></p><form method="post">账号<input name="username"><br>密码<input type="password" name="password"><br><button>登录</button></form>
