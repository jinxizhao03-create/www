<?php
require __DIR__ . '/../config/functions.php'; $db = get_db();
$id = intval($_GET['id']);
$r = $db->query('SELECT * FROM rewrite_tasks WHERE id='.$id)->fetch(PDO::FETCH_ASSOC);
?><!doctype html><html><head><meta charset="utf-8"><title>预览</title></head>
<body><h3><?php echo esc($r['title']); ?></h3><p>原文字数：<?php echo $r['origin_words']; ?> 改写字数：<?php echo $r['rewritten_words']; ?></p>
<div style="display:flex;gap:20px"><textarea style="width:48%;height:400px"><?php echo esc($r['original_content']); ?></textarea><textarea style="width:48%;height:400px"><?php echo esc($r['rewritten_content']); ?></textarea></div>
</body></html>
