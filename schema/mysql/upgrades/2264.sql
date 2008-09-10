CREATE TABLE {$prefix}posts_tmp (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(255) NOT NULL,
  content_type SMALLINT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  guid VARCHAR(255) NOT NULL,
  content LONGTEXT NOT NULL,
  cached_content LONGTEXT NOT NULL,
  user_id SMALLINT UNSIGNED NOT NULL,
  status SMALLINT UNSIGNED NOT NULL,
  pubdate INT UNSIGNED NULL,
  updated INT UNSIGNED NULL,
  modified INT UNSIGNED NULL,
  PRIMARY KEY (id),
  UNIQUE KEY slug (slug(80))
);
INSERT INTO {$prefix}posts_tmp SELECT `id`, `slug`, `content_type`, `title`, `guid`, `content`, `cached_content`, `user_id`, `status`, UNIX_TIMESTAMP(`pubdate`) as `pubdate`, UNIX_TIMESTAMP(`updated`) as `updated`, UNIX_TIMESTAMP(`updated`) as `modified` FROM {$prefix}posts;

CREATE TABLE  {$prefix}comments_tmp (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id INT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  url VARCHAR(255) NULL,
  ip INT UNSIGNED NOT NULL,
  content TEXT,
  status SMALLINT UNSIGNED NOT NULL,
  date INT UNSIGNED NOT NULL,
  type SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY post_id (post_id)
);
INSERT INTO {$prefix}comments_tmp SELECT `id`, `post_id`, `name`, `email`, `url`, `ip`, `content`, `status`, UNIX_TIMESTAMP(`date`) as `date`, `type` FROM {$prefix}comments;

CREATE TABLE {$prefix}log_tmp (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL DEFAULT NULL,
  type_id INT NOT NULL,
  severity_id TINYINT NOT NULL,
  message VARCHAR(255) NOT NULL,
  data BLOB NULL,
  timestamp INT UNSIGNED NOT NULL,
  ip INT UNSIGNED NOT NULL, 
  PRIMARY KEY (id)
);
INSERT INTO {$prefix}log_tmp SELECT `id`, `user_id`, `type_id`, `severity_id`, `message`, `data`, UNIX_TIMESTAMP(`timestamp`) as `timestamp`, `ip` FROM {$prefix}log;

CREATE TABLE {$prefix}crontab_tmp (
  cron_id INT unsigned NOT NULL auto_increment,
  name VARCHAR(255) NOT NULL,
  callback VARCHAR(255) NOT NULL,
  last_run INT UNSIGNED,
  next_run INT UNSIGNED NOT NULL,
  increment INT UNSIGNED NOT NULL,
  start_time INT UNSIGNED NOT NULL,
  end_time INT UNSIGNED,
  result VARCHAR(255) NOT NULL,
  notify VARCHAR(255) NOT NULL,
  cron_class TINYINT unsigned NOT NULL DEFAULT 0,
  description TEXT NULL,
  PRIMARY KEY (cron_id)
);
INSERT INTO {$prefix}crontab_tmp SELECT `cron_id`, `name`, `callback`, UNIX_TIMESTAMP(`last_run`) as `last_run`, UNIX_TIMESTAMP(`next_run`) as `next_run`, `increment`, UNIX_TIMESTAMP(`start_time`) as `start_time`, UNIX_TIMESTAMP(`end_time`) as `end_time`, `result`, `notify`, `cron_class`, `description` FROM {$prefix}crontab;

DROP TABLE {$prefix}posts;
DROP TABLE {$prefix}comments;
DROP TABLE {$prefix}log;
DROP TABLE {$prefix}crontab;

CREATE TABLE {$prefix}posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(255) NOT NULL,
  content_type SMALLINT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  guid VARCHAR(255) NOT NULL,
  content LONGTEXT NOT NULL,
  cached_content LONGTEXT NOT NULL,
  user_id SMALLINT UNSIGNED NOT NULL,
  status SMALLINT UNSIGNED NOT NULL,
  pubdate INT UNSIGNED NOT NULL,
  updated INT UNSIGNED NOT NULL,
  modified INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY slug (slug(80))
);

CREATE TABLE  {$prefix}comments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id INT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  url VARCHAR(255) NULL,
  ip INT UNSIGNED NOT NULL,
  content TEXT,
  status SMALLINT UNSIGNED NOT NULL,
  date INT UNSIGNED NOT NULL,
  type SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY post_id (post_id)
);

CREATE TABLE {$prefix}log (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL DEFAULT NULL,
  type_id INT NOT NULL,
  severity_id TINYINT NOT NULL,
  message VARCHAR(255) NOT NULL,
  data BLOB NULL,
  timestamp INT UNSIGNED NOT NULL,
  ip INT UNSIGNED NOT NULL, 
  PRIMARY KEY (id)
);

CREATE TABLE {$prefix}crontab (
  cron_id INT unsigned NOT NULL auto_increment,
  name VARCHAR(255) NOT NULL,
  callback VARCHAR(255) NOT NULL,
  last_run INT UNSIGNED,
  next_run INT UNSIGNED NOT NULL,
  increment INT UNSIGNED NOT NULL,
  start_time INT UNSIGNED NOT NULL,
  end_time INT UNSIGNED,
  result VARCHAR(255) NOT NULL,
  notify VARCHAR(255) NOT NULL,
  cron_class TINYINT unsigned NOT NULL DEFAULT 0,
  description TEXT NULL,
  PRIMARY KEY (cron_id)
);

INSERT INTO {$prefix}posts SELECT * FROM {$prefix}posts_tmp;
INSERT INTO {$prefix}comments SELECT * FROM {$prefix}comments_tmp;
INSERT INTO {$prefix}log SELECT * FROM {$prefix}log_tmp;
INSERT INTO {$prefix}crontab SELECT * FROM {$prefix}crontab_tmp;

DROP TABLE {$prefix}posts_tmp;
DROP TABLE {$prefix}comments_tmp;
DROP TABLE {$prefix}log_tmp;
DROP TABLE {$prefix}crontab_tmp;

DROP TABLE {$prefix}permissions;
DROP TABLE {$prefix}groups_permissions;
