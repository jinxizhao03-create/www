<?php
require __DIR__ . '/config/functions.php';
ensure_install();
$db = get_db();
$app = app_config();
$categories = $db->query("SELECT * FROM prompt_categories WHERE type='generate' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$article = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $cid = intval($_POST['category_id']);
    $title = trim($_POST['title']);
    $stmt = $db->prepare('SELECT prompt FROM prompt_categories WHERE id=?');
    $stmt->execute(array($cid));
    $prompt = $stmt->fetchColumn();
    $api = $db->query('SELECT * FROM api_configs WHERE is_default=1 ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    $article = call_llm($api ? $api['provider'] : 'siliconflow', $api ? $api['api_key'] : '', $prompt ? $prompt : '根据标题生成文章', $title);
}
?>
<!doctype html><html><head><meta charset="utf-8"><title><?php echo esc($app['site_name']); ?></title></head>
<body>
<h1><?php echo esc($app['site_name']); ?></h1>
<a href="/user/rewrite.php">文章改写</a> | <a href="/admin/login.php">后台管理</a>
<h2>标题生成文章</h2>
<form method="post">
标题：<input name="title" required style="width:400px"><br>
分类：<select name="category_id"><?php foreach($categories as $c){echo '<option value="'.$c['id'].'">'.esc($c['name']).'</option>';} ?></select>
<button type="submit">生成</button>
</form>
<textarea style="width:90%;height:300px"><?php echo esc($article); ?></textarea>
</body></html>
