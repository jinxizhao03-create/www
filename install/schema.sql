CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS prompt_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('generate','rewrite') NOT NULL,
  name VARCHAR(100) NOT NULL,
  prompt TEXT NOT NULL,
  created_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS api_configs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  provider VARCHAR(50) NOT NULL,
  api_key VARCHAR(255) NOT NULL,
  base_url VARCHAR(255) DEFAULT NULL,
  is_default TINYINT(1) DEFAULT 0,
  created_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS rewrite_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  category_id INT,
  original_content MEDIUMTEXT,
  rewritten_content MEDIUMTEXT,
  status ENUM('pending','processing','done') DEFAULT 'pending',
  origin_words INT DEFAULT 0,
  rewritten_words INT DEFAULT 0,
  created_at DATETIME,
  updated_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
