CREATE TABLE `meedyaitems` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`file` TEXT NOT NULL,
	`fsize` INTEGER DEFAULT 0,
	`tsize` INTEGER DEFAULT 0,
	`ownid` INTEGER DEFAULT 0,
	`mtype` TEXT DEFAULT NULL,
	`title` TEXT DEFAULT NULL,
	`desc` TEXT DEFAULT NULL,
	`kywrd` TEXT DEFAULT NULL,
	`album` TEXT DEFAULT NULL,
	`timed` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`expodt` DATETIME DEFAULT NULL,
	`thumb` TEXT DEFAULT NULL);
CREATE INDEX item_expodt_idx ON meedyaitems (expodt);
CREATE VIEW 'usage' AS SELECT SUM(tsize) as totuse FROM `meedyaitems`;
CREATE TABLE `cats` (
	`cid` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`ownid` INTEGER DEFAULT 0,
	`title` TEXT DEFAULT NULL);
CREATE TABLE `albums` (
	`aid` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`paid` INTEGER DEFAULT 0,
	`catid` INTEGER DEFAULT 0,
	`ownid` INTEGER DEFAULT 0,
	`tstamp` INTEGER DEFAULT 0,
	`thumb` INTEGER DEFAULT 0,
	`visib` INTEGER DEFAULT 0,
	`hord` TEXT DEFAULT NULL,
	`title` TEXT DEFAULT NULL,
	`desc` TEXT DEFAULT NULL,
	`items` TEXT DEFAULT NULL);
CREATE TABLE `config` (
	`type` TEXT NOT NULL,
	`vals` TEXT)