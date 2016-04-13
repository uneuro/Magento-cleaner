Magento-cleaner
===============

###This module has never been used in production.

A php script for find all magento webstites in your webserver and clean them all depending on settings stored in app/etc/local.xml

## Features

  - Find all magento installed under /home/ or /var/www/ with a maxdepth to 7

  - Parse app/etc/local.xml for get database credentials, db prefix and custom settings

  - Purge db tables

  - Logrotate logs custom settings

  - Purge old magento reports custom settings

  - Purge only old php sessions accross custom settings

##Usage

Put magento/app/etc/local.xml custom settings (only integers)
```xml
<config>
    <magentocleaner>
        <log_rotate_magento_app_logs><![CDATA[1]]></log_rotate_magento_app_logs>
        <clean_magento_reports><![CDATA[1]]></clean_magento_reports>
        <clean_magento_sessions_files><![CDATA[1]]></clean_magento_sessions_files>
        <log_rotate_magento_app_logs_days><![CDATA[10]]></log_rotate_magento_app_logs_days>
        <clean_magento_log_php><![CDATA[1]]></clean_magento_log_php>
        <clean_magento_reports_days><![CDATA[2]]></clean_magento_reports_days>
        <clean_magento_sessions_files_minuts><![CDATA[500]]></clean_magento_sessions_files_minuts>
    </magentocleaner>
    <global>
        <install>
            <date><![CDATA[Thu, 02 Jul 2015 14:35:15 +0000]]></date>
        </install>
[...]
```
Launch it with php cli on a shell with:
```bash
php /path/to/file/cron_clean_mage_day.php
```
Add cron task like:
```
50 07  * * *   your-php-user php /path/to/cron_clean_mage_day.php
```
Output is:
```yaml
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
```

## Requirements
- Magento Community 1.3.x, 1.9.x
- PHP 5.3+


##Todo

  - Add compatibility for Magento2
  - Collect statistics / generate nice report.
  - Logrotate more somes filenames with local.xml custome settings 
```bash
bash -c \"[[ ! $1 =~ logistics ]] && [[ ! $1 =~ ph2m ]] && [[ ! $1 =~ norotate ]]  )
```
## License
Magento-cleaner is licensed under the [MIT license](https://github.com/1pulse/Magento-cleaner/blob/master/LICENSE).
