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

class JOJO_Plugin_Jojo_profile extends JOJO_Plugin
{

    static function saveTags($record, $tags = array())
    {
        /* Delete existing tags for this item */
        JOJO_Plugin_Jojo_Tags::deleteTags('jojo_profile', $record['profileid']);

        /* Save all the new tages */
        foreach($tags as $tag) {
            JOJO_Plugin_Jojo_Tags::saveTag($tag, 'jojo_profile', $record['profileid']);
        }
    }

    static function getTagSnippets($ids)
    {
        /* Convert array of ids to a string */
        $ids = "'" . implode($ids, "', '") . "'";

        /* Get the profiles */
        $profiles = Jojo::selectQuery("SELECT *
                                       FROM {profile}
                                       WHERE
                                            profileid IN ($ids)
                                         AND
                                           pr_livedate < ?
                                         AND
                                           pr_expirydate<=0 OR pr_expirydate > ?
                                       ORDER BY
                                         pr_date DESC",
                                      array(time(), time()));

        /* Create the snippets */
        $snippets = array();
        foreach ($profiles as $i => $a) {
            $image = !empty($profiles[$i]['pr_image']) ? 'profiles/' . $profiles[$i]['pr_image'] : '';
            $snippets[] = array(
                    'id'    => $profiles[$i]['profileid'],
                    'image' => $image,
                    'title' => Jojo::html2text($profiles[$i]['pr_name']),
                    'text'  => Jojo::html2text($profiles[$i]['pr_description']),
                    'url'   => Jojo::urlPrefix(false) . JOJO_Plugin_Jojo_profile::getProfileUrl($profiles[$i]['profileid'], $profiles[$i]['pr_url'], $profiles[$i]['pr_name'], $profiles[$i]['pr_language'], $profiles[$i]['pr_category'])
                );
        }

        /* Return the snippets */
        return $snippets;
    }


