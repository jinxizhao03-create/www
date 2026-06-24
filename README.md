# Agnes 多模态AI综合网站

面向 IIS + 护卫神主机大师 + PHP 8.0 + MySQL 5.7.44.0 的多模态AI站点源码，包含一键安装、后台管理、会员系统、对话/图片/视频模型调用、日志与备份。

## 主要功能
- 安装向导：PHP扩展、函数、目录权限、内存检测；数据库连接测试；自动执行 `install/schema.sql`；生成配置文件。
- 后台：模型API配置中心（agnes-2.0-flash、agnes-image-2.1-flash、agnes-video-v2.0）、会员管理、调用日志筛选导出、站点配置、管理员账号管理、数据备份。
- 前台：会员注册登录、智能对话、AI图片生成、AI视频生成、会员中心与消耗明细。
- 安全：PDO预处理、HTML转义、CSRF校验、API密钥加密、IIS安全响应头、HTTPS部署提示。

## 交付文件
- `web.config`：IIS伪静态和安全头配置。
- `install/schema.sql`：数据库初始化脚本。
- `docs/deployment.md`：安装部署文档与后台操作手册。

## 安装
访问 `/install/index.php`，按页面检测结果修复环境后填写数据库、站点和管理员信息即可。
