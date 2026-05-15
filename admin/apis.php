<?php session_start(); require __DIR__ . '/../config/functions.php'; if(empty($_SESSION['admin'])) die('请登录'); $db=get_db();
if($_SERVER['REQUEST_METHOD']==='POST'){
$db->exec('UPDATE api_configs SET is_default=0');
$db->prepare('INSERT INTO api_configs(provider,api_key,base_url,is_default,created_at) VALUES(?,?,?,?,NOW())')->execute(array($_POST['provider'],$_POST['api_key'],$_POST['base_url'],1));
}
$list=$db->query('SELECT * FROM api_configs ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?><h2>API配置</h2><form method="post">服务商<select name="provider"><option value="siliconflow">硅基流动(默认)</option><option value="chatgpt">ChatGPT</option><option value="deepseek">DeepSeek</option><option value="doubao">豆包</option><option value="qwen">通义千问</option></select><br>API Key<input name="api_key" style="width:500px"><br>Base URL<input name="base_url" style="width:500px"><br><button>保存并设为默认</button></form>
<table border="1"><?php foreach($list as $r){echo '<tr><td>'.$r['provider'].'</td><td>'.($r['is_default']?'默认':'').'</td><td>'.esc(substr($r['api_key'],0,8)).'***</td></tr>'; }?></table>
