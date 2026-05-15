<?php
if (file_exists(__DIR__ . '/../config/app.php')) {
    $app = include __DIR__ . '/../config/app.php';
    if (!empty($app['installed'])) {
        die('系统已安装。');
    }
}
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = array(
        'host' => $_POST['db_host'],
        'port' => $_POST['db_port'],
        'name' => $_POST['db_name'],
        'user' => $_POST['db_user'],
        'pass' => $_POST['db_pass'],
        'charset' => 'utf8'
    );
    $app = array(
        'site_name' => $_POST['site_name'],
        'site_url' => $_POST['site_url'],
        'installed' => true,
        'default_provider' => 'siliconflow'
    );

    try {
        $dsn = 'mysql:host='.$db['host'].';port='.$db['port'].';dbname='.$db['name'].';charset=utf8';
        $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $sql = file_get_contents(__DIR__ . '/schema.sql');
        $pdo->exec($sql);
        $hash = password_hash($_POST['admin_pass'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO admins(username,password,created_at) VALUES(?,?,NOW())');
        $stmt->execute(array($_POST['admin_user'], $hash));

        file_put_contents(__DIR__ . '/../config/db.php', "<?php\nreturn " . var_export($db, true) . ";\n");
        file_put_contents(__DIR__ . '/../config/app.php', "<?php\nreturn " . var_export($app, true) . ";\n");
        $msg = '安装成功，请删除install目录并访问前台。';
    } catch (Exception $e) {
        $msg = '安装失败：' . $e->getMessage();
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>安装向导</title></head>
<body><h1>AI文章平台安装</h1><p><?php echo htmlspecialchars($msg); ?></p>
<form method="post">
站点名称<input name="site_name" required><br>
站点URL<input name="site_url"><br>
数据库主机<input name="db_host" value="127.0.0.1"><br>
端口<input name="db_port" value="3306"><br>
数据库名<input name="db_name" value="ai_writer"><br>
数据库用户<input name="db_user" value="root"><br>
数据库密码<input name="db_pass" type="password"><br>
管理员账号<input name="admin_user" value="admin"><br>
管理员密码<input name="admin_pass" type="password" required><br>
<button type="submit">开始安装</button></form></body></html>
