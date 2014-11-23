<?php

// Author : Alex Sbille

$parsed_magento_folders='';

function clean_log_tables() {
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
            $dbh = new PDO('mysql:host='.$db['host'].';port=3306;dbname='.$db['name'], $db['user'], $db['pass'], array( PDO::ATTR_PERSISTENT => false));
            foreach($tables as $v => $k) {
                echo "Running ".'TRUNCATE `'.$db['pref'].$k.'`'."...\n";
                $stmt = $dbh->prepare('TRUNCATE `'.$db['pref'].$k.'`');
                $stmt->execute();
                var_dump($stmt);
                echo date("r")."\n";
            }
    
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "\n";
        //die();
    }


        /*
        mysql_connect($db['host'], $db['user'], $db['pass']) or die(mysql_error());
        mysql_select_db($db['name']) or die(mysql_error());
        foreach($tables as $v => $k) {
            echo 'Query for dbname'.$db['name'].' : TRUNCATE `'.$db['pref'].$k.'`'."\n";
            $result = mysql_query('TRUNCATE `'.$db['pref'].$k.'`') or print(mysql_error());
            echo $result."\n";
        }*/
}

function clean_var_directory($magento_dir) {
        if(empty($magento_dir)){echo 'empty magento_dir!'; exit();}
        $dirs = array(
        $magento_dir.'var/log/',
        $magento_dir.'var/report/'
        );

        foreach($dirs as $v => $k) {
            if(empty($k)){
                echo 'empty $k'; exit();
            }
            else{
                echo'deleted'.$k."\n";
                exec('rm -rf '.$k.'/*');
            }
        }
}

$lines = shell_exec('find /home/ -maxdepth 6 -path \'*/app/etc/*\' -name \'local.xml\'  | xargs grep -l "Magento" > /tmp/listmagento.tmp');
$lines = file('/tmp/listmagento.tmp', FILE_IGNORE_NEW_LINES);
foreach ($lines as $value) {
if(file_exists($value)) {

$magento_dir = explode("app/etc/local.xml", $value);
echo "\n \n--- Got another Magento website to clean ".$magento_dir[0]."\n";

    // Load in the local.xml and retrieve the database settings
    $xml = simplexml_load_file($value);
    if(isset($xml->global->resources->default_setup->connection)) {
        $connection = $xml->global->resources->default_setup->connection;
        echo 'Host : '.$connection->host[0]."\n";
        echo 'Dbname : '.$connection->dbname[0]."\n";
        echo 'Username : '.$connection->username[0]."\n";
        echo 'Pwd : '.$connection->password[0]."\n";
        echo 'Prefix : '.$connection->table_prefix[0]."\n";
        $db['host'] = $connection->host[0];
        $db['name'] = $connection->dbname[0];
        $db['user'] = $connection->username[0];
        $db['pass'] = $connection->password[0];
        $db['pref'] = $connection->table_prefix[0];

        // Verify
        if(is_dir($magento_dir[0].'/var/session')){
        $parsed_magento_folders++;
        echo exec("find ".$magento_dir[0]."/var/session -type f -mmin +600 -delete");
        echo "Call clean_log_tables() \n";
        clean_log_tables();
        echo "Call clean_var_directory(".$magento_dir[0].") \n";
        clean_var_directory($magento_dir[0]);

        if(is_file($magento_dir[0].'/shell/log.php')){
            echo " log.php exists \n";
            echo exec("php -q ".$magento_dir[0]."/shell/log.php clean status")."\n";
            echo exec("php -q ".$magento_dir[0]."/shell/log.php clean --days 1")."\n";
            echo exec("php -q ".$magento_dir[0]."/shell/log.php clean status")."\n"."\n";
        }}
        else {
            echo "\n ! ".$magento_dir[0].'/var/session doesnt exists'."\n";
        }
    }
    } else {
    die('Unable to load Magento local xml File');
}}
echo "\n End of script, parsed ".$parsed_magento_folders." magento folders.";
?>
