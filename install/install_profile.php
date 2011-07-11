<?php

$table = 'profile';
$query = "
    CREATE TABLE {profile} (
      `profileid` int(11) NOT NULL auto_increment,
      `pr_firstname` varchar(255) NOT NULL default '',
      `pr_name` varchar(255) NOT NULL default '',
      `pr_honorific` varchar(255) NOT NULL default '',
      `pr_quals` varchar(100) NOT NULL default '',
      `pr_title` varchar(255) NOT NULL default '',
      `pr_department` varchar(255) NOT NULL default '',
      `pr_date` int(11) default '0',
      `pr_url` varchar(255) NOT NULL default '',
      `pr_quote` text NULL,
      `pr_quote_code` text NULL,
      `pr_image` varchar(255) NOT NULL default '',
      `pr_snippet` text NULL,
      `pr_description` text NULL,
      `pr_description_code` text NULL,
      `pr_language` varchar(100) NOT NULL default '',
      `pr_htmllang` varchar(100) NOT NULL default '',
      `pr_category` int(11) NOT NULL default '0',
      `pr_displayorder` int(11) NOT NULL default '0',
      `pr_phone` varchar(255) NOT NULL default '',
      `pr_fax` varchar(100) NOT NULL default '',
      `pr_email` varchar(255) NOT NULL default '',
      `pr_room` varchar(255) NOT NULL default '',";
if (class_exists('Jojo_Plugin_Jojo_Tags')) {
$query .= "
      `pr_tags` text NULL,";
}
 $query .= "
      `pr_livedate` int(11) NOT NULL default '0',
      `pr_expirydate` int(11) NOT NULL default '0',
      `pr_seotitle` varchar(255) NOT NULL default '',
      `pr_metadesc` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`profileid`),
      FULLTEXT KEY `title` (`pr_title`, `pr_firstname`,`pr_name`),
      FULLTEXT KEY `body` (`pr_title`, `pr_firstname`, `pr_name`, `pr_quote`, `pr_description`, `pr_department`),
      KEY `category` (`pr_category`)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci  AUTO_INCREMENT=1000;";

/* Convert mysql date format to unix timestamps */
if (Jojo::tableExists($table) && Jojo::getMySQLType($table, 'pr_date') == 'date') {
    date_default_timezone_set(Jojo::getOption('sitetimezone', 'Pacific/Auckland'));
    $items = Jojo::selectQuery("SELECT profileid, pr_date FROM {profile}");
    Jojo::structureQuery("ALTER TABLE  {profile} CHANGE  `pr_date`  `pr_date` INT(11) NOT NULL DEFAULT '0'");
    foreach ($items as $k => $a) {
        if ($a['pr_date']!='0000-00-00') {
            $timestamp = strtotime($a['pr_date']);
        } else {
            $timestamp = 0;
        }
       Jojo::updateQuery("UPDATE {profile} SET pr_date=? WHERE profileid=?", array($timestamp, $a['profileid']));
    }
}

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_profile: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_profile: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);

$table = 'profilecategory';
$query = "
    CREATE TABLE {profilecategory} (
      `profilecategoryid` int(11) NOT NULL auto_increment,
      `sortby` ENUM('pr_title asc','pr_date desc','pr_livedate desc','pr_name','pr_displayorder') NOT NULL default 'pr_name',
      `addtonav` tinyint(1) NOT NULL default '0',
      `pageid` int(11) NOT NULL default '0',
      `type` enum('normal','parent','index') NOT NULL default 'normal',
      `pageid` int(11) NOT NULL default '0',
      `showdate` tinyint(1) NOT NULL default '0',
      `dateformat` varchar(255) NOT NULL default '%e %b %Y',
      `snippet` varchar(255) NOT NULL default '400',
      `readmore` varchar(255) NOT NULL default '> Read more',
      `thumbnail` varchar(255) NOT NULL default '',
      `mainimage` varchar(255) NOT NULL default 'v60000',";
if (class_exists('Jojo_Plugin_Jojo_comment')) {
    $query .= "
     `comments` tinyint(1) NOT NULL default '0',";
}
$query .= "
      PRIMARY KEY  (`profilecategoryid`),
      KEY `id` (`pageid`)
    ) TYPE=MyISAM ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_profile: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_profile: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);

/* add relational table for use by newsletter plugin if present */
if (class_exists('Jojo_Plugin_Jojo_Newsletter')) {
    $table = 'newsletter_profile';
    $query = "CREATE TABLE {newsletter_profile} (
      `newsletterid` int(11) NOT NULL,
      `profileid` int(11) NOT NULL,
      `order` int(11) NOT NULL
    );";
    
    /* Check table structure */
    $result = Jojo::checkTable($table, $query);
    
    /* Output result */
    if (isset($result['created'])) {
        echo sprintf("jojo_newsletter_phplist: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
    }
    
    if (isset($result['added'])) {
        foreach ($result['added'] as $col => $v) {
            echo sprintf("jojo_newsletter_phplist: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
        }
    }
    
    if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);

    /* add the new field to the newsletter table if it does not exist */
    if (Jojo::tableExists('newsletter') && !Jojo::fieldExists('newsletter', 'profiles')) {
        Jojo::structureQuery("ALTER TABLE `newsletter` ADD `profiles` TEXT NOT NULL;");
    }

}
