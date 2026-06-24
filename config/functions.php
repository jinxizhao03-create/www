<?php
function config_path($name){ return __DIR__ . '/' . $name . '.php'; }
function app_config() { return file_exists(config_path('app')) ? include config_path('app') : array('installed'=>false,'site_name'=>'Agnes多模态AI平台'); }
function get_db() {
    static $pdo = null; if ($pdo) return $pdo;
    $cfg = include __DIR__ . '/db.php';
    $dsn = 'mysql:host='.$cfg['host'].';port='.$cfg['port'].';dbname='.$cfg['name'].';charset='.$cfg['charset'];
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC));
    return $pdo;
}
function ensure_install(){ $app=app_config(); if(empty($app['installed']) && strpos($_SERVER['REQUEST_URI'],'/install/')===false){ header('Location: /install/index.php'); exit; } }
function esc($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf_token(){ if(session_status()!==PHP_SESSION_ACTIVE) session_start(); if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function verify_csrf(){ if($_SERVER['REQUEST_METHOD']==='POST'){ if(empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) die('CSRF校验失败'); } }
function clean($s,$len=255){ return mb_substr(trim(strip_tags((string)$s)),0,$len,'UTF-8'); }
function require_admin(){ if(session_status()!==PHP_SESSION_ACTIVE) session_start(); if(empty($_SESSION['admin_id'])){ header('Location: /admin/login.php'); exit; } }
function require_member(){ if(session_status()!==PHP_SESSION_ACTIVE) session_start(); if(empty($_SESSION['member_id'])){ header('Location: /user/login.php'); exit; } }
function encryption_key(){ $app=app_config(); return hash('sha256', isset($app['secret_key'])?$app['secret_key']:'change-me', true); }
function encrypt_secret($plain){ if($plain==='') return ''; $iv=random_bytes(16); $cipher=openssl_encrypt($plain,'AES-256-CBC',encryption_key(),OPENSSL_RAW_DATA,$iv); return base64_encode($iv.$cipher); }
function decrypt_secret($enc){ if($enc==='') return ''; $raw=base64_decode($enc,true); if($raw===false || strlen($raw)<17) return $enc; $iv=substr($raw,0,16); $cipher=substr($raw,16); return openssl_decrypt($cipher,'AES-256-CBC',encryption_key(),OPENSSL_RAW_DATA,$iv); }
function json_response($data,$code=200){ http_response_code($code); header('Content-Type: application/json; charset=utf-8'); header('Access-Control-Allow-Origin: '.(isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'*')); header('Access-Control-Allow-Credentials: true'); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function agnes_config($type){ $db=get_db(); $st=$db->prepare('SELECT * FROM api_configs WHERE model_type=? AND enabled=1 ORDER BY id DESC LIMIT 1'); $st->execute(array($type)); $row=$st->fetch(); return $row; }
function call_agnes($type,$payload){ $cfg=agnes_config($type); $started=microtime(true); $ok=false; $resp=''; $cost=isset($payload['cost'])?(int)$payload['cost']:1; try{ if(!$cfg){ $resp='[模拟'.$type.'结果] '.(isset($payload['prompt'])?$payload['prompt']:''); $ok=true; } else { $key=decrypt_secret($cfg['api_key']); $ch=curl_init($cfg['base_url']); curl_setopt_array($ch,array(CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_HTTPHEADER=>array('Content-Type: application/json','Authorization: Bearer '.$key),CURLOPT_POSTFIELDS=>json_encode($payload,JSON_UNESCAPED_UNICODE),CURLOPT_TIMEOUT=>120)); $resp=curl_exec($ch); if($resp===false) throw new Exception(curl_error($ch)); $code=curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch); $ok=$code>=200&&$code<300; } } catch(Exception $e){ $resp=$e->getMessage(); }
    log_model_call($type,$cost,$ok?'success':'failed',$resp,round((microtime(true)-$started)*1000)); return array('ok'=>$ok,'result'=>$resp,'cost'=>$cost); }
function log_model_call($type,$cost,$status,$result,$latency){ try{ $db=get_db(); $mid=isset($_SESSION['member_id'])?$_SESSION['member_id']:null; $st=$db->prepare('INSERT INTO model_logs(member_id,model_type,cost,status,result_excerpt,latency_ms,ip,created_at) VALUES(?,?,?,?,?,?,?,NOW())'); $st->execute(array($mid,$type,$cost,$status,mb_substr($result,0,500,'UTF-8'),$latency,isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'')); if($mid && $status==='success') $db->prepare('UPDATE members SET credits=GREATEST(credits-?,0) WHERE id=?')->execute(array($cost,$mid)); }catch(Exception $e){} }
function page_header($title){ $app=app_config(); echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.esc($title).'</title><link rel="stylesheet" href="/assets/style.css"></head><body><header><b>'.esc($app['site_name']).'</b><nav><a href="/index.php">首页</a><a href="/user/dashboard.php">会员中心</a><a href="/admin/index.php">后台</a></nav></header><main>'; }
function page_footer(){ echo '</main><footer>请遵守法律法规，AI生成内容需人工审核。生产环境请启用HTTPS。</footer></body></html>'; }
