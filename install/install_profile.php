<?php

$table = 'profile';
$query = "
    CREATE TABLE {profile} (
      `profileid` int(11) NOT NULL auto_increment,
      `pr_title` varchar(255) NOT NULL default '',
      `pr_date` date default NULL,
      `pr_url` varchar(255) NOT NULL default '',
      `pr_quote` text NULL,
      `pr_quote_code` text NULL,
      `pr_image` varchar(255) NOT NULL default '',
      `pr_name` varchar(255) NOT NULL default '',
      `pr_description` text NULL,
      `pr_description_code` text NULL,
      `pr_language` varchar(100) NOT NULL default '',
      `pr_htmllang` varchar(100) NOT NULL default '',
      `pr_category` int(11) NOT NULL default '0',
      `pr_displayorder` int(11) NOT NULL default '0',
      `pr_tags` text NULL,
      `pr_livedate` int(11) NOT NULL default '0',
      `pr_expirydate` int(11) NOT NULL default '0',
      `pr_seotitle` varchar(255) NOT NULL default '',
      `pr_metadesc` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`profileid`),
      FULLTEXT KEY `title` (`pr_title`),
      FULLTEXT KEY `body` (`pr_title`, `pr_name`, `pr_quote`, `pr_description`)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci  AUTO_INCREMENT=1000;";


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
      `pc_url` varchar(255) NOT NULL default '',
      `sortby` ENUM('pr_title asc','pr_date desc','pr_livedate desc','pr_name','pr_displayorder') NOT NULL default 'pr_name',
      PRIMARY KEY  (`profilecategoryid`)
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