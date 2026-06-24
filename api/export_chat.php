<?php
session_start();
require __DIR__.'/../config/functions.php';
require_member();
$db=get_db();
header('Content-Type:text/csv;charset=utf-8');
header('Content-Disposition: attachment; filename=chat_logs.csv');
$out=fopen('php://output','w');
fputcsv($out,array('time','model','cost','status','result'));
$st=$db->prepare('SELECT * FROM model_logs WHERE member_id=? AND model_type="chat" ORDER BY id DESC');
$st->execute(array($_SESSION['member_id']));
foreach($st as $r){ fputcsv($out,array($r['created_at'],'chat',$r['cost'],$r['status'],$r['result_excerpt'])); }
