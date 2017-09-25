<?php

// https://github.com/1pulse/Magento-cleaner/

//  Defaults settings / level of cleaning
//  Settings in */app/etc/local.xml overwrite this
//  Only integers

$log_rotate_magento_app_logs         = '1';   // 0 or 1
$clean_magento_reports               = '1';   // 0 or 1
$clean_magento_sessions_files        = '1';   // 0 or 1
$log_rotate_magento_app_logs_days    = '10';  //setting of logs days to keep
$clean_magento_log_php               = '1';   // 0 or 1
$clean_magento_reports_days          = '2';   //setting of var/repports to keep in days
$clean_magento_sessions_files_minuts = '500'; //setting of var/session/{sess_files} to keep in minuts


$parsed_magento_folders       = ''; //For stats and repport


function clean_log_tables()
{
    global $db;
    $tables = array(
        'aw_core_logger',
        'lengow_log',
        'dataflow_batch_export',
        'dataflow_batch_import',
        'log_customer',
        'log_quote',
        'log_summary',
        'log_summary_type',
        'log_url',
        'log_url_info',
        'log_visitor',
        'log_visitor_info',
        'log_visitor_online',
        'index_event',
        'report_event',
        'report_viewed_product_index',
        'report_compared_product_index',
        'catalog_compare_item',
        'catalogindex_aggregation',
        'catalogindex_aggregation_tag',
        'catalogindex_aggregation_to_tag'
    );
    try {
        $dbh = new PDO('mysql:host=' . $db['host'] . ';port=3306;dbname=' . $db['name'], $db['user'], $db['pass'], array(
            PDO::ATTR_PERSISTENT => false
        ));
        foreach ($tables as $v => $k) {
            echo "Running " . 'TRUNCATE `' . $db['pref'] . $k . '`' . "...\n";
            $stmt = $dbh->prepare('TRUNCATE `' . $db['pref'] . $k . '`');
            $stmt->execute();
            var_dump($stmt);
            echo date("r") . "\n";
        }
        
    }
    catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "\n";
        //die();
    }
    
}

//Clean only old reports of x days
function clean_var_report_directory($magento_dir,$days)
{
    if (is_dir($magento_dir . '/var/report')) {
        echo exec("find " . $magento_dir . "/var/report -type f -mtime +".$days." -delete");
    }
}

//Logrotate of magento logs
function clean_var_log_directory($magento_dir,$days)
{
    if (is_dir($magento_dir . '/var/log')) {
        $log_file_path = str_replace('/','_',$magento_dir);
        $logrotate_mage_file = fopen("/tmp/logrotate_mage".$log_file_path.".conf", "w") or die("Unable to open file!");
        $txt = $magento_dir . "var/log/*.log {\n" .
        "daily\n" .
        "missingok\n" .
        "rotate ".$days."\n" .
        "compress\n" .
        "compresscmd /bin/bzip2\n" .
        "compressoptions -9\n" .
        "compressext .bz2\n" .
        "uncompresscmd /bin/bunzip2\n" .
        "notifempty\n" .
        "nocreate\n" .
        "nosharedscripts\n".
        "prerotate\n".
        "  bash -c \"[[ ! $1 =~ logistics ]] && [[ ! $1 =~ ph2m ]] && [[ ! $1 =~ norotate ]]\"\n".
        "endscript\n".
        "}";
        
        fwrite($logrotate_mage_file, $txt);
        fclose($logrotate_mage_file);
        
        echo exec("/usr/sbin/logrotate -f /tmp/logrotate_mage$log_file_path.conf");
    }
}


exec('find -L /home/ /var/www/ -maxdepth 7 -path \'*/app/etc/*\' -name \'local.xml\'', $lines);

