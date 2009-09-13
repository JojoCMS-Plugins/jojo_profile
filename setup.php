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
    echo "jojo_profile: Adding <b>Profile Categories</b> Page to Edit Content menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Profile Categories', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/profilecategory', pg_parent=?, pg_order=3", array($_ADMIN_CONTENT_ID));
}

/* Ensure there is a folder for uploading profile images */
$res = JOJO::RecursiveMkdir(_DOWNLOADDIR . '/profiles');
if ($res === true) {
    echo "jojo_profile: Created folder: " . _DOWNLOADDIR . '/profiles';
} elseif($res === false) {
    echo 'jojo_profile: Could not automatically create ' .  _DOWNLOADDIR . '/profiles' . 'folder on the server. Please create this folder and assign 777 permissions.';
}

