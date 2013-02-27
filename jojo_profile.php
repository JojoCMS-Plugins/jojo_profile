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
 * @name  Harvey Kane <code@ragepank.com>
 * @name  Michael Cochrane <mikec@jojocms.org>
 * @name  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class Jojo_Plugin_Jojo_profile extends Jojo_Plugin
{

    /* Get articles  */
    public static function getProfiles($num=false, $start = 0, $categoryid='all', $sortby='pr_displayorder, pr_name', $exclude=false, $include=false) {
        global $page;
        if ($categoryid == 'all' && $include != 'alllanguages') {
            $categoryid = array();
            $sectionpages = self::getPluginPages('', $page->page['root']);
            foreach ($sectionpages as $s) {
                $categoryid[] = $s['profilecategoryid'];
            }
        }
        if (is_array($categoryid)) {
             $categoryquery = " AND pr_category IN ('" . implode("','", $categoryid) . "')";
        } else {
            $categoryquery = is_numeric($categoryid) ? " AND pr_category = '$categoryid'" : '';
        }
        /* if calling page is an profile, Get current profile, exclude from the list and up the limit by one */
        $exclude = ($exclude && Jojo::getOption('profile_sidebar_exclude_current', 'no')=='yes' && $page->page['pg_link']=='jojo_plugin_jojo_profile' && (Jojo::getFormData('id') || Jojo::getFormData('url'))) ? (Jojo::getFormData('url') ? Jojo::getFormData('url') : Jojo::getFormData('id')) : '';
        if ($num && $exclude) $num++;
        $shownumcomments = (boolean)(class_exists('Jojo_Plugin_Jojo_comment') && Jojo::getOption('comment_show_num', 'no') == 'yes');
        $query  = "SELECT pr.*, c.*, p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_livedate, pg_expirydate";
        $query .= $shownumcomments ? ", COUNT(com.itemid) AS numcomments" : '';
        $query .= " FROM {profile} pr";
        $query .= " LEFT JOIN {profilecategory} c ON (pr.pr_category=c.profilecategoryid) LEFT JOIN {page} p ON (c.pageid=p.pageid)";
        $query .= $shownumcomments ? " LEFT JOIN {comment} com ON (com.itemid = pr.profileid AND com.plugin = 'jojo_profile')" : '';
        $query .= " WHERE 1" . $categoryquery;
        $query .= $shownumcomments ? " GROUP BY profileid" : '';
        $query .= $num ? " ORDER BY $sortby LIMIT $start,$num" : '';
        $profiles = Jojo::selectQuery($query);
        $profiles = self::cleanItems($profiles, $exclude, $include);
        if (!$num)  $profiles = self::sortItems($profiles, $sortby);
        $profiles = array_values($profiles);
        return $profiles;
    }

     /* get items by id - accepts either an array of ids returning a results array, or a single id returning a single result  */
    public static function getItemsById($ids = false, $sortby='pr_displayorder, pr_name') {
        $query  = "SELECT pr.*, c.*, p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_livedate, pg_expirydate";
        $query .= " FROM {profile} pr";
        $query .= " LEFT JOIN {profilecategory} c ON (pr.pr_category=c.profilecategoryid) LEFT JOIN {page} p ON (c.pageid=p.pageid)";
        $query .=  is_array($ids) ? " WHERE profileid IN ('". implode("',' ", $ids) . "')" : " WHERE profileid=$ids";
        $items = Jojo::selectQuery($query);
        $items = self::cleanItems($items);
        $items = is_array($ids) ? self::sortItems($items, $sortby) : $items[0];
        return $items;
    }

    /* clean items for output */
    private static function cleanItems($items, $exclude=false, $include=false) {
        $now    = time();
        foreach ($items as $k=>&$i){
            $pagedata = Jojo_Plugin_Core::cleanItems(array($i), $include);
            if (!$pagedata || $i['pr_livedate']>$now || (!empty($i['pr_expirydate']) && $i['pr_expirydate']<$now) || (!empty($i['profileid']) && $i['profileid']==$exclude)  || (!empty($i['pr_url']) && $i['pr_url']==$exclude)) {
                unset($items[$k]);
                continue;
            }
            $i['pagetitle'] = $pagedata[0]['title'];
            $i['pageurl']   = $pagedata[0]['url'];
            $i['id']           = $i['profileid'];
            $i['title']        = htmlspecialchars($i['pr_title'], ENT_COMPAT, 'UTF-8', false);
            $i['firstname']    = isset($i['pr_firstname']) ? htmlspecialchars($i['pr_firstname'], ENT_COMPAT, 'UTF-8', false) : '';
            $i['name']         = htmlspecialchars($i['pr_name'], ENT_COMPAT, 'UTF-8', false);
            $i['honorific']     =  isset($i['pr_honorific']) ? htmlspecialchars($i['pr_honorific'], ENT_COMPAT, 'UTF-8', false) : '';
            $i['quals']            = isset($i['pr_quals']) ? htmlspecialchars($i['pr_quals'], ENT_COMPAT, 'UTF-8', false) : '';
            $i['fullname']      = (!empty($i['honorific']) ? $i['honorific'] . ' ' : '') . (!empty($i['firstname']) ? $i['firstname'] . ' ' : '') . $i['name'];
            $i['department']        =  isset($i['pr_department']) ? htmlspecialchars($i['pr_department'], ENT_COMPAT, 'UTF-8', false) : '';
            $i['phone']        =  isset($i['pr_phone']) ? htmlspecialchars($i['pr_phone'], ENT_COMPAT, 'UTF-8', false) : '';
            $i['fax']        =  isset($i['pr_fax']) ? htmlspecialchars($i['pr_fax'], ENT_COMPAT, 'UTF-8', false) : '';
            $i['room']      =  isset($i['pr_room']) ? htmlspecialchars($i['pr_room'], ENT_COMPAT, 'UTF-8', false) : '';
            // Snip for the index description
            $splitcontent = Jojo::iExplode('[[snip]]', $i['pr_description']);
            $i['description'] = array_shift($splitcontent);
            /* Strip all tags and template include code ie [[ ]] */
            $i['description'] = preg_replace('/\[\[.*?\]\]/', '',  trim(strip_tags($i['description'])));
            $i['quote'] = preg_replace('/\[\[.*?\]\]/', '',  trim(strip_tags($i['pr_quote'])));
            $i['bodyplain']  = $i['description'] . ' ' . $i['quote'];
            $i['desc'] = isset($i['pr_snippet']) && $i['pr_snippet'] ? htmlspecialchars(nl2br($i['pr_snippet']), ENT_COMPAT, 'UTF-8', false) : (strlen($i['bodyplain']) >400 ?  substr($mbody=wordwrap($i['bodyplain'], 400, '$$'), 0, strpos($mbody,'$$')) . '...' : $i['bodyplain']);
            $i['snippet']       = isset($i['snippet']) ? $i['snippet'] : '400';
            $i['thumbnail']       = isset($i['thumbnail']) ? $i['thumbnail'] : 's150';
            $i['mainimage']       = isset($i['mainimage']) ? $i['mainimage'] : 'v60000';
            $i['readmore'] = isset($i['readmore']) ? str_replace(' ', '&nbsp;', htmlspecialchars($i['readmore'], ENT_COMPAT, 'UTF-8', false)) : '&gt;&nbsp;read&nbsp;more';
            $i['date']       = $i['pr_date'];
            $i['datefriendly'] = isset($i['dateformat']) && !empty($i['dateformat']) ? strftime($i['dateformat'], $i['pr_date']) :  Jojo::formatTimestamp($i['pr_date'], "medium");
            $i['image'] = !empty($i['pr_image']) ? 'profiles/' . $i['pr_image'] : '';
            $i['url']          = self::getUrl($i['profileid'], $i['pr_url'], $i['fullname'], $i['pageid'], $i['pr_category']);
            $i['plugin']     = 'jojo_profile';
            unset($items[$k]['pr_description_code']);
        }
        return $items;
    }

    /* sort items for output */
    private static function sortItems($items, $sortby=false) {
        if ($sortby) {
            $order = "name";
            $reverse = false;
            switch ($sortby) {
              case "pr_date desc":
                $order="date";
                $reverse = true;
                break;
              case "pr_name":
                $order="name";
                break;
              case "pr_title asc":
                $order="title";
                break;
              case "pr_displayorder":
                $order="order";
                break;
            }
            usort($items, array('Jojo_Plugin_Jojo_profile', $order . 'sort'));
            $items = $reverse ? array_reverse($items) : $items;
        }
        return $items;
    }


    private static function namesort($a, $b)
    {
         if ($a['pr_name']) {
            return strcmp($a['pr_name'],$b['pr_name']);
        }
    }

    private static function titlesort($a, $b)
    {
         if ($a['pr_title']) {
            return strcmp($a['pr_title'],$b['pr_title']);
        }
    }

    private static function datesort($a, $b)
    {
         if ($a['pr_date']) {
            return strnatcasecmp($a['pr_date'],$b['pr_date']);
         }
    }

    private static function ordersort($a, $b)
    {
         if ($a['pr_displayorder']) {
            return strnatcasecmp($a['pr_displayorder'],$b['pr_displayorder']);
         }
    }

    /*
     * calculates the URL - requires the ID, but works without a query if given the URL or title from a previous query
     *
     */
    public static function getUrl($id=false, $url=false, $title=false, $pageid=false, $category=false )
    {
        $pageprefix = Jojo::getPageUrlPrefix($pageid);

        /* URL specified */
        if (!empty($url)) {
            return $pageprefix . self::_getPrefix($category) . '/' . $url . '/';
         }
        /* ID + title specified */
        if ($id && !empty($title)) {
            return $pageprefix . self::_getPrefix($category) . '/' . $id . '/' .  Jojo::cleanURL($title) . '/';
        }
        /* use the ID to find either the URL or title */
        if ($id) {
            $item = Jojo::selectRow("SELECT pr.*, p.pageid FROM {profile} pr LEFT JOIN {profilecategory} c ON (pr.pr_category=c.profilecategoryid) LEFT JOIN {page} p ON (c.pageid=p.pageid) WHERE profileid = ?", array($id));
             if ($item) {
                 $item['fullname'] = (!empty($item['pr_honorific']) ? $item['pr_honorific'] . ' ' : '') . (!empty($item['pr_firstname']) ? $item['pr_firstname'] . ' ' : '') . $item['pr_name'];
                return self::getUrl($id, $item['pr_url'], $item['fullname'], $item['pageid'], $item['pr_category']);
            }
         }
        /* No matching ID or no ID supplied */
        return false;
    }

    function _getContent()
    {
        global $smarty;
        $content = array();
        $pageid = $this->page['pageid'];
        $pageprefix = Jojo::getPageUrlPrefix($pageid);
        $smarty->assign('multilangstring', $pageprefix);

        if (class_exists('Jojo_Plugin_Jojo_comment') && Jojo::getOption('comment_subscriptions', 'no') == 'yes') {
            Jojo_Plugin_Jojo_comment::processSubscriptionEmails();
        }

        /* Are we looking at an profile or the index? */
        $profileid = Jojo::getFormData('id',        0);
        $url       = Jojo::getFormData('url',      '');
        $action    = Jojo::getFormData('action',   '');
        $categorydata =  Jojo::selectRow("SELECT * FROM {profilecategory} WHERE pageid = ?", $pageid);
        $categorydata['type'] = isset($categorydata['type']) ? $categorydata['type'] : 'normal';
        if ($categorydata['type']=='index') {
            $categoryid = 'all';
        } elseif ($categorydata['type']=='parent') {
            $childcategories = Jojo::selectQuery("SELECT profilecategoryid FROM {page} p LEFT JOIN {profilecategory} c ON (c.pageid=p.pageid) WHERE pg_parent = ? AND pg_link = 'jojo_plugin_jojo_profile'", $pageid);
            foreach ($childcategories as $c) {
                $categoryid[] = $c['profilecategoryid'];
            }
            $categoryid[] = $categorydata['profilecategoryid'];
        } else {
            $categoryid = $categorydata['profilecategoryid'];
        }
        $sortby = $categorydata ? $categorydata['sortby'] : '';
        /* Get sortby post */
        if (isset($_POST['sortbyfield'])) {
               switch ($_POST['sortbyfield']) {
                case 'name':
                    $sortby = 'pr_name';
                     $sortbyfield = 'name';
                   break;
                case 'title':
                     $sortby = 'pr_title';
                     $sortbyfield = 'title';
                   break;
                case 'date':
                     $sortby = 'pr_date desc';
                     $sortbyfield = 'date';
                   break;
                case 'order':
                     $sortby = 'pr_displayorder';
                     $sortbyfield = 'order';
                   break;
                 case 'location':
                     $sortby = 'pr_location, pr_displayorder, pr_name';
                     $sortbyfield = 'location';
                   break;
                case 'department':
                     $sortby = 'pr_department, pr_displayorder, pr_name';
                     $sortbyfield = 'department';
                  break;
             }
        } else {
               switch ($sortby) {
                case 'pr_name':
                     $sortbyfield = 'name';
                   break;
                case 'pr_title asc':
                     $sortbyfield = 'title';
                   break;
                case 'pr_date desc':
                     $sortbyfield = 'date';
                   break;
                case 'pr_displayorder':
                     $sortbyfield = 'order';
                   break;
                 case 'pr_livedate desc':
                     $sortbyfield = 'date';
                   break;
                case '':
                     $sortbyfield = '';
                   break;
             }
        }
        $smarty->assign('sortby', $sortbyfield);

        /* handle unsubscribes */
        if ($action == 'unsubscribe') {
            $code      = Jojo::getFormData('code',      '');
            $profileid = Jojo::getFormData('profileid', '');
            if (Jojo_Plugin_Jojo_comment::removeSubscriptionByCode($code, $profileid, 'jojo_profile')) {
                $content['content'] = 'Subscription removed.<br />';
            } else {
                $content['content'] = 'This unsubscribe link is inactive, or you have already been unsubscribed.<br />';
            }
            $content['content'] .= 'Return to <a href="' . self::getUrl($profileid) . '">profile</a>.';
            return $content;
        }

        $profiles = self::getprofiles('', '', $categoryid, $sortby, $exclude=false, $include='showhidden');

        if ($profileid || !empty($url)) {
            /* find the current, next and previous items */
            $profile = array();
            $prevprofile = array();
            $nextprofile = array();
            $next = false;
            foreach ($profiles as $a) {
                if (!empty($url) && $url==$a['pr_url']) {
                    $profile = $a;
                    $next = true;
               } elseif ($profileid==$a['profileid']) {
                    $profile = $a;
                    $next = true;
                } elseif ($next==true) {
                    $nextprofile = $a;
                     break;
                } else {
                    $prevprofile = $a;
                }
            }

            /* If the item can't be found, return a 404 */
            if (!$profile) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
            }

            /* Get the specific profile */
            $profileid = $profile['profileid'];

            /* calculate the next and previous profiles */
            if (Jojo::getOption('profile_next_prev') == 'yes') {
                if (!empty($nextprofile)) {
                    $smarty->assign('nextprofile', $nextprofile);
                }
                if (!empty($prevprofile)) {
                    $smarty->assign('prevprofile', $prevprofile);
                }
            }

            /* Get tags if used */
            if (class_exists('Jojo_Plugin_Jojo_Tags')) {
                /* Split up tags for display */
                $tags = Jojo_Plugin_Jojo_Tags::getTags('jojo_profile', $profileid);
                $smarty->assign('tags', $tags);

                /* generate tag cloud of tags belonging to this profile */
                $profile_tag_cloud_minimum = Jojo::getOption('profile_tag_cloud_minimum');
                if (!empty($profile_tag_cloud_minimum) && ($profile_tag_cloud_minimum < count($tags))) {
                    $itemcloud = Jojo_Plugin_Jojo_Tags::getTagCloud('', $tags);
                    $smarty->assign('itemcloud', $itemcloud);
                }
               /* get related profiles if tags plugin installed and option enabled */
                $numrelated = Jojo::getOption('profile_num_related');
                if ($numrelated) {
                    $related = Jojo_Plugin_Jojo_Tags::getRelated('jojo_profile', $profileid, $numrelated, 'jojo_profile'); //set the last argument to 'jojo_profile' to restrict results to only profiles
                    $smarty->assign('related', $related);
                }
            }

            /* Get Comments if used */
            if (class_exists('Jojo_Plugin_Jojo_comment') && (!isset($profile['comments']) || $profile['comments']) ) {
                /* Was a comment submitted? */
                if (Jojo::getFormData('comment', false)) {
                    Jojo_Plugin_Jojo_comment::postComment($profile);
                }
               $commentsenabled = (boolean)(isset($profile['pr_comments']) && $profile['pr_comments']=='yes');
               $commenthtml = Jojo_Plugin_Jojo_comment::getComments($profile['id'], $profile['plugin'], $profile['pageid'], true);
               $smarty->assign('commenthtml', $commenthtml);
            }

            /* Add breadcrumb */
            $breadcrumbs                      = $this->_getBreadCrumbs();
            $breadcrumb                       = array();
            $breadcrumb['name']               = $profile['fullname'] . ($profile['title'] ? ' - ' . $profile['title'] : '');
            $breadcrumb['rollover']           = $profile['title'];
            $breadcrumb['url']                = $profile['url'];
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;

            /* Assign profile content to Smarty */
            $smarty->assign('jojo_profile', $profile);

            /* Prepare fields for display */
            if (isset($profile['pr_htmllang'])) {
                // Override the language setting on this page if necessary.
                $content['pg_htmllang'] = $profile['pr_htmllang'];
                $smarty->assign('pg_htmllang', $profile['pr_htmllang']);
            }
            $content['title']            =  $profile['fullname'] . (!empty($profile['quals']) ? ' <span class="qualifications">' . $profile['quals'] . '</span>' : '');
            $content['seotitle']         = Jojo::either($profile['pr_seotitle'], $profile['fullname'] . ' - ' . $profile['title']);
            $content['breadcrumbs']      = $breadcrumbs;

            if (!empty($profile['pr_metadesc'])) {
                $content['meta_description'] = $profile['pr_metadesc'];
            } else {
                $meta_description_template = Jojo::getOption('profile_meta_description', '[title] - [body]... ');
                $metafilters = array(
                        '[title]',
                        '[site]',
                        '[body]'
                        );
                $metafilterreplace = array(
                        $profile['name'] . ' - ' . $profile['title'],
                        _SITETITLE,
                        $profile['desc'],
                        );
                        $content['meta_description'] = str_replace($metafilters, $metafilterreplace, $meta_description_template);
            }
            $content['metadescription']  = $content['meta_description'];
            if ((boolean)(Jojo::getOption('ogdata', 'no')=='yes')) {
                $content['ogtags']['description'] = $profile['desc'];
                $content['ogtags']['image'] = $profile['image'] ? _SITEURL .  '/images/' . ($profile['thumbnail'] ? $profile['thumbnail'] : 's150') . '/' . $profile['image'] : '';
                $content['ogtags']['title'] = $profile['name'] . ' - ' . $profile['title'];
            }

            $content['content'] = $smarty->fetch('jojo_profile.tpl');

        } else {

            /* profile index section */
            $pagenum = Jojo::getFormData('pagenum', 1);
            if ($pagenum[0] == 'p') {
                $pagenum = substr($pagenum, 1);
            }
            $letters = array_flip(range('A', 'Z'));
            foreach ($letters as $k=>&$l) $l=0 ;
            foreach ($profiles as $k=>$p) {
                    $letters[strtoupper(substr($p['name'], 0, 1))] = 1;
            }
            $smarty->assign('letters',$letters);

            /* get number of profiles for pagination */
            $profilesperpage = Jojo::getOption('profilesperpage', 40);
            $start = ($profilesperpage * ($pagenum-1));
            $numprofiles = count($profiles);
            $numpages = ceil($numprofiles / $profilesperpage);
            /* calculate pagination */
            if ($numpages == 1) {
                $pagination = '';
            } elseif ($numpages == 2 && $pagenum == 2) {
                $pagination = sprintf('<a href="%s/p1/">previous...</a>', $pageprefix . self::_getPrefix($categoryid) );
            } elseif ($numpages == 2 && $pagenum == 1) {
                $pagination = sprintf('<a href="%s/p2/">more...</a>', $pageprefix . self::_getPrefix($categoryid) );
            } else {
                $pagination = '<ul>';
                for ($p=1;$p<=$numpages;$p++) {
                    $url = $pageprefix . self::_getPrefix($categoryid) . '/';
                    if ($p > 1) {
                        $url .= 'p' . $p . '/';
                    }
                    if ($p == $pagenum) {
                        $pagination .= '<li>&gt; Page '.$p.'</li>'. "\n";
                    } else {
                        $pagination .= '<li>&gt; <a href="'.$url.'">Page '.$p.'</a></li>'. "\n";
                    }
                }
                $pagination .= '</ul>';
            }
            $smarty->assign('pagination', $pagination);
            $smarty->assign('pagenum', $pagenum);

            /* clear the meta description to avoid duplicate content issues */
            $content['metadescription'] = '';

            /* get profile content and assign to Smarty */
            $profiles = array_slice($profiles, $start, $profilesperpage);
            $smarty->assign('jojo_profiles', $profiles);

            $content['content'] = $smarty->fetch('jojo_profile.tpl');
        }
        return $content;
    }

    static function getPluginPages($for='', $section=0)
    {
        global $sectiondata;
        $items =  Jojo::selectAssoc("SELECT p.pageid AS id, c.*, p.*  FROM {profilecategory} c LEFT JOIN {page} p ON (c.pageid=p.pageid) ORDER BY pg_parent, pg_order");
        // use core function to clean out any pages based on permission, status, expiry etc
        $items =  Jojo_Plugin_Core::cleanItems($items, $for);
        foreach ($items as $k=>$i){
            if ($section && $section != $i['root']) {
                unset($items[$k]);
                continue;
            }
        }
        return $items;
    }

    public static function getNavItems($pageid, $selected=false)
    {
        $nav = array();
        $section = Jojo::getSectionRoot($pageid);
        $profilepages = self::getPluginPages('', $section);
        if (!$profilepages) return $nav;
        $categoryid = $profilepages[$pageid]['profilecategoryid'];
        $sortby = $profilepages[$pageid]['sortby'];
        $items = isset($profilepages[$pageid]['addtonav']) && $profilepages[$pageid]['addtonav'] ? self::getProfiles('', '', $categoryid, $sortby) : '';
        if (!$items) return $nav;
        //if the page is currently selected, check to see if an item has been called
        if ($selected) {
            $id = Jojo::getFormData('id', 0);
            $url = Jojo::getFormData('url', '');
        }
        foreach ($items as $i) {
            $nav[$i['id']]['url'] = $i['url'];
            $nav[$i['id']]['title'] = $i['title'];
            $nav[$i['id']]['label'] = $i['fullname'];
            $nav[$i['id']]['selected'] = (boolean)($selected && (($id && $id== $i['id']) ||(!empty($url) && $i['url'] == $url)));
        }
        return $nav;
    }

    // Sync the articategory data over to the page table
    static function admin_action_after_save_profilecategory($id) {
        if (!Jojo::getFormData('fm_pageid', 0)) {
            // no pageid set for this category (either it's a new category or maybe the original page was deleted)
            self::sync_category_to_page($id);
       }
    }

    // Sync the category data over from the page table
    static function admin_action_after_save_page($id) {
        if (strtolower(Jojo::getFormData('fm_pg_link',    ''))=='jojo_plugin_jojo_profile') {
           self::sync_page_to_category($id);
       }
    }

    static function sync_category_to_page($catid) {
        // add a new hidden page for this category and make up a title
            $newpageid = Jojo::insertQuery(
            "INSERT INTO {page} SET pg_title = ?, pg_link = ?, pg_url = ?, pg_parent = ?, pg_status = ?",
            array(
                'Orphaned profiles',  // Title
                'jojo_plugin_jojo_profile',  // Link
                'orphaned-profiles',  // URL
                0,  // Parent - don't do anything smart, just put it at the top level for now
                'hidden' // hide new page so it doesn't show up on the live site until it's been given a proper title and url
            )
        );
        // If we successfully added the page, update the category with the new pageid
        if ($newpageid) {
            jojo::updateQuery(
                "UPDATE {profilecategory} SET pageid = ? WHERE profilecategoryid = ?",
                array(
                    $newpageid,
                    $catid
                )
            );
       }
    return true;
    }

    static function sync_page_to_category($pageid) {
        // Get the list of categories by page id
        $categories = jojo::selectAssoc("SELECT pageid AS id, pageid FROM {profilecategory}");
        // no category for this page id
        if (!count($categories) || !isset($categories[$pageid])) {
            jojo::insertQuery("INSERT INTO {profilecategory} (pageid) VALUES ('$pageid')");
        }
        return true;
    }

    public static function sitemap($sitemap)
    {
        global $page;
        /* See if we have any profile sections to display and find all of them */
        $indexes =  self::getPluginPages('sitemap');
        if (!count($indexes)) {
            return $sitemap;
        }

        if (Jojo::getOption('profile_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the profiles section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = self::_sitemapRemoveSelf($section['tree']);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

        $now = strtotime('now');
        $limit = 15;
        $profilesperpage = Jojo::getOption('profilesperpage', 40);
         /* Make sitemap trees for each profiles instance found */
        foreach($indexes as $k => $i){
            $categoryid = $i['profilecategoryid'];
            $sortby = $i['sortby'];

            /* Create tree and add index and feed links at the top */
            $profiletree = new hktree();
            $indexurl = $i['url'];
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $profiletree->addNode('index', 0, $i['title'], $indexurl);
               $parent = 'index';
            }

            $profiles = self::getprofiles('', '', $categoryid, $sortby);
            $n = count($profiles);

            /* Trim items down to first page and add to tree*/
            $profiles = array_slice($profiles, 0, $profilesperpage);
            foreach ($profiles as $a) {
                $profiletree->addNode($a['id'], $parent, $a['fullname'], $a['url']);
            }

            /* Get number of pages for pagination */
            $numpages = ceil($n / $profilesperpage);
            /* calculate pagination */
            if ($numpages > 1) {
                for ($p=2; $p <= $numpages; $p++) {
                    $url = $indexurl .'p' . $p .'/';
                    $nodetitle = $i['title'] . ' (p.' . $p . ')';
                    $profiletree->addNode('p' . $p, $parent, $nodetitle, $url);
                }
            }
            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = $i['url'];
                $sitemap['pages']['tree'] = self::_sitemapAddInplace($sitemap['pages']['tree'], $profiletree->asArray(), $url);
            } else {
                $mldata = Jojo::getMultiLanguageData();
                /* Add to the end */
                $sitemap["profiles$k"] = array(
                    'title' => $i['title'] . (count($mldata['sectiondata'])>1 ? ' (' . ucfirst($mldata['sectiondata'][$i['root']]['name']) . ')' : ''),
                    'tree' => $profiletree->asArray(),
                    'order' => 3 + $k,
                    'header' => '',
                    'footer' => '',
                    );
            }
        }
        return $sitemap;
    }

    static function _sitemapAddInplace($sitemap, $toadd, $url)
    {
        foreach ($sitemap as $k => $t) {
            if ($t['url'] == $url) {
                $sitemap[$k]['children'] = isset($sitemap[$k]['children']) ? array_merge($toadd, $sitemap[$k]['children']): $toadd;
            } elseif (isset($sitemap[$k]['children'])) {
                $sitemap[$k]['children'] = self::_sitemapAddInplace($t['children'], $toadd, $url);
            }
        }
        return $sitemap;
    }

    static function _sitemapRemoveSelf($tree)
    {
        static $urls;

        if (!is_array($urls)) {
            $urls = array();
            $indexes =  self::getPluginPages('sitemap');
            if (count($indexes)==0) {
               return $tree;
            }
            foreach($indexes as $key => $i){
                $urls[] = $i['url'];
            }
        }

        foreach ($tree as $k =>$t) {
            if (in_array($t['url'], $urls)) {
                unset($tree[$k]);
            } else {
                $tree[$k]['children'] = self::_sitemapRemoveSelf($t['children']);
            }
        }
        return $tree;
    }

    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds article pages
     */
    static function xmlsitemap($sitemap)
    {
        /* Get articles from database */
        $items = self::getProfiles('', '', 'all', '', '', 'alllanguages');
        $now = time();
        $indexes =  self::getPluginPages('xmlsitemap');
        $ids=array();
        foreach ($indexes as $i) {
            $ids[$i['profilecategoryid']] = true;
        }
        /* Add to sitemap */
        foreach($items as $k => $a) {
            // strip out items from expired pages
            if (!isset($ids[$a['pr_category']])) {
                unset($articles[$k]);
                continue;
            }
            $url = _SITEURL . '/'. $a['url'];
            $lastmod = $a['date'];
            $priority = 0.6;
            $changefreq = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }
        /* Return sitemap */
        return $sitemap;
    }

   /**
     * Remove Snip
     *
     * Removes any [[snip]] tags leftover in the content before outputting
     */
    public static function removesnip($data)
    {
        $data = str_ireplace('[[snip]]','',$data);
        return $data;
    }

    static function _getPrefix($categoryid=false) {
        $cacheKey = 'profile';
        $cacheKey .= ($categoryid) ? $categoryid : '';

        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        /* Cache some stuff */
        $res = Jojo::selectRow("SELECT p.pageid, pg_title, pg_url FROM {page} p LEFT JOIN {profilecategory} c ON (c.pageid=p.pageid) WHERE `profilecategoryid` = '$categoryid'");
        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . $res['pg_title'];
        } else {
            $_cache[$cacheKey] = '';
        }
        return $_cache[$cacheKey];
    }

    static function getPrefixById($id=false) {
        if ($id) {
            $data = Jojo::selectRow("SELECT profilecategoryid, pageid FROM {profile} LEFT JOIN {profilecategory} ON (pr_category=profilecategoryid) WHERE profileid = ?", array($id));
            if ($data) {
                $fullprefix = Jojo::getPageUrlPrefix($data['pageid']) . self::_getPrefix($data['profilecategoryid']);
                return $fullprefix;
            }
        }
        return false;
    }


    function getCorrectUrl()
    {
        global $page;
        $pageid  = $page->page['pageid'];
        $id = Jojo::getFormData('id',     0);
        $url       = Jojo::getFormData('url',    '');
        $action    = Jojo::getFormData('action', '');
        $pagenum   = Jojo::getFormData('pagenum', 1);

        $data = Jojo::selectRow("SELECT profilecategoryid FROM {profilecategory} WHERE pageid=?", $pageid);
        $categoryid = !empty($data['profilecategoryid']) ? $data['profilecategoryid'] : '';

        if ($pagenum[0] == 'p') {
            $pagenum = substr($pagenum, 1);
        }

        /* unsubscribing */
        if ($action == 'unsubscribe') return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $correcturl = self::getUrl($id, $url, null, $pageid, $categoryid);

        if ($correcturl) {
            return _SITEURL . '/' . $correcturl;
        }

        /* index with pagination */
        if ($pagenum > 1) return parent::getCorrectUrl() . 'p' . $pagenum . '/';

        /* index - default */
        return parent::getCorrectUrl();
    }

    static public function isUrl($uri)
    {
        $prefix = false;
        $getvars = array();
        /* Check the suffix matches and extract the prefix */
        if (preg_match('#^(.+)/unsubscribe/([0-9]+)/([a-zA-Z0-9]{16})$#', $uri, $matches)) {
            /* "$prefix/[action:unsubscribe]/[profileid:integer]/[code:[a-zA-Z0-9]{16}]" eg "profiles/unsubscribe/34/7MztlFyWDEKiSoB1/" */
            $prefix = $matches[1];
            $getvars = array(
                        'action' => 'unsubscribe',
                        'profileid' => $matches[2],
                        'code' => $matches[3]
                        );
        /* Check for standard plugin url format matches */
        } elseif ($uribits = Jojo_Plugin::isPluginUrl($uri)) {
            $prefix = $uribits['prefix'];
            $getvars = $uribits['getvars'];
        } else {
            return false;
        }
        /* Check the prefix matches */
        if ($res = self::checkPrefix($prefix)) {
            /* If full uri matches a prefix it's an index page so ignore it and let the page plugin handle it */
            if (self::checkPrefix(trim($uri, '/'))) return false;
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

    /**
     * Check if a prefix is an profile prefix
     */
    static public function checkPrefix($prefix)
    {
        static $_prefixes, $categories;
        if (!isset($categories)) {
            /* Initialise cache */
            $categories = array(false);
            $categories = array_merge($categories, Jojo::selectAssoc("SELECT profilecategoryid, profilecategoryid as profilecategoryid2 FROM {profilecategory}"));
            $_prefixes = array();
        }
        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }
        /* Check everything */
        foreach($categories as $category) {
            $testPrefix = self::_getPrefix($category);
            $_prefixes[$testPrefix] = true;
            if ($testPrefix == $prefix) {
                /* The prefix is good */
                return true;
            }
        }
        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }


    /**
     * Site Search
     */
    static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        $searchfields = array(
            'plugin' => 'jojo_profile',
            'table' => 'profile',
            'idfield' => 'profileid',
            'languagefield' => 'pr_htmllang',
            'primaryfields' => 'pr_title, pr_firstname, pr_name',
            'secondaryfields' => 'pr_title, pr_firstname, pr_name, pr_quote, pr_description, pr_department',
        );
        $rawresults =  Jojo_Plugin_Jojo_search::searchPlugin($searchfields, $keywords, $language, $booleankeyword_str);
        $data = $rawresults ? self::getItemsById(array_keys($rawresults)) : '';
        if ($data) {
            foreach ($data as $result) {
                $result['relevance'] = $rawresults[$result['id']]['relevance'];
                $result['title'] = $result['fullname'] . ($result['title'] ? ' - ' . $result['title'] : '');
                $result['type'] = $result['pagetitle'];
                $result['tags'] = isset($rawresults[$result['id']]['tags']) ? $rawresults[$result['id']]['tags'] : '';
                $results[] = $result;
            }
        }
        /* Return results */
        return $results;
    }

/*
* Tags
*/
    static function getTagSnippets($ids)
    {
        $snippets = self::getItemsById($ids);
        foreach ($snippets as &$s) {
            $s['title'] = $s['fullname'] . ($s['title'] ? ' - ' . $s['title'] : '');
        }
        return $snippets;
    }

}
