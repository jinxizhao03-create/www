<?php session_start(); require __DIR__ . '/../config/functions.php'; if(empty($_SESSION['admin'])) die('请登录'); $db=get_db();
if($_SERVER['REQUEST_METHOD']==='POST'){$db->prepare('INSERT INTO prompt_categories(type,name,prompt,created_at) VALUES(?,?,?,NOW())')->execute(array($_POST['type'],$_POST['name'],$_POST['prompt']));}
$list=$db->query('SELECT * FROM prompt_categories ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?><h2>指令词管理（生成/改写分离）</h2><form method="post">类型<select name="type"><option value="generate">标题生成</option><option value="rewrite">文章改写</option></select> 名称<input name="name"> <br>指令词<br><textarea name="prompt" style="width:600px;height:120px"></textarea><br><button>保存</button></form>
<table border="1"><?php foreach($list as $r){echo '<tr><td>'.$r['id'].'</td><td>'.$r['type'].'</td><td>'.esc($r['name']).'</td><td>'.esc($r['prompt']).'</td></tr>';}?></table>
