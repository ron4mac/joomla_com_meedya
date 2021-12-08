ALTER TABLE `meedyaitems` ADD COLUMN `ratecnt` INTEGER DEFAULT 0;
ALTER TABLE `meedyaitems` ADD COLUMN `ratetot` INTEGER DEFAULT 0;
ALTER TABLE `meedyaitems` ADD COLUMN `cmntcnt` INTEGER DEFAULT 0;
# add ratings and comments tables
CREATE TABLE `uratings` (`iid` INTEGER,`uid` INTEGER,`rdate` INTEGER DEFAULT 0);
CREATE TABLE `gratings` (`iid` INTEGER,`ip` INTEGER,`rdate` INTEGER DEFAULT 0);
CREATE TABLE `comments` (`iid` INTEGER,`uid` INTEGER,'ctime' INTEGER,`cmnt` TEXT);