foreach ($lines as $value) {
    
    if (file_exists($value)) {
        $magento_dir = explode("app/etc/local.xml", $value);
        echo "\n- Got another Magento website to clean " . $magento_dir[0] . ":";
        // Load in the local.xml and retrieve the database settings
        $xml = simplexml_load_file($value);
        if (isset($xml->global->resources->default_setup->connection)) {

            //Get default settings
            $conf['log_rotate_magento_app_logs'] = $log_rotate_magento_app_logs;
            $conf['clean_magento_reports'] = $clean_magento_reports ;
            $conf['clean_magento_sessions_files'] = $clean_magento_sessions_files;
            $conf['log_rotate_magento_app_logs_days'] = $log_rotate_magento_app_logs_days;
            $conf['clean_magento_log_php'] = $clean_magento_log_php;
            $conf['clean_magento_reports_days'] = $clean_magento_reports_days;
            $conf['clean_magento_sessions_files_minuts'] = $clean_magento_sessions_files_minuts;


            //Get Database settings
            $connection = $xml->global->resources->default_setup->connection;
            $db['host'] = $connection->host[0];
            $db['name'] = $connection->dbname[0];
            $db['user'] = $connection->username[0];
            $db['pass'] = $connection->password[0];
            $db['pref'] = $connection->table_prefix[0];

            //Get Cleaner settings in magento/app/etc/local.xml / try to get custom settings
            $config = $xml->magentocleaner;

            if ($config->log_rotate_magento_app_logs[0] == '1' or $config->log_rotate_magento_app_logs[0] == '0'){
              $conf['log_rotate_magento_app_logs'] = $config->log_rotate_magento_app_logs[0];
            }
            if ($config->clean_magento_reports[0] == '1' or $config->clean_magento_reports[0] == '0'){
              $conf['clean_magento_reports'] = $config->clean_magento_reports[0];
            }
            if ($config->clean_magento_sessions_files[0] == '1' or $config->clean_magento_sessions_files[0] == '0'){
              $conf['clean_magento_sessions_files'] = $config->clean_magento_sessions_files[0];
            }
            if (is_int ($config->log_rotate_magento_app_logs_days[0])){
              $conf['log_rotate_magento_app_logs_days'] = $config->log_rotate_magento_app_logs_days[0];
            }
            if ($config->clean_magento_log_php[0] == '1' or $config->clean_magento_log_php[0] == '0'){
              $conf['clean_magento_log_php'] = $config->clean_magento_log_php[0];
            }
            if (is_int ($config->clean_magento_reports_days[0])){
              $conf['clean_magento_reports_days'] = $config->clean_magento_reports_days[0];
            }
            if (is_int ($config->clean_magento_sessions_files_minuts[0])){
              $conf['clean_magento_sessions_files_minuts'] = $config->clean_magento_sessions_files_minuts[0];
            }

            echo "\n  - Settings and environment: \n";
            echo '    - Number of magento folders already parsed: ' . $parsed_magento_folders . "\n";
            echo '    - Host : ' . $connection->host[0] . "\n";
            echo '    - Dbname : ' . $connection->dbname[0] . "\n";
            echo '    - Username : ' . $connection->username[0] . "\n";
            echo '    - Pwd : ' . $connection->password[0] . "\n";
            echo '    - Prefix : ' . $connection->table_prefix[0] . "\n";
            echo '    - Log rotate_of logs : ' . $conf['log_rotate_magento_app_logs'] . "\n";
            echo '    - Days of the log rotate : ' . $conf['log_rotate_magento_app_logs_days'] . "\n";
            echo '    - Clean reports : ' . $conf['clean_magento_reports'] . "\n";
            echo '    - Clean reports after days : ' . $conf['clean_magento_reports_days'] . "\n";
            echo '    - Clean sessions files : ' . $conf['clean_magento_sessions_files'] . "\n";
            echo '    - Clean sessions files after minuts : ' . $conf['clean_magento_sessions_files_minuts'] . "\n";
            echo '    - Using shell/log.php : ' . $conf['clean_magento_log_php'] . "\n\n";



            // Verify mysql connection
            try {
                $dbh = new PDO('mysql:host=' . $db['host'] . ';port=3306;dbname=' . $db['name'], $db['user'], $db['pass'], array(
                    PDO::ATTR_PERSISTENT => false
                ));
            }
            catch (PDOException $e) {
                print "Connect to MYSQL Error: !: " . $e->getMessage() . "\n";
                continue;
            }
            
            // Verify and run
            if (is_dir($magento_dir[0] . '/var/session')) {
                $parsed_magento_folders++;
                
                if ($conf['clean_magento_sessions_files'] == '1') {
                    echo "Call clean session files \n";
                    echo exec("find " . $magento_dir[0] . "/var/session -type f -mmin +".$conf['clean_magento_sessions_files_minuts']." -delete");
                }
                
                echo "Call clean_log_tables() \n";
                clean_log_tables();
                
                if ($conf['clean_magento_reports'] == '1') {
                    echo "Call clean_var_directory(" . $magento_dir[0] . " with param ".$conf['clean_magento_reports_days']." days) \n";
                    clean_var_report_directory($magento_dir[0],$conf['clean_magento_reports_days']);
                }
                
                if ($conf['log_rotate_magento_app_logs'] == '1') {
                    echo "Call clean_var_log_directory(" . $magento_dir[0] . ") \n";
                    clean_var_log_directory($magento_dir[0],$conf['log_rotate_magento_app_logs_days']);
                }
                
                if (is_file($magento_dir[0] . '/shell/log.php') and $conf['clean_magento_log_php'] == '1') {
                    echo " log.php exists \n";
                    echo exec("php -q " . $magento_dir[0] . "/shell/log.php clean status") . "\n";
                    echo exec("php -q " . $magento_dir[0] . "/shell/log.php clean --days 1") . "\n";
                    echo exec("php -q " . $magento_dir[0] . "/shell/log.php clean status") . "\n" . "\n";
                }
            } else
                echo "\n ! " . $magento_dir[0] . '/var/session doesnt exists' . "\n";
        }
    } else
        die('Unable to load Magento local xml File');
}
echo "\n End of script, parsed " . $parsed_magento_folders . " magento folders.\n";
