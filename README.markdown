Tumblr and/or Disqus Backup (PHP)
======
This script has been designed to run on the CLI and as such you may need to update the first line in run.php file to reflect the location of the PHP binary on your machine.

You also need to rename config-dist.ini to config.ini and change the options to match your logins (leave the configuration details blank if you do not wish to download a backup for one of the services). Then call run.php to complete the backup.


Requires
--------

* SQLite
* cURL
* PHP5


TODO
----

* Add in extra logging so progress is more visible.
* Create restore scripts.
* Make scripts timeout etc aware and safe.
* Backup images etc as well