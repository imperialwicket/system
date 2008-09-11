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

DROP TABLE {$prefix}posts;
DROP TABLE {$prefix}comments;
DROP TABLE {$prefix}log;

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

INSERT INTO {$prefix}posts SELECT * FROM {$prefix}posts_tmp;
INSERT INTO {$prefix}comments SELECT * FROM {$prefix}comments_tmp;
INSERT INTO {$prefix}log SELECT * FROM {$prefix}log_tmp;

DROP TABLE {$prefix}posts_tmp;
DROP TABLE {$prefix}comments_tmp;
DROP TABLE {$prefix}log_tmp;

ALTER TABLE {$prefix}crontab MODIFY COLUMN last_run INT UNSIGNED;
ALTER TABLE {$prefix}crontab MODIFY COLUMN next_run INT UNSIGNED NOT NULL;
ALTER TABLE {$prefix}crontab MODIFY COLUMN increment INT UNSIGNED NOT NULL;
ALTER TABLE {$prefix}crontab MODIFY COLUMN start_time INT UNSIGNED NOT NULL;
ALTER TABLE {$prefix}crontab MODIFY COLUMN end_time INT UNSIGNED;

UPDATE {$prefix}crontab SET last_run=NULL WHERE last_run=0;
UPDATE {$prefix}crontab SET end_time=NULL WHERE end_time=0;

DROP TABLE {$prefix}permissions;
DROP TABLE {$prefix}groups_permissions;
