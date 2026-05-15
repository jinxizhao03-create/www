<?php
require __DIR__ . '/../config/functions.php';
ensure_install();
$db = get_db();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $name = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, array('txt','doc','docx'))) die('仅支持TXT/Word');
    $content = file_get_contents($tmp);
    $title = isset($_POST['title']) ? $_POST['title'] : $name;
    $stmt = $db->prepare('INSERT INTO rewrite_tasks(title,category_id,original_content,status,origin_words,created_at,updated_at) VALUES(?,?,?,?,?,NOW(),NOW())');
    $stmt->execute(array($title, intval($_POST['category_id']), $content, 'pending', mb_strlen($content,'UTF-8')));
    $msg = '导入成功';
}
if (isset($_GET['start'])) {
    $id = intval($_GET['start']);
    $db->prepare("UPDATE rewrite_tasks SET status='processing',updated_at=NOW() WHERE id=?")->execute(array($id));
    $task = $db->prepare('SELECT t.*,p.prompt FROM rewrite_tasks t LEFT JOIN prompt_categories p ON t.category_id=p.id WHERE t.id=?');
    $task->execute(array($id));
    $row = $task->fetch(PDO::FETCH_ASSOC);
    $api = $db->query('SELECT * FROM api_configs WHERE is_default=1 ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    $newContent = call_llm($api ? $api['provider'] : 'siliconflow', $api ? $api['api_key'] : '', $row['prompt'] ? $row['prompt'] : '改写文章', $row['original_content']);
    $db->prepare("UPDATE rewrite_tasks SET rewritten_content=?,status='done',rewritten_words=?,updated_at=NOW() WHERE id=?")
        ->execute(array($newContent, mb_strlen($newContent,'UTF-8'), $id));
    header('Location: rewrite.php');exit;
}
$cats = $db->query("SELECT * FROM prompt_categories WHERE type='rewrite' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$list = $db->query("SELECT * FROM rewrite_tasks ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><head><meta charset="utf-8"><title>文章改写</title></head><body>
<h1>文章改写</h1><a href="/index.php">返回首页</a>
<p><?php echo esc($msg); ?></p>
<form method="post" enctype="multipart/form-data">
标题：<input name="title">
分类：<select name="category_id"><?php foreach($cats as $c){echo '<option value="'.$c['id'].'">'.esc($c['name']).'</option>';} ?></select>
文件：<input type="file" name="file" required>
<button>导入并创建任务</button></form>
<table border="1" cellpadding="6"><tr><th>ID</th><th>标题</th><th>状态</th><th>字数</th><th>操作</th></tr>
<?php foreach($list as $r){echo '<tr><td>'.$r['id'].'</td><td>'.esc($r['title']).'</td><td>'.$r['status'].'</td><td>'.$r['origin_words'].'/'.$r['rewritten_words'].'</td><td>';
if($r['status']!='done') echo '<a href="?start='.$r['id'].'">开始改写</a> ';
if($r['status']=='done') echo '<a href="preview.php?id='.$r['id'].'" target="_blank">预览</a> <a href="edit.php?id='.$r['id'].'">编辑</a>';
echo '</td></tr>'; } ?>
</table></body></html>
