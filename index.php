<?php require __DIR__.'/config/functions.php'; ensure_install(); page_header('多模态AI中心'); ?>
<section class="card"><h1>多模态AI综合网站</h1><p>集成 agnes-2.0-flash 对话、agnes-image-2.1-flash 图片生成、agnes-video-v2.0 视频生成，支持会员额度、调用日志与后台配置。</p><a class="btn" href="/user/dashboard.php">进入会员中心</a></section>
<div class="grid"><div class="card"><h3>智能对话</h3><p>流式体验入口、上下文记忆、多轮历史导出。</p><a href="/user/chat.php">开始对话</a></div><div class="card"><h3>AI图片生成</h3><p>关键词、尺寸、风格配置，记录留存与下载。</p><a href="/user/image.php">生成图片</a></div><div class="card"><h3>AI视频生成</h3><p>脚本、时长、分辨率配置，进度展示和下载。</p><a href="/user/video.php">生成视频</a></div></div>
<?php page_footer(); ?>
