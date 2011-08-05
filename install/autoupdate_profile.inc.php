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

$table = 'profile';
$o = 1;

$default_td[$table]['td_displayfield'] = "CONCAT(pr_firstname, ' ', pr_name, ' - ' , pr_title)";
$default_td[$table]['td_parentfield'] = '';
$default_td[$table]['td_orderbyfields'] = "pr_displayorder, pr_name, pr_firstname";
$default_td[$table]['td_topsubmit'] = 'yes';
$default_td[$table]['td_filter'] = 'yes';
$default_td[$table]['td_deleteoption'] = 'yes';
$default_td[$table]['td_menutype'] = 'tree';
$default_td[$table]['td_categoryfield'] = 'pr_category';
$default_td[$table]['td_categorytable'] = 'profilecategory';
$default_td[$table]['td_group1'] = '';
$default_td[$table]['td_help'] = 'profiles are managed from here.  The system will comfortably take many hundreds of profiles, but you may want to manually delete anything that is no longer relevant, or correct.';
$default_td[$table]['td_plugin'] = 'Jojo_profile';

//profile ID
$field = 'profileid';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'readonly';
$default_fd[$table][$field]['fd_help'] = 'A unique ID, automatically assigned by the system';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';


// Category Field
$default_fd[$table]['pr_category'] = array(
        'fd_name' => "Page",
        'fd_type' => "dblist",
        'fd_options' => "profilecategory",
        'fd_default' => "0",
        'fd_size' => "20",
        'fd_help' => "If applicable, the category the Profile belongs to",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

//Title
$field = 'pr_honorific';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '10';
$default_fd[$table][$field]['fd_help'] = 'Honorific title (Mr. Dr. etc)';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//First Name
$field = 'pr_firstname';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '30';
$default_fd[$table][$field]['fd_help'] = 'The first names of the profile subject';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Name
$field = 'pr_name';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_size'] = '30';
$default_fd[$table][$field]['fd_help'] = 'The name of the profile subject';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

// Qualifications
$field = 'pr_quals';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '30';
$default_fd[$table][$field]['fd_help'] = 'Qualifications (MBA, BSc. etc)';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Job Title
$field = 'pr_title';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '70';
$default_fd[$table][$field]['fd_help'] = 'Job Title of the profile.';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Snippet description
$field = 'pr_snippet';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'textarea';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_rows'] = '3';
$default_fd[$table][$field]['fd_help'] = 'Optional short description for snippet.';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';


//Date
$field = 'pr_date';
$default_fd[$table][$field]['fd_order']     = $o++;
$default_fd[$table][$field]['fd_type']      = 'unixdate';
$default_fd[$table][$field]['fd_default']   = 'now';
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

// Comments Field
$default_fd[$table]['pr_comments'] = array(
        'fd_name' => "Comments enabled",
        'fd_type' => "radio",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Whether comments are allowed for this item",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

//Language
$field = 'pr_language';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'hidden';
$default_fd[$table][$field]['fd_default']   = '';
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


/* Location TAB */
$o=0;

//Department
$field = 'pr_department';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '40';
$default_fd[$table][$field]['fd_help'] = 'The name of the department/section';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Location/Contact';

//Phone
$field = 'pr_phone';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '20';
$default_fd[$table][$field]['fd_help'] = 'DDI phone or extension';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Location/Contact';

//Phone
$field = 'pr_fax';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '20';
$default_fd[$table][$field]['fd_help'] = 'DDI fax or extension';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Location/Contact';

//Email
$field = 'pr_email';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'email';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '20';
$default_fd[$table][$field]['fd_help'] = 'email address';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Location/Contact';

//Department
$field = 'pr_room';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '40';
$default_fd[$table][$field]['fd_help'] = 'Room no. / floor etc';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Location/Contact';

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

if (class_exists('Jojo_Plugin_Jojo_Tags')) {
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
}

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
        'td_displayfield' => "pageid",
        'td_filter' => "yes",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_help' => "New Profile Categories are managed from here.",
        'td_plugin' => "Jojo_profile",
    );

$o=0;
$table = 'profilecategory';
/* Content Tab */

// Articlecategoryid Field
$default_fd[$table]['profilecategoryid'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// Page Field
$default_fd[$table]['pageid'] = array(
        'fd_name' => "Page",
        'fd_type' => "dbpluginpagelist",
        'fd_options' => "jojo_plugin_jojo_profile",
        'fd_readonly' => "1",
        'fd_default' => "0",
        'fd_help' => "The artciles page on the site used for this category.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// URL Field
$default_fd['profilecategory']['pc_url'] = array(
        'fd_name' => "URL",
        'fd_type' => "hidden",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "URL for the Profile Category. This will be used for the base URL for all articles in this category. The Page url for this category's home page MUST match the category URL.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );


// Type Field
$default_fd[$table]['type'] = array(
        'fd_name' => "Type",
        'fd_type' => "radio",
        'fd_options' => "normal:Normal\nparent:Parent\nindex:All",
        'fd_readonly' => "0",
        'fd_default' => "normal",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Sortby Field
$default_fd[$table]['sortby'] = array(
        'fd_name' => "Sortby",
        'fd_type' => "radio",
        'fd_options' => "pr_title asc:Title\npr_date desc:Profile Date\npr_livedate desc:Go Live Date\npr_name:Profile Name\npr_displayorder:Assigned Order",
        'fd_default' => "pr_name",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Add to Nav 
$default_fd[$table]['addtonav'] = array(
        'fd_name' => "Show Profiles in Nav",
        'fd_type' => "yesno",
        'fd_help' => "Add profiles to navigation as child pages of this one.",
        'fd_default' => "0",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );
    
// Snippet Length Field
$default_fd[$table]['snippet'] = array(
        'fd_name' => "Snippet Length",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "full",
        'fd_help' => "Truncate index snippets to this many characters. Use 'full' for no snipping.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Read more link text 
$default_fd[$table]['readmore'] = array(
        'fd_name' => "Read more link",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => '> Read more',
        'fd_help' => "The link text to read the full item",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Show Date
$default_fd[$table]['showdate'] = array(
        'fd_name' => "Show Post Date",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "1",
        'fd_help' => "Show date added on posts",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Date format Field
$default_fd[$table]['dateformat'] = array(
        'fd_name' => "Date Format",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "%e %b %Y",
        'fd_help' => "Format the time and/or date according to locale settings. See php.net/strftime for details",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Thumbnail sizing Field
$default_fd[$table]['thumbnail'] = array(
        'fd_name' => "Thumbnail Size",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "s150",
        'fd_help' => "image thumbnail sizing in index eg: 150x200, h200, v4000",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Main image sizing 
$default_fd[$table]['mainimage'] = array(
        'fd_name' => "Main Image",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "v60000",
        'fd_help' => "image thumbnail sizing in index eg: 150x200, h200, v4000",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

if (class_exists('Jojo_Plugin_Jojo_comment')) {
// Allow Comments
$default_fd[$table]['comments'] = array(
        'fd_name' => "Enable comments",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "1",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );
}

/* add many to many table for use by newsletter plugin if present */
if (class_exists('Jojo_Plugin_Jojo_Newsletter')) {
$default_fd['newsletter']['profiles'] = array(
        'fd_name' => "Profiles To Include",
        'fd_type' => "many2manyordered",
        'fd_size' => "0",
        'fd_rows' => "0",
        'fd_cols' => "0",
        'fd_showlabel' => "no",
        'fd_tabname' => "2. Profiles",
        'fd_m2m_linktable' => "newsletter_profile",
        'fd_m2m_linkitemid' => "newsletterid",
        'fd_m2m_linkcatid' => "profileid",
        'fd_m2m_cattable' => "profile",
    );
}
