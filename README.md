# joomla-com-meedya
A developing media gallery for Joomla!

This component provides media (image/video) galleries at user, group or site levels. Menu instances can be created where every Joomla user can have their own gallery. Or an instance can belong to a user group. Or an instance can be a sitewide gallery.

All gallery management for each gallery is done from the front-end.

Data for each gallery is kept in Sqlite databases, so Joomla must support sqlite3 (PDO:sqlite).

Each gallery can select one album, along with its sub-albums, to be public. A Joomla menu item can then be used to display all public albums.

All gallery items can, by option, include ratings and comments.

Gallery items exist on their own and can be associated with one or more albums. Deleting an album will not (necessarily) delete the items in the album.

The component uses SQLite3 databases for gallery information, allowing separate galleries for all users, groups, or site-wide. The Joomla library [lib_rjuser](http://github.com/ron4mac/lib_rjuser) is REQUIRED to use this component. Also, the companion plugin, [plg_system_rjuserd](http://github.com/ron4mac/joomla_plg_rjuserd), should be used to manage the storage location for users and groups. To download a single, auto-generated package installer for the latest release versions of com_meedya, lib_rjuser and plg_system_rjuserd, [click here](http://rjcrans.net/git/com_meedya/packager/).
