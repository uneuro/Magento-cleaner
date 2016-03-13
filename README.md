Magento-cleaner
===============

  - Find all magento installed under /home/ or /var/www/ with a maxdepth to 7

  - Parse app/etc/local.xml for get database credentials and db prefix

  - Purge listed tables

  - Purge listed folders

  - Logrotate logs

  - Purge only old php sessions


Launch it with php cli on a terminal with:

php /path/to/file/cron_clean_mage_day.php

Add cron task like:

50 07  * * *   yourwebuser php /path/to/cron_clean_mage_day.php

#Todo

  - Add compatibility with Magento2
  - Collect statistics / generate nice report.
  - Add foreach exluded logrotate a specific logrotate of 1 year
  - Detect vars from external data like a file configuration in app/etc/
