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

if (Jojo::getOption('profile_num_sidebar_articles') >= 1) {
    $_PROFILECATEGORIES = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;
    $language = (_MULTILANGUAGE) ? $page->getValue('pg_language') : '';
    $exclude = (boolean)(Jojo::getOption('profile_sidebar_exclude_current', 'no')=='yes' );
    
    if ($_PROFILECATEGORIES) {
        $query = "SELECT pageid, pg_title, pg_url, pc.profilecategoryid, pc.sortby FROM {page} p";
            $query .= " LEFT JOIN {profilecategory} pc ON (pg_url=pc.pc_url)";        
            $query .= " WHERE pg_link = ?";        
            $query .= (_MULTILANGUAGE) ? " AND pg_language = '$language'" : '';        
            $categories = Jojo::selectQuery($query, array('Jojo_Plugin_Jojo_profile'));
    }
    /* Create Profiles array for sidebar based on the page language, shuffle them randomly and display as many as are set in options (default is 1)*/
    if ($_PROFILECATEGORIES && count($categories)) {
        foreach ($categories as $c) {
            $category = $c['pg_url'];
            $sortby = $c['sortby'] ? $c['sortby'] : '';
            $categoryid = $c['profilecategoryid'];
            $profiles = JOJO_Plugin_Jojo_profile::getProfiles('', '', $categoryid, $sortby, $exclude);
            shuffle($profiles);
            $profiles = array_slice($profiles, 0, Jojo::getOption('profile_num_sidebar_articles', 1)); 
            $smarty->assign('profiles_' . str_replace('-', '_', $category), $profiles);
            $smarty->assign('profiles_' . str_replace('-', '_', $category) . 'home', JOJO_Plugin_Jojo_profile::_getPrefix('', $page->getValue('pg_language'), $categoryid) );
        }
    }
    $profiles = JOJO_Plugin_Jojo_profile::getProfiles('', '', '', '', $exclude);
    shuffle($profiles);
    $profiles = array_slice($profiles, 0, Jojo::getOption('profile_num_sidebar_articles', 1)); 
    $smarty->assign('profiles', $profiles);
    /* Get the prefix for profiles (can vary for multiple installs) for use in the theme template instead of hard coding it */
    $smarty->assign('profileshome', JOJO_Plugin_Jojo_profile::_getPrefix('', $language) );
}


/** Example usage in theme template:
        {if $profiles}
        <div id="profilebox" class="sidebarbox">
             <h2>Our Guides</h2>
            {foreach from=$profiles key=key item=profile}
                <h3>{$profile.name}</h3>
                {if $profile.imageurl}<img class="quoteimage" src="{$profile.imageurl}" alt="{$profile.name}" />{/if}
                <h4>{$profile.title}</h4>
                <p>{$profile.description|truncate:180:"..."} <a href="{$profile.url}" class="links">&gt; Read more</a></p>
            {/foreach}
            <p  class="links"><a href='{$SITEURL}/{if _MULTILANGUAGE}{$lclanguage}/{/if}{$profileshome}/'>&gt;  See all guide profiles</a></p>
        </div>
        {/if}
*/