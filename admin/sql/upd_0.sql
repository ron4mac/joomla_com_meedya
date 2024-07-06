# add ratings and comments tables;
CREATE TABLE `ratings` (`iid` INTEGER,`uid` INTEGER,`val` INTEGER);
CREATE TABLE `comments` (`iid` INTEGER,`uid` INTEGER,`cmnt` TEXT);
PRAGMA user_version=1
