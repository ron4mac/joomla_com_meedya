ALTER TABLE `meedyaitems` ADD COLUMN `ratecnt` INTEGER DEFAULT 0;
ALTER TABLE `meedyaitems` ADD COLUMN `ratetot` INTEGER DEFAULT 0;
ALTER TABLE `meedyaitems` ADD COLUMN `cmntcnt` INTEGER DEFAULT 0;
# add ratings and comments tables
CREATE TABLE IF NOT EXISTS `uratings` (`iid` INTEGER,`uid` INTEGER,`rdate` INTEGER DEFAULT 0);
CREATE TABLE IF NOT EXISTS `gratings` (`iid` INTEGER,`ip` INTEGER,`rdate` INTEGER DEFAULT 0);
CREATE TABLE IF NOT EXISTS `comments` (`iid` INTEGER,`uid` INTEGER,'ctime' INTEGER,`cmnt` TEXT);
PRAGMA user_version=1
