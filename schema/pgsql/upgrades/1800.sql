ALTER TABLE {$prefix}posts MODIFY pubdate VARCHAR(25) NOT NULL;
ALTER TABLE {$prefix}posts MODIFY updated VARCHAR(25) NOT NULL;
ALTER TABLE {$prefix}comments MODIFY date VARCHAR(25) NOT NULL;
ALTER TABLE {$prefix}log MODIFY timestamp VARCHAR(25) NOT NULL;

UPDATE {$prefix}posts SET pubdate = UNIX_TIMESTAMP(pubdate);
UPDATE {$prefix}posts SET updated = UNIX_TIMESTAMP(updated);
UPDATE {$prefix}comments SET date = UNIX_TIMESTAMP(date);
UPDATE {$prefix}log SET timestamp = UNIX_TIMESTAMP(timestamp);

ALTER TABLE {$prefix}posts MODIFY pubdate INT UNSIGNED NOT NULL;
ALTER TABLE {$prefix}posts MODIFY updated INT UNSIGNED NOT NULL;
ALTER TABLE {$prefix}comments MODIFY date INT UNSIGNED NOT NULL;
ALTER TABLE {$prefix}log MODIFY timestamp INT UNSIGNED NOT NULL;