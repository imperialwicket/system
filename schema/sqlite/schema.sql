PRAGMA auto_vacuum = 1;

CREATE TABLE {$prefix}posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  slug VARCHAR(255) NOT NULL,
  content_type SMALLINT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  guid VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  cached_content LONGTEXT NOT NULL,
  user_id SMALLINT UNSIGNED NOT NULL,
  status SMALLINT UNSIGNED NOT NULL,
  pubdate INT UNSIGNED NOT NULL,
  updated INT UNSIGNED NOT NULL
  modified INT UNSIGNED NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS slug ON {$prefix}posts(slug);

CREATE TABLE {$prefix}postinfo  (
  post_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  type SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  value TEXT,
  PRIMARY KEY (post_id, name)
);

CREATE TABLE {$prefix}posttype (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  active TINYINT(1) DEFAULT 1
);

CREATE TABLE {$prefix}poststatus (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  internal TINYINT(1)
);

CREATE TABLE {$prefix}options (
  name VARCHAR(255) NOT NULL,
  type SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  value TEXT,
  PRIMARY KEY (name)
);

CREATE TABLE {$prefix}users (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS username ON {$prefix}users(username);

CREATE TABLE {$prefix}userinfo (
  user_id SMALLINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  type SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  value TEXT,
  PRIMARY KEY (user_id, name)
);

CREATE TABLE {$prefix}tags (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  tag_text VARCHAR(255) NOT NULL,
  tag_slug VARCHAR(255) NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS tag_slug ON {$prefix}tags(tag_slug);

CREATE TABLE {$prefix}tag2post (
  tag_id INTEGER UNSIGNED NOT NULL,
  post_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (tag_id, post_id)
);
CREATE INDEX IF NOT EXISTS tag2post_post_id ON {$prefix}tag2post(post_id);

CREATE TABLE {$prefix}comments (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  post_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  url VARCHAR(255) NULL,
  ip INTEGER UNSIGNED NOT NULL,
  content TEXT,
  status SMALLINT UNSIGNED NOT NULL,
  date INT UNSIGNED NOT NULL,
  type SMALLINT UNSIGNED NOT NULL
);
CREATE INDEX IF NOT EXISTS comments_post_id ON {$prefix}comments(post_id);

CREATE TABLE {$prefix}commentinfo (
  comment_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  type SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  value TEXT NULL,
  PRIMARY KEY (comment_id, name)
);

CREATE TABLE {$prefix}rewrite_rules (
  rule_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  parse_regex VARCHAR(255) NOT NULL,
  build_str VARCHAR(255) NOT NULL,
  handler VARCHAR(255) NOT NULL,
  action VARCHAR(255) NOT NULL,
  priority SMALLINT UNSIGNED NOT NULL,
  is_active SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  rule_class SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  description TEXT NULL,
  parameters TEXT NULL
);

CREATE TABLE {$prefix}crontab (
  cron_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  callback VARCHAR(255) NOT NULL,
  last_run INT UNSIGNED NOT NULL,
  next_run INT UNSIGNED NOT NULL,
  increment INT UNSIGNED NOT NULL,
  start_time INT UNSIGNED NOT NULL,
  end_time INT UNSIGNED NOT NULL,
  result VARCHAR(255) NOT NULL,
  notify VARCHAR(255) NOT NULL,
  cron_class TINYINT UNSIGNED NOT NULL DEFAULT 0,
  description TEXT NULL
);

CREATE TABLE {$prefix}log (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  user_id INTEGER NULL DEFAULT NULL,
  type_id INTEGER NOT NULL,
  severity_id TINYINT NOT NULL,
  message VARCHAR(255) NOT NULL,
  data BLOB NULL,
  timestamp INT UNSIGNED NOT NULL,
  ip INTEGER UNSIGNED NOT NULL
);

CREATE TABLE {$prefix}log_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  module VARCHAR(100) NOT NULL,
  type VARCHAR(100) NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS module_type ON {$prefix}log_types(module, type);

CREATE TABLE {$prefix}groups (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS group_name ON {$prefix}groups(name);

CREATE TABLE {$prefix}users_groups (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  user_id INTEGER UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS user_group ON {$prefix}users_groups(user_id,group_id);

CREATE TABLE {$prefix}sessions  (
  token VARCHAR(255) NOT NULL,
  subnet INTEGER NOT NULL,
  expires INTEGER UNSIGNED NOT NULL,
  ua VARCHAR(255) NOT NULL,
  user_id INTEGER,
  data TEXT
);
CREATE UNIQUE INDEX IF NOT EXISTS token_key ON {$prefix}sessions(token);

CREATE TABLE {$prefix}terms (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  term VARCHAR(255) NOT NULL,
  term_display VARCHAR(255) NOT NULL,
  vocabulary_id INTEGER NOT NULL,
  mptt_left INTEGER UNSIGNED NOT NULL,
  mptt_right INTEGER UNSIGNED NOT NULL
);

CREATE TABLE {$prefix}vocabularies (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  hierarchical TINYINT(1) UNSIGNED NOT NULL DEFAUlT 0,
  required TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
);

CREATE TABLE {$prefix}object_terms (
  object_id INTEGER NOT NULL,
  term_id INTEGER NOT NULL,
  object_type_id INTEGER NOT NULL,
  PRIMARY KEY (object_id,term_id)
);

CREATE TABLE {$prefix}object_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(50)
);

INSERT INTO {$prefix}object_types (name) VALUES
  ('post');

CREATE TABLE {$prefix}tokens (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  description VARCHAR(255) NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS name ON {$prefix}tokens(name);

CREATE TABLE {$prefix}post_tokens (
  post_id INTEGER NOT NULL,
  token_id INTEGER NOT NULL,
  PRIMARY KEY (post_id, token_id)
);

CREATE TABLE {$prefix}group_token_permissions (
  group_id INTEGER NOT NULL,
  token_id INTEGER NOT NULL,
  permission_id TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (group_id, token_id)
);

CREATE TABLE {$prefix}user_token_permissions (
  user_id INTEGER NOT NULL,
  token_id INTEGER NOT NULL,
  permission_id TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, token_id)
);

CREATE TABLE {$prefix}permissions (
  id TINYINT UNSIGNED PRIMARY KEY AUTOINCREMENT NOT NULL,
  description VARCHAR(255) NOT NULL
);

INSERT INTO {$prefix}permissions (description) VALUES
  ('denied'),
  ('read'),
  ('write'),
  ('full');
