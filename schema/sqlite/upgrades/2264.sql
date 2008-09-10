PRAGMA auto_vacuum = 1;

CREATE TEMPORARY TABLE posts_tmp (
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
  updated INT UNSIGNED NOT NULL,
  modified INT UNSIGNED NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS slug ON posts_tmp(slug);

CREATE TEMPORARY TABLE comments_tmp (
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
CREATE INDEX IF NOT EXISTS comments_post_id ON comments_tmp(post_id);

CREATE TEMPORARY TABLE log_tmp (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  user_id INTEGER NULL DEFAULT NULL,
  type_id INTEGER NOT NULL,
  severity_id TINYINT NOT NULL,
  message VARCHAR(255) NOT NULL,
  data BLOB NULL,
  timestamp INT UNSIGNED NOT NULL,
  ip INTEGER UNSIGNED NOT NULL
);

CREATE TEMPORARY TABLE crontab_tmp (
  cron_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  callback VARCHAR(255) NOT NULL,
  last_run INTEGER,
  next_run INTEGER NOT NULL,
  increment INTEGER NOT NULL,
  start_time INTEGER NOT NULL,
  end_time INTEGER,
  result VARCHAR(255) NOT NULL,
  notify VARCHAR(255) NOT NULL,
  cron_class TINYINTEGER NOT NULL DEFAULT 0,
  description TEXT NULL
);

INSERT INTO posts_tmp SELECT id, slug, content_type, title, guid, content, cached_content, user_id, status, strftime('%s', pubdate) as pubdate, strftime('%s', updated) as updated, strftime('%s', updated) as modified FROM {$prefix}posts;
INSERT INTO comments_tmp SELECT id, post_id, name, email, url, ip, content, status, strftime('%s', date) as date, type FROM {$prefix}comments;
INSERT INTO log_tmp SELECT id, user_id, type_id, severity_id, message, data, strftime('%s', timestamp) as timestamp, ip FROM {$prefix}log;
INSERT INTO crontab_tmp SELECT cron_id, name, callback, strftime('%s', last_run) as last_run, strftime('%s', next_run) as next_run, increment, strftime('%s', start_time) as start_time, strftime('%s', end_time) as end_time, result, notify, cron_class, description FROM {$prefix}crontab;

DROP TABLE {$prefix}posts;
DROP TABLE {$prefix}comments;
DROP TABLE {$prefix}log;
DROP TABLE {$prefix}crontab;

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
  updated INT UNSIGNED NOT NULL,
  modified INT UNSIGNED NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS slug ON {$prefix}posts(slug);

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

CREATE TABLE {$prefix}crontab (
  cron_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  callback VARCHAR(255) NOT NULL,
  last_run INTEGER,
  next_run INTEGER NOT NULL,
  increment INTEGER NOT NULL,
  start_time INTEGER NOT NULL,
  end_time INTEGER,
  result VARCHAR(255) NOT NULL,
  notify VARCHAR(255) NOT NULL,
  cron_class TINYINTEGER NOT NULL DEFAULT 0,
  description TEXT NULL
);

INSERT INTO {$prefix}posts SELECT * FROM posts_tmp;
INSERT INTO {$prefix}comments SELECT * FROM comments_tmp;
INSERT INTO {$prefix}log SELECT * FROM log_tmp;
INSERT INTO {$prefix}crontab SELECT * FROM crontab_tmp;
