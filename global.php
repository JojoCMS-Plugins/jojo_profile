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

$numprofiles = Jojo::getOption('profile_num_sidebar_articles', 3);

$exclude = (boolean)(Jojo::getOption('profile_sidebar_exclude_current', 'no')=='yes');
//some of the profiles we're getting might have expired or not yet gone live, so put in a buffer
$num = $numprofiles + 20;
    /* Create latest profiles array for sidebar: getprofiles(x, start, categoryid) = list x# of profiles */
    if (Jojo::getOption('profile_sidebar_categories', 'no')=='yes') {
        $categories = Jojo::selectQuery("SELECT * FROM {profilecategory}");
        $allprofiles = Jojo_Plugin_Jojo_profile::getProfiles($num, 0, 'all',  'pr_name', $exclude);
        $allprofiles = array_slice ($allprofiles, 0, $numprofiles);
        $smarty->assign('allprofiles',  $allprofiles);
        foreach ($categories as $c) {
            $catprofiles = Jojo_Plugin_Jojo_profile::getProfiles($num, 0, $c['profilecategoryid'],  $c['sortby'], $exclude );
            if (isset($catprofiles[0])) {
                $smarty->assign('profiles_' . str_replace(array('-', '/'), array('_', ''), $catprofiles[0]['pg_url']), $catprofiles);
            }
        }
    } else {
        if (Jojo::getOption('profile_sidebar_randomise', 0) > 0) {
            $num = Jojo::getOption('profile_sidebar_randomise', 0) + 20;
            $recentprofiles = Jojo_Plugin_Jojo_profile::getProfiles($num, 0, 'all',  'pr_name', $exclude);
            $recentprofiles = array_slice ($recentprofiles, 0, Jojo::getOption('profile_sidebar_randomise', 0));
            shuffle($recentprofiles);
        } else {
            $recentprofiles = Jojo_Plugin_Jojo_profile::getProfiles($num, 0, 'all', 'pr_name', $exclude);
        }
        $recentprofiles = array_slice($recentprofiles, 0, $numprofiles);
        $smarty->assign('profiles', $recentprofiles );
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