    static function getProfiles($num = false, $start = 0, $categoryid=false, $sortby=false, $exclude=false) {
        global $page;
        if (_MULTILANGUAGE) $language = !empty($page->page['pg_language']) ? $page->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
        $_PROFILECATEGORIES = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;

        /* if calling page is an article, Get current articleid, and exclude from the list  */
        $excludethisid = ($exclude && $page->page['pg_link']=='jojo_plugin_jojo_profile' && Jojo::getFormData('id')) ? Jojo::getFormData('id') : '';
        $excludethisurl = ($exclude && $page->page['pg_link']=='jojo_plugin_jojo_profile' && Jojo::getFormData('url')) ? Jojo::getFormData('url') : '';

        $now = time();
        $query = 'SELECT pr.*';
        $query .= $_PROFILECATEGORIES ? ", pc.pc_url, p.pg_title" : '';
        $query .= " FROM {profile} pr";
        $query .= $_PROFILECATEGORIES ? " LEFT JOIN {profilecategory} pc ON (pr.pr_category=pc.profilecategoryid) LEFT JOIN {page} p ON (pc.pc_url=p.pg_url)" : '';
        $query .= " WHERE pr_livedate<$now AND (pr_expirydate<=0 OR pr_expirydate>$now) ";
        $query .= (_MULTILANGUAGE) ? " AND (pr_language = '$language')" : '';
        $query .= ($_PROFILECATEGORIES && _MULTILANGUAGE) ? " AND (pg_language = '$language')" : '';
        $query .= ($_PROFILECATEGORIES) ? " AND (pr_category = '$categoryid')" : '';
        $query .= ($excludethisid) ? " AND (profileid != '$excludethisid')" : '';
        $query .= ($excludethisurl) ? " AND (pr_url != '$excludethisurl')" : '';
        $query .= " ORDER BY " . ($sortby ? $sortby : "pr_displayorder, pr_name");
        $query .= ($num) ? " LIMIT $start, $num" : '';
        $profiles = Jojo::selectQuery($query);
        foreach ($profiles as &$p){
            $p['title']        = htmlspecialchars($p['pr_title'], ENT_COMPAT, 'UTF-8', false);
            $p['name']        = htmlspecialchars($p['pr_name'], ENT_COMPAT, 'UTF-8', false);
            $p['description'] = strip_tags($p['pr_description']);
            $p['datefriendly'] = Jojo::mysql2date($p['pr_date'], "medium");
            $p['url'] = JOJO_Plugin_Jojo_profile::getProfileUrl($p['profileid'], $p['pr_url'], $p['pr_name'], $p['pr_language'], ($_PROFILECATEGORIES ? $p['pr_category'] : ''));
            if ( $p['pr_image']) $p['imageurl'] = "images/v10000/profiles/" . $p['pr_image'];
            if ( $p['pr_quote']) $p['profile'] = strip_tags($p['pr_quote']);
            $p['category']     = ($_PROFILECATEGORIES && !empty($p['pg_title'])) ? $p['pg_title'] : '';
            $p['categoryurl']  = ($_PROFILECATEGORIES && !empty($p['pc_url'])) ? (_MULTILANGUAGE ? $language . '/' : '') . $p['pc_url'] . '/' : '';
        }
        return $profiles;
    }
    /*
     * calculates the URL for the profile - requires the profile ID, but works without a query if given the URL or title from a previous query
     *
     *
     */
    static function getProfileUrl($profileid=false, $url=false, $title=false, $language=false, $categoryid=false )
    {
        if (_MULTILANGUAGE) {
            $language = !empty($language) ? $language : Jojo::getOption('multilanguage-default', 'en');
            $mldata = Jojo::getMultiLanguageData();
            $lclanguage = $mldata['longcodes'][$language];
        }
        $languagePrefix = ( _MULTILANGUAGE ) ? Jojo::getMultiLanguageString ( $language ) : '';
        /* URL specified */
        if (!empty($url)) {
            $fullurl = $languagePrefix;
            $fullurl .= Jojo_Plugin_Jojo_profile::_getPrefix('', ((_MULTILANGUAGE) ? $language : ''), (!empty($categoryid) ? $categoryid : '') ) . '/' . $url . '/';
            return $fullurl;
         }
        /* ID + title specified */
        if ($profileid && !empty($title)) {
            $fullurl = $languagePrefix;
            $fullurl .= (_MULTILANGUAGE && $language != 'en') ? Jojo_Plugin_Jojo_profile::_getPrefix('', ((_MULTILANGUAGE) ? $language : ''), (!empty($categoryid) ? $categoryid : '') ) . '/' . $profileid . '/' . Jojo::cleanURL($title) : Jojo::rewrite(Jojo_Plugin_Jojo_profile::_getPrefix('', ((_MULTILANGUAGE) ? $language : ''), (!empty($categoryid) ? $categoryid : '') ), $profileid, $title, '');
            return $fullurl;
        }
        /* use the profile ID to find either the URL or title */
        if ($profileid) {
            $profile = Jojo::selectRow("SELECT pr_url, pr_name, pr_language, pr_category FROM {profile} WHERE profileid = ?", $profileid);
            if ($profile) {
                if (_MULTILANGUAGE) {
                    $language = !empty($profile['pr_language']) ? $profile['pr_language'] : Jojo::getOption('multilanguage-default', 'en');
                    $lclanguage = $mldata['longcodes'][$language];
                }
                $languagePrefix = ( _MULTILANGUAGE ) ? Jojo::getMultiLanguageString ( $language ) : '';
                $prefix = Jojo_Plugin_Jojo_profile::_getPrefix('', (_MULTILANGUAGE ? $language : ''), $profile['pr_category'] );
                if (!empty($profile['pr_url'])) {
                    $fullurl = $languagePrefix;
                    $fullurl .= $prefix . '/' . $profile['pr_url'] . '/';
                    return $fullurl;
                } else {
                    $fullurl = $languagePrefix;
                    $fullurl .= (_MULTILANGUAGE && $language != 'en') ? $prefix . '/' . $profileid . '/' . Jojo::cleanURL($profile['pr_name'])  : Jojo::rewrite($prefix, $profileid, $profile['pr_name'], '');
                    return $fullurl;
                }
            }
         }
        /* No profile matching the ID supplied or no ID supplied */
        return false;
    }


