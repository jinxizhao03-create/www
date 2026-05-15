# AI文章生成与改写平台（IIS + PHP5.6 + MySQL5.6）

## 1. 功能概述
- 前台：标题生成文章、上传TXT/Word创建改写任务、任务状态（未改写/改写中/已完成）、预览原文和改写文、改写后可编辑。
- 后台：管理员登录、指令词分类管理（标题生成与文章改写分开）、API密钥管理（硅基流动默认，可扩展ChatGPT/DeepSeek/豆包/通义千问）。
- 安装：安装向导、数据库初始化、管理员账号初始化、站点信息配置。

## 2. 目录结构
- `index.php`：前台首页（标题生成文章）
- `user/`：会员/用户功能（改写、预览、编辑）
- `admin/`：管理员功能（登录、指令词、API配置）
- `config/`：数据库与系统配置
- `install/`：安装向导与SQL结构
- `uploads/`：上传文件目录（可按需扩展使用）

## 3. 服务器要求
- IIS 7+，启用PHP5.6（建议开启 `mbstring`、`curl`、`pdo_mysql`）
- MySQL 5.6+
- 站点根目录绑定到本项目根目录

## 4. 安装步骤
1. 上传代码到IIS站点根目录。
2. 访问 `http://你的域名/install/index.php`。
3. 填写站点信息、数据库信息、管理员账号密码并安装。
4. 安装成功后删除或限制 `install/` 目录访问。
5. 访问前台 `/index.php`，后台 `/admin/login.php`。

## 5. API接入说明
- 后台 `API配置` 中添加服务商和API Key并设为默认。
- 当前默认走兼容 Chat Completions 协议，便于扩展。
- 可通过调整 `config/functions.php` 的 `call_llm()` 中 endpoint/model 完成更深度适配。

## 6. 维护建议
- 生产环境启用HTTPS。
- 定期备份数据库（`admins`、`prompt_categories`、`api_configs`、`rewrite_tasks`）。
- 建议给 `admin/` 增加IP白名单、验证码和登录限制。
