<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007 Harvey Kane <code@ragepank.com>
 * Copyright 2007 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <code@gardyneholt.co.nz>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

if (!defined('_MULTILANGUAGE')) {
    define('_MULTILANGUAGE', Jojo::getOption('multilanguage', 'no') == 'yes');
}
$_PROFILECATEGORIES      = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;

$table = 'profile';
$o = 1;

$tabledisplay = (_MULTILANGUAGE) ? ", ' (', pr_language, ')'" : '';
$menutype = ($_PROFILECATEGORIES) ? 'tree' : 'list';

$default_td[$table]['td_displayfield'] = "CONCAT(pr_name, ' - ' , pr_title$tabledisplay)";
$default_td[$table]['td_parentfield'] = '';
$default_td[$table]['td_orderbyfields'] = "pr_displayorder, pr_name";
$default_td[$table]['td_topsubmit'] = 'yes';
$default_td[$table]['td_filter'] = 'yes';
$default_td[$table]['td_deleteoption'] = 'yes';
$default_td[$table]['td_menutype'] = 'list';
$default_td[$table]['td_categoryfield'] = 'pr_category';
$default_td[$table]['td_categorytable'] = 'profilecategory';
$default_td[$table]['td_group1'] = '';
$default_td[$table]['td_help'] = 'profiles are managed from here.  The system will comfortably take many hundreds of profiles, but you may want to manually delete anything that is no longer relevant, or correct.';

//profile ID
$field = 'profileid';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'readonly';
$default_fd[$table][$field]['fd_help'] = 'A unique ID, automatically assigned by the system';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Title
$field = 'pr_title';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '60';
$default_fd[$table][$field]['fd_help'] = 'Title of the profile.';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';


//Name
$field = 'pr_name';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_size'] = '20';
$default_fd[$table][$field]['fd_help'] = 'The name of the profile subject';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';


//Date
$field = 'pr_date';
$default_fd[$table][$field]['fd_order']     = $o++;
$default_fd[$table][$field]['fd_type']      = 'date';
$default_fd[$table][$field]['fd_default']   = 'NOW()';
$default_fd[$table][$field]['fd_help']      = 'Date the profile was published (defaults to Today)';
$default_fd[$table][$field]['fd_mode']      = 'standard';
$default_fd[$table][$field]['fd_tabname']   = 'Content';

//URL
$field = 'pr_url';
$default_fd[$table][$field]['fd_order']     = $o++;
$default_fd[$table][$field]['fd_type']      = 'internalurl';
$default_fd[$table][$field]['fd_required']  = 'no';
$default_fd[$table][$field]['fd_size']      = '20';
$default_fd[$table][$field]['fd_help']      = 'A customized URL - leave blank to create a URL from the title of the profile';
/* the actual URL may vary here, so query the database to be sure */
$default_fd[$table][$field]['fd_options']   = 'profiles';
$default_fd[$table][$field]['fd_options'] = JOJO_Plugin_Jojo_profile::_getPrefix();
$default_fd[$table][$field]['fd_mode']      = 'standard';
$default_fd[$table][$field]['fd_tabname']   = 'Content';