    function _getContent()
    {
        global $smarty, $_USERGROUPS;
        $content = array();

        $pg_url = $this->page['pg_url'];
        $language = !empty($this->page['pg_language']) ? $this->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
        $mldata = Jojo::getMultiLanguageData();
        $lclanguage = $mldata['longcodes'][$language];
        $languagePrefix = ( _MULTILANGUAGE ) ? Jojo::getMultiLanguageString ( $language ) : '';
        if (_MULTILANGUAGE) {
            $smarty->assign('multilangstring', $languagePrefix);
        }
        /* Get category url and id if needed */
        $_PROFILECATEGORIES = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_PROFILECATEGORIES) ? Jojo::selectRow("SELECT profilecategoryid, sortby FROM {profilecategory} WHERE pc_url = '$pg_url'") : '';
        $categoryid = ($_PROFILECATEGORIES && count($categorydata)) ? $categorydata['profilecategoryid'] : '';
        $sortby = ($_PROFILECATEGORIES && count($categorydata)) ? $categorydata['sortby'] : '';

        $profiles = JOJO_Plugin_Jojo_profile::getProfiles('', '', $categoryid, $sortby);
        /* Are we looking at a profile or the index? */
        $profileid = Util::getFormData('id', 0);
        $url       = Jojo::clean(Util::getFormData('url', ''));

        if ($profileid || !empty($url)) {
            /* find the current, next and previous profiles */
            $profile = array();
            $prevprofile = array();
            $nextprofile = array();
            $next = false;
            foreach ($profiles as $p) {
                if (!_MULTILANGUAGE && !empty($url) && $url==$p['pr_url']) {
                    $profile = $p;
                    $next = true;
               } elseif (_MULTILANGUAGE && !empty($url) && $url==$p['pr_url'] && $language==$p['pr_language']) {
                    $profile = $p;
                    $next = true;
               } elseif ($profileid==$p['profileid']) {
                    $profile = $p;
                    $next = true;
                } elseif ($next==true) {
                    $nextprofile = $p;
                     break;
                } else {
                    $prevprofile = $p;
                }
            }

            /* If the profile can't be found, return a 404 */
            if (!$profile) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
            }

            /* Get the specific profile */
            $profileid = $profile['profileid'];
            $profile['pr_datefriendly'] = Jojo::mysql2date($profile['pr_date'], "long");

            if (Jojo::getOption('profile_next_prev') == 'yes') {
                if (!empty($nextprofile)) {
                    $nextprofile['url'] = JOJO_Plugin_Jojo_profile::getProfileUrl($nextprofile['profileid'], $nextprofile['pr_url'], $nextprofile['pr_name'], $nextprofile['pr_language'], $nextprofile['pr_category']);
                    $smarty->assign('nextprofile', $nextprofile);
                }

                if (!empty($prevprofile)) {
                    $prevprofile['url'] = JOJO_Plugin_Jojo_profile::getProfileUrl($prevprofile['profileid'], $prevprofile['pr_url'], $prevprofile['pr_name'], $prevprofile['pr_language'], $prevprofile['pr_category']);
                    $smarty->assign('prevprofile', $prevprofile);
                }
            }

