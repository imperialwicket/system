CREATE TABLE {$prefix}posts ( 
	id INTEGER NOT NULL AUTOINCREMENT
,	slug VARCHAR(255) NOT NULL
,	content_type SMALLINT UNSIGNED NOT NULL
,	title VARCHAR(255) NOT NULL
,	guid VARCHAR(255) NOT NULL
,	content LONGTEXT NOT NULL
,	user_id SMALLINT UNSIGNED NOT NULL
,	status SMALLINT UNSIGNED NOT NULL
,	pubdate DATETIME NOT NULL 
,	updated TIMESTAMP NOT NULL
, PRIMARY KEY (id)
, UNIQUE (slug(80))
);

CREATE TABLE  {$prefix}postinfo  ( 
	post_id INT UNSIGNED NOT NULL
,	name VARCHAR(50) NOT NULL
,	type SMALLINT UNSIGNED NOT NULL DEFAULT 0
,	value TEXT
, PRIMARY KEY (post_id, name)
);

CREATE TABLE  {$prefix}posttype ( 
	name VARCHAR(255) NOT NULL 
,	type SMALLINT UNSIGNED NOT NULL DEFAULT 0
, PRIMARY KEY (name)
);

INSERT INTO  {$prefix}posttype VALUES
("entry", 0),
("page", 1);

CREATE TABLE  {$prefix}poststatus ( 
	name VARCHAR(255) NOT NULL 
,	type SMALLINT UNSIGNED NOT NULL DEFAULT 0
, PRIMARY KEY (name)
);

INSERT INTO  {$prefix}poststatus VALUES
("draft", 0),
("published", 1), 
("private", 1);

CREATE TABLE  {$prefix}options (
	name VARCHAR(50) NOT NULL
,	type SMALLINT UNSIGNED NOT NULL DEFAULT 0
,	value TEXT
, PRIMARY KEY (name)
);

CREATE TABLE  {$prefix}users (
	id SMALLINT UNSIGNED NOT NULL AUTOINCREMENT
,	username VARCHAR(20) NOT NULL
,	email VARCHAR(30) NOT NULL
,	password VARCHAR(40) NOT NULL
, PRIMARY KEY (id)
, UNIQUE (username)
);

CREATE TABLE  {$prefix}userinfo ( 
	user_id SMALLINT UNSIGNED NOT NULL
,	name VARCHAR(50) NOT NULL
,	type SMALLINT UNSIGNED NOT NULL DEFAULT 0
,	value TEXT
, PRIMARY KEY (user_id, name)
);

CREATE TABLE  {$prefix}tags (
  id INT UNSIGNED NOT NULL AUTOINCREMENT
, tag_text VARCHAR(50) NOT NULL
, PRIMARY KEY (id)
, UNIQUE (tag_text)	
);

CREATE TABLE  {$prefix}tag2post (
  tag_id INT UNSIGNED NOT NULL
, post_id INT UNSIGNED NOT NULL
, PRIMARY KEY (tag_id, post_id)
, INDEX (post_id)
);

CREATE TABLE  {$prefix}themes (
  id SMALLINT UNSIGNED NOT NULL AUTOINCREMENT
, name VARCHAR(80) NOT NULL
, version VARCHAR(10) NOT NULL
, template_engine VARCHAR(40) NOT NULL
, theme_dir VARCHAR(255) NOT NULL
, is_active TINYINT UNSIGNED NOT NULL DEFAULT 0
, PRIMARY KEY (id)
);

INSERT INTO  {$prefix}themes (
  id
, name
, version
, template_engine
, theme_dir
, is_active
) VALUES (
  NULL
, "k2"
, "1.0"
, "rawphpengine"
, "k2"
, 1
);

CREATE TABLE  {$prefix}comments (
	id INT UNSIGNED NOT NULL AUTOINCREMENT
,	post_id INT UNSIGNED NOT NULL
,	name VARCHAR(100) NOT NULL
,	email VARCHAR(100) NOT NULL
,	url VARCHAR(255) NULL
,	ip INT UNSIGNED NOT NULL
,	content TEXT
,	status TINYINT UNSIGNED NOT NULL
,	date TIMESTAMP NOT NULL
,	type SMALLINT UNSIGNED NOT NULL
, PRIMARY KEY (id)
, INDEX (post_id)
);

CREATE TABLE  {$prefix}commentinfo ( 
	comment_id INT UNSIGNED NOT NULL
,	name VARCHAR(50) NOT NULL
,	type SMALLINT UNSIGNED NOT NULL DEFAULT 0
,	value TEXT NULL
, PRIMARY KEY (comment_id, name)
);