// Display Order Field
$default_fd[$table]['pr_displayorder'] = array(
        'fd_name' => "Display Order",
        'fd_type' => "order",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

//Body Code
$field = 'pr_description_code';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'texteditor';
$default_fd[$table][$field]['fd_options'] = 'pr_description';
$default_fd[$table][$field]['fd_rows'] = '10';
$default_fd[$table][$field]['fd_cols'] = '50';
$default_fd[$table][$field]['fd_help'] = 'The editor code for the description text.';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Body
$field = 'pr_description';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'hidden';
$default_fd[$table][$field]['fd_rows'] = '10';
$default_fd[$table][$field]['fd_cols'] = '50';
$default_fd[$table][$field]['fd_help'] = 'The body of the profile description.';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Image
$field = 'pr_image';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'fileupload';
$default_fd[$table][$field]['fd_help'] = 'An image for the profile (eg of the author), if  available';
$default_fd[$table][$field]['fd_mode'] = 'standard';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Quote Code
$field = 'pr_quote_code';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'texteditor';
$default_fd[$table][$field]['fd_options'] = 'pr_quote';
$default_fd[$table][$field]['fd_rows'] = '10';
$default_fd[$table][$field]['fd_cols'] = '50';
$default_fd[$table][$field]['fd_help'] = 'The editor code for the body text.';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Body
$field = 'pr_quote';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'hidden';
$default_fd[$table][$field]['fd_rows'] = '10';
$default_fd[$table][$field]['fd_cols'] = '50';
$default_fd[$table][$field]['fd_help'] = 'The body of the profile.';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

// Category Field
$default_fd[$table]['pr_category'] = array(
        'fd_name' => "Category",
        'fd_type' => "dblist",
        'fd_options' => "profilecategory",
        'fd_default' => "0",
        'fd_size' => "20",
        'fd_help' => "If applicable, the category the Profile belongs to",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

//Language
$field = 'pr_language';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'dblist';
$default_fd[$table][$field]['fd_default']   = 'en';
$default_fd[$table][$field]['fd_options'] = 'lang_country';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '20';
$default_fd[$table][$field]['fd_help'] = 'The language/country of the profile';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//HTML Language
$field = 'pr_htmllang';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'dblist';
$default_fd[$table][$field]['fd_default']   = 'en';
$default_fd[$table][$field]['fd_options'] = 'language';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '20';
$default_fd[$table][$field]['fd_help'] = 'The language of the profile (if different from the default language for the language/country chosen above)';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';


/* SEO TAB */


// SEO Title Field
$default_fd[$table]['pr_seotitle'] = array(
        'fd_name' => "SEO Title",
        'fd_type' => "text",
        'fd_options' => "seotitle",
        'fd_size' => "60",
        'fd_help' => "Title of the Profile - it may be worth including your search phrase at the beginning of the title to improve rankings for that phrase.",
        'fd_order' => "1",
        'fd_tabname' => "SEO",
        'fd_mode' => "standard",
    );

// META Description Field
$default_fd[$table]['pr_metadesc'] = array(
        'fd_name' => "META Description",
        'fd_type' => "textarea",
        'fd_options' => "metadescription",
        'fd_rows' => "4",
        'fd_cols' => "60",
        'fd_help' => "A META Description for the profile. By default, a meta description is auto-generated, but hand-written descriptions are always better. This is a recommended field.",
        'fd_order' => "2",
        'fd_tabname' => "SEO",
        'fd_mode' => "advanced",
    );


/* TAGS TAB */
$o = 1;

//Tags
$field = 'pr_tags';
$default_fd[$table][$field]['fd_order']     = $o++;
$default_fd[$table][$field]['fd_name']      = 'Tags';
$default_fd[$table][$field]['fd_type']      = 'tag';
$default_fd[$table][$field]['fd_required']  = 'no';
$default_fd[$table][$field]['fd_options']   = 'jojo_profile';
$default_fd[$table][$field]['fd_showlabel'] = 'no';
$default_fd[$table][$field]['fd_tabname']   = 'Tags';
$default_fd[$table][$field]['fd_help']      = 'A list of words describing the profile';
$default_fd[$table][$field]['fd_mode']      = 'standard';

/* SCHEDULING TAB */
$o = 1;
//Go Live Date
$field = 'pr_livedate';
$default_fd[$table][$field]['fd_order']     = $o++;
$default_fd[$table][$field]['fd_name']      = 'Go Live Date';
$default_fd[$table][$field]['fd_type']      = 'unixdate';
$default_fd[$table][$field]['fd_default']   = 'NOW()';
$default_fd[$table][$field]['fd_help']      = 'The profile will not appear on the site until this date';
$default_fd[$table][$field]['fd_mode']      = 'standard';
$default_fd[$table][$field]['fd_tabname']   = 'Scheduling';

//Expiry Date
$field = 'pr_expirydate';
$default_fd[$table][$field]['fd_order']     = $o++;
$default_fd[$table][$field]['fd_name']      = 'Expiry Date';
$default_fd[$table][$field]['fd_type']      = 'unixdate';
$default_fd[$table][$field]['fd_default']   = 'NOW()';
$default_fd[$table][$field]['fd_help']      = 'The page will be removed from the site after this date';
$default_fd[$table][$field]['fd_mode']      = 'standard';
$default_fd[$table][$field]['fd_tabname']   = 'Scheduling';



$default_td['profilecategory'] = array(
        'td_name' => "profilecategory",
        'td_primarykey' => "profilecategoryid",
        'td_displayfield' => "pc_url",
        'td_filter' => "yes",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_help' => "New Profile Categories are managed from here.",
    );


/* Content Tab */

// Articlecategoryid Field
$default_fd['profilecategory']['profilecategoryid'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => "1",
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// URL Field
$default_fd['profilecategory']['pc_url'] = array(
        'fd_name' => "URL",
        'fd_type' => "internalurl",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "URL for the Profile Category. This will be used for the base URL for all articles in this category. The Page url for this category's home page MUST match the category URL.",
        'fd_order' => "2",
        'fd_tabname' => "Content",
    );

// Sortby Field
$default_fd['profilecategory']['sortby'] = array(
        'fd_name' => "Sortby",
        'fd_type' => "radio",
        'fd_options' => "pr_title asc:Title\npr_date desc:Profile Date\npr_livedate desc:Go Live Date\npr_name:Profile Name\npr_displayorder:Assigned Order",
        'fd_default' => "pr_name",
        'fd_order' => "3",
        'fd_tabname' => "Content",
    );