            /* Ensure the tags class is available */
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
            }

            /* Calculate whether the profile has expired or not */
            $now = Jojo::strToTimeUK('now');
            if (($now < $profile['pr_livedate']) || (($now > $profile['pr_expirydate']) && ($profile['pr_expirydate'] > 0)) ) {
                $this->expired = true;
            }


            /* Add profile breadcrumb */
            $breadcrumbs = $this->_getBreadCrumbs();
            $breadcrumb = array();
            $breadcrumb['name'] = $profile['pr_name'];
            $breadcrumb['rollover'] = $profile['pr_name'];
            $breadcrumb['url'] = JOJO_Plugin_Jojo_profile::getProfileUrl($profile['profileid'], $profile['pr_url'], $profile['pr_name'], $profile['pr_language'], $profile['pr_category']);
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;


            /* Assign profile content to Smarty */
            $smarty->assign('jojo_profile', $profile);



            /* Prepare fields for display */
            if ($profile['pr_htmllang']) {
                // Override the language setting on this page if necessary.
                $content['pg_htmllang'] = $profile['pr_htmllang'];
                $smarty->assign['pg_htmllang'] = $profile['pr_htmllang'];
            }
            $content['title']            = $profile['pr_name'];
            $content['seotitle']         = Jojo::either($profile['pr_seotitle'], $profile['pr_name'] . ' - ' . $profile['pr_title']);
            $content['breadcrumbs']      = $breadcrumbs;
            $content['meta_description'] = Jojo::either($profile['pr_metadesc'], $profile['pr_name'] . ' - ' . $profile['pr_title'] . ', a profile on '._SITETITLE.' - Read all about '.$profile['pr_name'].' and other people on '._SITETITLE.'. '.Jojo::getOption('linkprofile'));
            $content['metadescription']  = $content['meta_description'];

         } else {
            /* profile index section */

            $pagenum = Util::getFormData('pagenum', 1);

            $smarty->assign('profile','');
            $profilesperpage = Jojo::getOption('profilesperpage', 40);
            $start = ($profilesperpage * ($pagenum-1));

            /* get number of profiles for pagination */
            $numprofiles = count($profiles);
            $numpages = ceil($numprofiles / $profilesperpage);

            /* calculate pagination */
            if ($numpages == 1) {
                $pagination = '';
            } elseif ($numpages == 2 && $pagenum == 2) {
                $pagination = sprintf('<a href="%s/p1/">Previous page...</a>', ((_MULTILANGUAGE) ? $languagePrefix . JOJO_Plugin_Jojo_profile::_getPrefix('', $language, $categoryid) : JOJO_Plugin_Jojo_profile::_getPrefix('', '', $categoryid)) );
            } elseif ($numpages == 2 && $pagenum == 1) {
                $pagination = sprintf('<a href="%s/p2/">Next page...</a>', ((_MULTILANGUAGE) ? $languagePrefix . JOJO_Plugin_Jojo_profile::_getPrefix('', $language, $categoryid) : JOJO_Plugin_Jojo_profile::_getPrefix('', '', $categoryid)) );
            } else {
                $pagination = '<ul>';
                for ($p=1;$p<=$numpages;$p++) {
                    $url = (_MULTILANGUAGE) ? $languagePrefix . JOJO_Plugin_Jojo_profile::_getPrefix('', $language, $categoryid) . '/' : JOJO_Plugin_Jojo_profile::_getPrefix('', '', $categoryid) . '/';
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
            $smarty->assign('pagination',$pagination);
            $smarty->assign('pagenum',$pagenum);

            /* clear the meta description to avoid duplicate content issues */
             $content['metadescription'] = '';
             $content['meta_description'] = '';

            /* get profile content and assign to Smarty */
            $profiles = array_slice($profiles, $start,$profilesperpage);
            $smarty->assign('jojo_profiles', $profiles);

        }
        /* get related profiles if tags plugin installed and option enabled */
        $numrelated = Jojo::getOption('profile_num_related');
        if ($numrelated) {
            $related = JOJO_Plugin_Jojo_Tags::getRelated('jojo_profile', $profileid, $numrelated, 'jojo_profile'); //set the last argument to 'jojo_profile' to restrict results to only profiles
            $smarty->assign('related', $related);
        }

        $smarty->assign ( 'indexurl', (_MULTILANGUAGE) ? $languagePrefix . JOJO_Plugin_Jojo_profile::_getPrefix('', $language, $categoryid) : JOJO_Plugin_Jojo_profile::_getPrefix('', '', $categoryid). '/');

        $content['content'] = $smarty->fetch('jojo_profile.tpl');
        return $content;
    }

    static function admin_action_after_save()
    {
        Jojo::updateQuery("UPDATE {option} SET `op_value`='".time()."' WHERE `op_name`='profile_last_updated'");
        return true;
    }
    /**
     * Sitemap filter
     *
     * Receives existing sitemap and adds profiles section
     */
    public static function sitemap($sitemap)
    {
        /* See if we have any profile sections to display */
        $_PROFILECATEGORIES = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;
        $query = "SELECT *";
        $query .= $_PROFILECATEGORIES ? ", profilecategoryid" : '';
        $query .= " FROM {page} p";
        $query .= $_PROFILECATEGORIES ? " LEFT JOIN {profilecategory} pc ON (p.pg_url=pc.pc_url)" : '';
        $query .= " WHERE pg_link = 'Jojo_Plugin_Jojo_profile' AND pg_sitemapnav = 'yes'";

        $profileindexes = Jojo::selectQuery($query);
        if (!count($profileindexes)) {
            return $sitemap;
        }

        if (Jojo::getOption('profile_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the profiles section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = Jojo_Plugin_Jojo_profile::_sitemapRemoveSelf($section['tree'], $profileindexes);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

        $now = strtotime('now');
        $limit = 15;
        $profilesperpage = Jojo::getOption('profilesperpage', 40);
        foreach($profileindexes as $k => $i){
            if (_MULTILANGUAGE) {
                $language = !empty($i['pg_language']) ? $i['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $mldata = Jojo::getMultiLanguageData();
                $lclanguage = $mldata['longcodes'][$language];
            }
            $languagePrefix = ( _MULTILANGUAGE ) ? Jojo::getMultiLanguageString ( $language ) : '';
            /* Get category url and id if needed */
            $categoryid = $_PROFILECATEGORIES ? $i['profilecategoryid'] : '';

            /* Create tree and add index and feed links at the top */
            $profiletree = new hktree();
            $indexurl = (_MULTILANGUAGE) ? $languagePrefix . Jojo_Plugin_Jojo_profile::_getPrefix('', $language, $categoryid) . '/' : Jojo_Plugin_Jojo_profile::_getPrefix('', '', $categoryid) . '/' ;
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $profiletree->addNode('index', 0, $i['pg_title'] . ' Index', $indexurl);
               $parent = 'index';
            }

            /* Get the profile content from the database */
            $query =  "SELECT * FROM {profile} WHERE pr_livedate<$now AND (pr_expirydate<=0 OR pr_expirydate>$now)";
            $query .= (_MULTILANGUAGE) ? " AND (pr_language = '$language')" : '';
            $query .= ($_PROFILECATEGORIES) ? " AND (pr_category = '$categoryid')" : '';
            $query .= " ORDER BY pr_date DESC LIMIT $limit";

            $profiles = Jojo::selectQuery($query);
            $n = count($profiles);
            foreach ($profiles as $a) {
                $profiletree->addNode($a['profileid'], $parent, $a['pr_name'] . ' - ' . $a['pr_title'], Jojo_Plugin_Jojo_profile::getProfileUrl($a['profileid'], $a['pr_url'], $a['pr_name'], $a['pr_language'], $a['pr_category']));
            }

            /* Get number of profiles for pagination */
            $countquery =  "SELECT COUNT(*) AS numprofiles FROM {profile} WHERE pr_livedate<$now AND (pr_expirydate<=0 OR pr_expirydate>$now)";
            $countquery .= (_MULTILANGUAGE) ? " AND (pr_language = '$language')" : '';
            $countquery .= ($_PROFILECATEGORIES) ? " AND (pr_category = '$categoryid')" : '';
            $profilescount = Jojo::selectQuery($countquery);
            $numprofiles = $profilescount[0]['numprofiles'];
            $numpages = ceil($numprofiles / $profilesperpage);

            /* calculate pagination */
            if ($numpages == 1) {
                if ($limit < $numprofiles) {
                    $profiletree->addNode('p1', $parent, 'More ' . $i['pg_title'] , $indexurl );
                }
            } else {
                for ($p=1; $p <= $numpages; $p++) {
                    if (($limit < $numprofiles) && ($p == 1)) {
                        $profiletree->addNode('p1', $parent, '...More' , $indexurl );
                    } elseif ($p != 1) {
                        $url = $indexurl .'p' . $p .'/';
                        $nodetitle = $i['pg_title'] . ' Page '. $p;
                        $profiletree->addNode('p' . $p, $parent, $nodetitle, $url);
                    }
                }
            }

            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = (_MULTILANGUAGE ? $languagePrefix : '') . Jojo_Plugin_Jojo_profile::_getPrefix('profile', (_MULTILANGUAGE ? $language : ''), $categoryid) . '/';
                $sitemap['pages']['tree'] = Jojo_Plugin_Jojo_profile::_sitemapAddInplace($sitemap['pages']['tree'], $profiletree->asArray(), $url);
            } else {
                /* Add to the end */
                $sitemap["profiles$k"] = array(
                    'title' => (_MULTILANGUAGE) ? $i['pg_title'] . ' (' . ucfirst($lclanguage) . ')' : $i['pg_title'],
                    'tree' => $profiletree->asArray(),
                    'order' => 3 + $k,
                    'header' => '',
                    'footer' => '',
                    );
            }
        }
        return $sitemap;
    }

    private static function _sitemapAddInplace($sitemap, $toadd, $url)
    {
        foreach ($sitemap as $k => $t) {
            if ($t['url'] == $url) {
                $sitemap[$k]['children'] = $toadd;
            } elseif (isset($sitemap[$k]['children'])) {
                $sitemap[$k]['children'] = Jojo_Plugin_Jojo_profile::_sitemapAddInplace($t['children'], $toadd, $url);
            }
        }
        return $sitemap;
    }

    private static function _sitemapRemoveSelf($tree, $profileindexes)
    {
        static $urls;

        if (!is_array($urls)) {
            $urls = array();
            if (count($profileindexes)==0) {
               return $tree;
            }

            foreach($profileindexes as $i){
                $language = !empty($i['pg_language']) ? $i['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $mldata = Jojo::getMultiLanguageData();
                $lclanguage = $mldata['longcodes'][$language];
                $categoryid =  $_PROFILECATEGORIES ? $i['profilecategoryid'] : '';
                $languagePrefix = ( _MULTILANGUAGE ) ? Jojo::getMultiLanguageString ( $language ) : '';
                $urls[] = $languagePrefix . Jojo_Plugin_Jojo_profile::_getPrefix('', (_MULTILANGUAGE ? $language : ''), $categoryid) . '/';
            }
        }

        foreach ($tree as $k =>$t) {
            if (in_array($t['url'], $urls)) {
                unset($tree[$k]);
            } else {
                $tree[$k]['children'] = Jojo_Plugin_Jojo_profile::_sitemapRemoveSelf($t['children']);
            }
        }
        return $tree;
    }




    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds profile pages
     */
    public static function xmlsitemap($sitemap)
    {
        /* Get profiles from database */
        $profiles = Jojo::selectQuery("SELECT * FROM {profile} WHERE pr_livedate<".time()." AND (pr_expirydate<=0 OR pr_expirydate>".time().")");

        /* Add profiles to sitemap */
        foreach($profiles as $a) {
            $url = _SITEURL . '/'. JOJO_Plugin_Jojo_profile::getprofileUrl($a['profileid'], $a['pr_url'], $a['pr_name'], $a['pr_language'], $a['pr_category']);
            $lastmod = Jojo::strToTimeUK($a['pr_date']);
            $priority = 0.6;
            $changefreq = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }

        /* Return sitemap */
        return $sitemap;
    }

     /**
     * Site Search
     *
     */
    public static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        global $_USERGROUPS;
        $_PROFILECATEGORIES = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;
        $pagePermissions = new JOJO_Permissions();
        $boolean = ($booleankeyword_str) ? true : false;
        $keywords_str = ($boolean) ? $booleankeyword_str :  implode(' ', $keywords);
        if ($boolean && stripos($booleankeyword_str, '+') === 0  ) {
            $like = '1';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" AND (pr_quote LIKE '%%%s%%' OR pr_title LIKE '%%%s%%' OR pr_description LIKE '%%%s%%' OR pr_name LIKE '%%%s%%')", JOJO::clean($keyword), JOJO::clean($keyword), JOJO::clean($keyword), JOJO::clean($keyword));
            }
        } elseif ($boolean && stripos($booleankeyword_str, '"') === 0) {
            $like = "(pr_quote LIKE '%%%". implode(' ', $keywords). "%%' OR pr_title LIKE '%%%". implode(' ', $keywords) . "%%' OR pr_description LIKE '%%%". implode(' ', $keywords) . "%%')";
        } else {
            $like = '(0';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" OR pr_quote LIKE '%%%s%%' OR pr_title LIKE '%%%s%%' OR pr_description LIKE '%%%s%%' OR pr_name LIKE '%%%s%%'", JOJO::clean($keyword), JOJO::clean($keyword), JOJO::clean($keyword), JOJO::clean($keyword));
            }
            $like .= ')';
        }

        $query = "SELECT profileid, pr_url, pr_title, pr_name, pr_description, pr_quote, pr_language, pr_expirydate, pr_livedate, pr_image, pr_category, ((MATCH(pr_title) AGAINST (?) * 0.2) + MATCH(pr_title, pr_description, pr_quote, pr_name) AGAINST (?)) AS relevance ";
        $query .= ", p.pg_url, p.pg_title";
        $query .= " FROM {profile} AS profile ";
        $query .= $_PROFILECATEGORIES ? " LEFT JOIN {profilecategory} pc ON (profile.pr_category=pc.profilecategoryid) LEFT JOIN {page} p ON (pc.pc_url=p.pg_url AND p.pg_language=pr_language)" : "LEFT JOIN {page} p ON (p.pg_link='jojo_plugin_jojo_profile' AND p.pg_language=pr_language)";
        $query .= "LEFT JOIN {language} AS language ON (profile.pr_language = languageid) ";
        $query .= "WHERE $like";
        $query .= ($language) ? "AND pr_language = '$language' " : '';
        $query .= "AND language.active = 'yes' ";
        $query .= "AND pr_livedate<" . time() . " AND (pr_expirydate<=0 OR pr_expirydate>" . time() . ") ";
        $query .= " ORDER BY relevance DESC LIMIT 100";

        $data = Jojo::selectQuery($query, array($keywords_str, $keywords_str));

        foreach ($data as $d) {
            $pagePermissions->getPermissions('profile', $d['profileid']);
            if (!$pagePermissions->hasPerm($_USERGROUPS, 'view')) {
                continue;
            }
            $result = array();
            $result['relevance'] = $d['relevance'];
            $result['title'] = $d['pr_name'] . ' - ' . $d['pr_title'];
            $result['image'] = !empty($d['pr_image']) ? 'profiles/' . $d['pr_image'] : '';
            $result['body'] = $d['pr_description'] . ' <br />' . $d['pr_quote'];
            $result['url'] = Jojo_Plugin_Jojo_profile::getProfileUrl($d['profileid'], $d['pr_url'], $d['pr_name'], $d['pr_language'], $d['pr_category']);
            $result['absoluteurl'] = _SITEURL. '/' . $result['url'];
            $result['id'] = $d['profileid'];
            $result['plugin'] = 'jojo_profile';
            $result['type'] = $d['pg_title'] ? $d['pg_title'] : 'Profiles';
            $results[] = $result;
        }


        /* Return results */
        return $results;
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


    /**
     * Get the url prefix for a particular part of this plugin
     */
    public static function _getPrefix($for='quides', $language=false, $categoryid=false) {
        $cacheKey = $for;
        $cacheKey .= ($language) ? $language : 'false';
        $cacheKey .= ($categoryid) ? $categoryid : 'false';

        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        $language = !empty($language) ? $language : Jojo::getOption('multilanguage-default', 'en');
        $_PROFILECATEGORIES = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_PROFILECATEGORIES && !empty($categoryid)) ? Jojo::selectRow("SELECT `pc_url` FROM {profilecategory} WHERE `profilecategoryid` = $categoryid") : '';
        $category = ($_PROFILECATEGORIES && !empty($categoryid)) ? $categorydata['pc_url'] : '';
        $query = "SELECT pageid, pg_title, pg_url FROM {page} WHERE pg_link = ?";
        $query .= (_MULTILANGUAGE) ? " AND pg_language = '$language'" : '';
        $query .= (!empty($category)) ? " AND pg_url LIKE '%$category'": '';
        $res = Jojo::selectRow($query, array('Jojo_Plugin_Jojo_profile'));

        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . Jojo::cleanURL($res['pg_title']);
        } else {
            $_cache[$cacheKey] = '';
        }
        return $_cache[$cacheKey];
    }



    function getCorrectUrl()
    {
        $pg_url = $this->page['pg_url'];
        $language = !empty($this->page['pg_language']) ? $this->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
        $profileid = Util::getFormData('id', 0);
        $url       = Util::getFormData('url', '');
        $action    = Util::getFormData('action', '');
        $pagenum   = Util::getFormData('pagenum', 1);
        $_PROFILECATEGORIES = (Jojo::getOption('profile_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_PROFILECATEGORIES) ? Jojo::selectRow("SELECT profilecategoryid, sortby FROM {profilecategory} WHERE pc_url = '$pg_url'") : '';
        $categoryid = ($_PROFILECATEGORIES && count($categorydata)) ? $categorydata['profilecategoryid'] : '';

        $correcturl = JOJO_Plugin_Jojo_profile::getProfileUrl($profileid, $url, null, $language, $categoryid);
        if ($correcturl) {
            return _SITEURL . '/' . $correcturl;
        }

        /* profile index with pagination */
        if ($pagenum > 1) return parent::getCorrectUrl().'p'.$pagenum.'/';

        /* profile index - default */
        return parent::getCorrectUrl();
    }

    static public function isUrl($uri)
    {
        $prefix = false;
        $getvars = array();

        /* Check the suffix matches and extra the prefix */
        if (preg_match('#^(.+)/([0-9]+)/([^/]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]/[string]" eg "items/123/name-of-item/" */
            $prefix = $matches[1];
            $getvars = array(
                        'id' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]" eg "items/123/" */
            $prefix = $matches[1];
            $getvars = array(
                        'id' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/p([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/p[pagenum:([0-9]+)]" eg "items/p2/" for pagination of items */
            $prefix = $matches[1];
            $getvars = array(
                        'pagenum' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/((?!rss)([^/]+))$#', $uri, $matches)) {
            /* "$prefix/[url:((?!rss)string)]" eg "items/name-of-item/" ignoring "items/rss" */
            $prefix = $matches[1];
            $getvars = array(
                        'url' => $matches[2]
                        );
        } else {
            /* Didn't match */
            return false;
        }

        /* Check the prefix matches */
        if ($res = Jojo_Plugin_Jojo_profile::checkPrefix($prefix)) {
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

    /**
     * Check if a prefix belongs to this plugin
     */
    static public function checkPrefix($prefix)
    {
        /* Cache some stuff */
        static $_prefixes, $languages, $categories;
        if (!isset($languages)) {
            /* Initialise cache */
            $languages = Jojo::selectAssoc("SELECT languageid, languageid as languageid2 FROM {language} WHERE active = 'yes'");
            $categories = array(false);
            if (Jojo::getOption('profile_enable_categories', 'no') == 'yes') {
                $categories = array_merge($categories, Jojo::selectAssoc("SELECT profilecategoryid, profilecategoryid as profilecategoryid2 FROM {profilecategory}"));
            }
            $_prefixes = array();
        }

        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }

        /* Check everything */
        foreach ($languages as $language) {
            $language = $language ? $language : Jojo::getOption('multilanguage-default', 'en');
            foreach($categories as $category) {
                $testPrefix = Jojo_Plugin_Jojo_profile::_getPrefix('', $language, $category);
                $_prefixes[$testPrefix] = true;
                if ($testPrefix == $prefix) {
                    /* The prefix is good */
                    return true;
                }
            }
        }

        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }
}
