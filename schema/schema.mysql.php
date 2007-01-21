<?php
$queries = array(
'CREATE TABLE {commentinfo} (
  comment_id bigint(20) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  type smallint(6) default \'0\',
  value text 
);',
'CREATE TABLE {comments} (
  id bigint(20) NOT NULL auto_increment,
  post_id bigint(20) unsigned NOT NULL,
  name varchar(255) default NULL,
  email varchar(255) default NULL,
  url varchar(255) default NULL,
  ip varchar(255) default NULL,
  content text ,
  status int(11) default NULL,
  date timestamp NOT NULL default CURRENT_TIMESTAMP,
  type int(11) default NULL,
  UNIQUE KEY id (id)
);',
'CREATE TABLE {options} (
  site_id int(11) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  type int(11) NOT NULL default \'0\',
  value text 
);',
'CREATE TABLE {postinfo} (
  post_id bigint(20) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  type smallint(6) default \'0\',
  value text 
);',
'CREATE TABLE {posts} (
  id int(11) NOT NULL auto_increment,
  content_type smallint(6) default NULL,
  title varchar(255) default NULL,
  guid varchar(255) NOT NULL,
  content longtext ,
  user_id smallint(6) default NULL,
  status smallint(6) default NULL,
  pubdate timestamp NOT NULL default CURRENT_TIMESTAMP,
  updated timestamp NOT NULL default \'0000-00-00 00:00:00\',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
);',
'CREATE TABLE {postsite} (
  post_id int(11) NOT NULL,
  site_id int(11) unsigned NOT NULL,
  slug varchar(255) NOT NULL
);',
'CREATE TABLE {poststatus} (
  name varchar(255) NOT NULL,
  type smallint(6) default \'0\',
  PRIMARY KEY  (name)
);',
'CREATE TABLE {posttype} (
  name varchar(255) NOT NULL,
  type smallint(6) default \'0\',
  PRIMARY KEY  (name)
);',
'CREATE TABLE {sites} (
  id int(11) unsigned NOT NULL auto_increment,
  hostname varchar(255) default NULL,
  base_url varchar(255) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
);',
'CREATE TABLE {tags} (
  post_id bigint(20) unsigned NOT NULL,
  tag varchar(30) NOT NULL,
  KEY slug (post_id),
  KEY tag (tag)
);',
'CREATE TABLE {userinfo} (
  user_id varchar(255) NOT NULL,
  name varchar(50) NOT NULL,
  type smallint(6) default \'0\',
  value text 
);',
'CREATE TABLE {users} (
  id smallint(6) NOT NULL auto_increment,
  username varchar(20) NOT NULL,
  email varchar(30) NOT NULL,
  password varchar(40) NOT NULL,
  PRIMARY KEY  (username),
  UNIQUE KEY id (id),
  UNIQUE KEY username (username)
);',
'INSERT INTO {posttype} VALUES
		(\'entry\', 0),
		(\'page\', 1);',
'INSERT INTO {poststatus} VALUES
		(\'draft\', 0),
		(\'published\', 1), 
		(\'private\', 1);',
);
?>
