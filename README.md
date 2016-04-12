Magento-cleaner
===============

  - Find all magento installed under /home/ or /var/www/ with a maxdepth to 7

  - Parse app/etc/local.xml for get database credentials, db prefix and custom settings

  - Purge db tables

  - Logrotate logs custom settings

  - Purge old magento reports custom settings

  - Purge only old php sessions accross custom settings


Launch it with php cli on a shell with:

php /path/to/file/cron_clean_mage_day.php

Add cron task like:

50 07  * * *   your-php-user php /path/to/cron_clean_mage_day.php

Output is:

[...]
- Got another Magento website to clean /var/www/mysite.com/:
  - Settings and environment: 
    - Number of magento folders already parsed: 1
    - Host : localhost
    - Dbname : prod
    - Username : prod
    - Pwd : xxxxxxxxxxxxxx
    - Prefix : 
    - Log rotate_of logs : 1
    - Days of the log rotate : 10
    - Clean reports : 1
    - Clean reports after days : 2
    - Clean sessions files : 1
    - Clean sessions files after minuts : 500
    - Using shell/log.php : 1

Call clean session files 
Call clean_log_tables() 
[...]
 End of script, parsed 2 magento folders.




#Todo

  - Add compatibility for Magento2
  - Collect statistics / generate nice report.
  - Logrotate more somes filenames with local.xml custome settings (bash -c \"[[ ! $1 =~ logistics ]] && [[ ! $1 =~ ph2m ]] && [[ ! $1 =~ norotate ]]  )
