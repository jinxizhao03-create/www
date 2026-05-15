<?php require __DIR__ . '/../config/functions.php'; $db=get_db(); $id=intval($_GET['id']);
if($_SERVER['REQUEST_METHOD']==='POST'){$content=$_POST['rewritten_content'];$db->prepare('UPDATE rewrite_tasks SET rewritten_content=?,rewritten_words=?,updated_at=NOW() WHERE id=?')->execute(array($content,mb_strlen($content,'UTF-8'),$id));}
$r=$db->query('SELECT * FROM rewrite_tasks WHERE id='.$id)->fetch(PDO::FETCH_ASSOC);
?><form method="post"><h3>编辑改写结果</h3><textarea name="rewritten_content" style="width:95%;height:500px"><?php echo esc($r['rewritten_content']);?></textarea><br><button>保存</button></form>
