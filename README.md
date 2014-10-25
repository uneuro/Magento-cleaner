Magento-cleaner
===============

-Find all magento installed under /home
-Parse app/etc/local.xml for get database credentials and db prefix
-Purge listed tables
-Purge listed folders
-Purge only old php sessions

Launch it with php cli on a terminal with:

php /path/to/file/cron_clean_mage_day.php

Add cron task like:
50 07  * * *   root php /path/to/cron_clean_mage_day.php

#Todo
Use mysql pdo.
Collect statistics / generate nice report.
