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

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_profile' => 'Profiles - Profile Listing and View'
        );

/* Register URI patterns */
Jojo::registerURI(null, 'jojo_plugin_jojo_profile', 'isUrl');

/* Sitemap filter */
Jojo::addFilter('jojo_sitemap', 'sitemap', 'jojo_profile');

/* XML Sitemap filter */
Jojo::addFilter('jojo_xml_sitemap', 'xmlsitemap', 'jojo_profile');

/* Search Filter */
Jojo::addFilter('jojo_search', 'search', 'jojo_profile');

/* Content Filter */
Jojo::addFilter('content', 'removesnip', 'jojo_profile');

/* capture the button press in the admin section */
Jojo::addHook('admin_action_after_save_page', 'admin_action_after_save_page', 'jojo_profile');
Jojo::addHook('admin_action_after_save_articlecategory', 'admin_action_after_save_profilecategory', 'jojo_profile');

$_options[] = array(
    'id'         => 'profile_tag_cloud_minimum',
    'category'   => 'Profiles',
    'label'      => 'Minimum tags to form cloud',
    'description' => 'On the profile pages, a tag cloud will be formed from tags if this number of tags is met (otherwise a plain text list of tags is shown). Set to zero to always use the plain text list.',
    'type'       => 'integer',
    'default'    => '0',
    'options'    => '',
    'plugin'     => 'jojo_profile'
);


$_options[] = array(
    'id'         => 'profilesperpage',
    'category'   => 'Profiles',
    'label'      => 'profiles per page on index',
    'description' => 'The number of profiles to show on the profiles index page before paginating',
    'type'       => 'integer',
    'default'    => '40',
    'options'    => '',
    'plugin'     => 'jojo_profile'
);

$_options[] = array(
    'id'         => 'profile_next_prev',
    'category'   => 'Profiles',
    'label'      => 'Show Next / Previous links',
    'description'=> 'Show a link to the next and previous profile at the top of each profile page',
    'type'       => 'radio',
    'default'    => 'yes',
    'options'    => 'yes,no',
    'plugin'     => 'jojo_profile'
);

$_options[] = array(
    'id' => 'profile_num_related',
    'category'   => 'Profiles',
    'label'      => 'Show Related profiles',
    'description'=> 'The number of related profiles to show at the bottom of each article (0 means do not show)',
    'type'       => 'integer',
    'default'    => '0',
    'options'    => '',
    'plugin'     => 'jojo_profile'
);


$_options[] = array(
    'id'          => 'profile_num_sidebar_articles',
    'category'    => 'Profiles',
    'label'       => 'Number of profile teasers to show in the sidebar',
    'description' => 'The number of profiles to be displayed as snippets in a teaser box on other pages)',
    'type'        => 'integer',
    'default'     => '1',
    'options'     => '',
    'plugin'      => 'jojo_profile'
);

$_options[] = array(
    'id'          => 'profile_sidebar_randomise',
    'category'    => 'Profiles',
    'label'       => 'Randmomise selection of teasers out of',
    'description' => 'Pick the sidebar items from a larger group, shuffle them, and then slice them back to the original number so that sidebar content is more dynamic  - set to 0 to disable',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => '',
    'plugin'      => 'jojo_profile'
);

$_options[] = array(
    'id'          => 'profile_inplacesitemap',
    'category'    => 'Profiles',
    'label'       => 'Profile sitemap location',
    'description' => 'Show profiles as a separate list on the site map, or in-place on the page list',
    'type'        => 'radio',
    'default'     => 'inplace',
    'options'     => 'separate,inplace',
    'plugin'      => 'jojo_profile'
);

$_options[] = array(
    'id'          => 'profile_sidebar_categories',
    'category'    => 'Profiles',
    'label'       => 'Profile teasers by category',
    'description' => 'Generate sidebar list from all Profiles and also create a list from each category',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_profile'
);

$_options[] = array(
    'id'          => 'profile_sidebar_exclude_current',
    'category'    => 'Profiles',
    'label'       => 'Exclude current Profile from list',
    'description' => 'Exclude the Profile from the sidebar list when on that Profiles page',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_profile'
);

$_options[] = array(
    'id'          => 'profile_meta_description',
    'category'    => 'Profiles',
    'label'       => 'Dynamic meta description',
    'description' => 'A dynamically built meta description template to use for articles, which will assist with SEO. Variables to use are [title], [site], [body].',
    'type'        => 'textarea',
    'default'     => '[title] - [body]...',
    'options'     => '',
    'plugin'      => 'jojo_article'
);