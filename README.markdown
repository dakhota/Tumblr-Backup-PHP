Tumblr Backup (PHP)
======
This script has been designed to run on the CLI and as such you may need to update the first line in run.php file to reflect the location of the PHP binary on your machine.

You also need to rename config-dist.ini to config.ini and change the options to match your login and tumblr blog details. Then call run.php to complete the backup.


Requires
--------

* SQLite
* cURL
* PHP5


TODO
----

* Back up DISQUS comments at the same time.
* Add in extra logging so progress is more visible.