<?php session_start(); if(empty($_SESSION['admin'])){header('Location: login.php');exit;} ?>
<h1>后台管理</h1><a href="prompts.php">指令词管理</a> | <a href="apis.php">API配置</a> | <a href="logout.php">退出</a>
