<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */


/* Profiles */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_profile'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_profile: Adding <b>Profiles</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Profiles', pg_link='Jojo_Plugin_Jojo_profile', pg_url='profiles'");
}

/* Edit Profiles */
$data = jojo::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/profile'");
if (count($data) == 0) {
    echo "jojo_profile: Adding <b>Edit Profiles</b> Page to menu<br />";
    JOJO::insertQuery("INSERT INTO {page} SET pg_title='Edit Profiles', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/profile', pg_parent=". JOJO::clean($_ADMIN_CONTENT_ID).", pg_order=2");
}

/* Edit Profile Categories */
$data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_url='admin/edit/profilecategory'");
if (!count($data)) {
    echo "jojo_profile: Adding <b>Profile Page Options</b> Page to Content menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Profile Page Options', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/profilecategory', pg_parent=?, pg_order=3", array($_ADMIN_CONTENT_ID));
}

/* Ensure there is a folder for uploading profile images */
$res = JOJO::RecursiveMkdir(_DOWNLOADDIR . '/profiles');
if ($res === true) {
    echo "jojo_profile: Created folder: " . _DOWNLOADDIR . '/profiles';
} elseif($res === false) {
    echo 'jojo_profile: Could not automatically create ' .  _DOWNLOADDIR . '/profiles' . 'folder on the server. Please create this folder and assign 777 permissions.';
}

Jojo::updateQuery("ALTER TABLE {profile} DROP INDEX `title`, ADD FULLTEXT `title` (`pr_title`, `pr_firstname`,`pr_name`)");
Jojo::updateQuery("ALTER TABLE {profile} DROP INDEX `body`, ADD FULLTEXT `body` (`pr_title`, `pr_firstname`, `pr_name`, `pr_quote`, `pr_description`, `pr_department`)");
Jojo::updateQuery("UPDATE {plugin} SET `majorversion`=2, `minorversion`=1 WHERE name='jojo_profile'");

//script to force profiles into categories - should only run once
if (Jojo::getOption('profile_enable_categories')) {

    $categories = jojo::selectAssoc("SELECT pageid AS id, profilecategoryid, pageid FROM {profilecategory}");
    $profiles = Jojo::selectQuery("SELECT profileid, pr_category, pr_language FROM {profile}");
    $profilepages = Jojo::selectQuery("SELECT pageid, pg_url, pg_language FROM {page} WHERE pg_link LIKE 'jojo_plugin_jojo_profile'"); 
    if (Jojo::getOption('profile_enable_categories')=='no') {
        //1st case - no categories and no multilanguage
        if (Jojo::getOption('multilanguage', '') == 'no') {
            /* should only be one profiles page in this case, but you never know.. 
            if there is more than one, only the first page will end up with the categorized profiles in it
            but make categories for any others found anyway so they can be populated manually if desired */
            foreach($profilepages as $k => $page) {
               $pageid = $page['pageid'];
                // if no category for this page id
                if (!count($categories) || !isset($categories[$pageid])) { 
                    $catid[$k] = Jojo::insertQuery("INSERT INTO {profilecategory} (pageid) VALUES ('$pageid')");
                } else {
                    $catid[$k] = $categories[$pageid]['profilecategoryid'];        
                }
            }
            //update all profiles with the first pageid found
            if ($profiles) {
                $cat = array_shift($catid);
                Jojo::updateQuery("UPDATE {profile} SET pr_category = ? ", array($cat));
            }
        // 2nd case - no categories but multilanguage
        } else {
            /* find each profiles page in whatever language,
            make a separate category for it,
            and assign all profiles in that language to that category */
            foreach($profilepages as $k => $page) {
               $pageid = $page['pageid'];
               $pagelanguage = $page['pg_language'];
                // if no category for this page id
                if (!isset($categories[$pageid])) { 
                    $catid = Jojo::insertQuery("INSERT INTO {profilecategory} (pageid) VALUES ('$pageid')");
                } else {
                    $catid = $categories[$pageid]['profilecategoryid'];
                } 
                //update all profiles with the pageid found for that language
                if ($profiles) {
                    foreach ($profiles as $a) {
                        Jojo::updateQuery("UPDATE {profile} SET pr_category = ? WHERE pr_language = ? ", array($catid, $pagelanguage));
                    }
                }
            }            
        }
    } else {
        //3rd case - categories enabled and no multilanguage
        if (Jojo::getOption('multilanguage', '') == 'no') {
            /* check if there are profiles pages that don't have a category set ,
            set a category for them and add any category-less profiles to that category */
            foreach($profilepages as $k => $page) {
               $pageid = $page['pageid'];
                if (!isset($categories[$pageid])){
                     $catid = Jojo::insertQuery("INSERT INTO {profilecategory} (pageid) VALUES ('$pageid')");
                    //update all profiles with no category to use this one
                    if ($profiles) {
                        foreach ($profiles as $a) {
                            Jojo::updateQuery("UPDATE {profile} SET pr_category = ? WHERE pr_category = '0' ", array($catid));
                        }
                    }
                } 
            }
        //4th case - categories enabled and multilanguage
        } else {
            /* check if there are profiles pages that don't have a category set ,
            set a category for them and add any category-less profiles to that category */
            foreach($profilepages as $k => $page) {
                $catid = '';
               $pageid = $page['pageid'];
               $pagelanguage = $page['pg_language'];
                if (!isset($categories[$pageid])) {
                    $catid = Jojo::insertQuery("INSERT INTO {profilecategory} (pageid) VALUES ('$pageid')");
                }
                //update all profiles with the pageid found for that language
                if ($profiles && $catid) {
                    foreach ($profiles as $a) {
                        Jojo::updateQuery("UPDATE {profile} SET pr_category = ? WHERE pr_language = ? AND pr_category= '0' ", array($catid, $pagelanguage));
                    }
                }
            }
        
        }
    }
    //delete option to enable categories
    Jojo::deleteQuery("DELETE FROM {option} WHERE op_name = 'profile_enable_categories' ");
    echo 'profile categories enforced';
}

