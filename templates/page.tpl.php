 <?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/garland.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 */
?>
<!-- mcbase page.tpl.php -->
<div class="page-wrapper">

<header class="header"><div class="limiter">

<?php /* if ($main_menu): ?>
<p class="skip-link"><em><a href="#navigation">Skip to Navigation</a></em><!--  &darr; --></p>
<?php endif; */ ?>

<?php print render($page['header_first']); ?>
<?php print render($page['header_second']); ?>

</div></header> <!-- /.section, /.header -->

<?php if ($page['navigation']): ?>
<nav id="navigation" role="navigation"><div class="limiter">
<?php print render($page['navigation']); ?>
<?php if ($breadcrumb): ?>
  <div id="breadcrumb"><?php print $breadcrumb; ?></div>
<?php endif; ?>
</div></nav> <!-- /.limiter, /#navigation -->
<?php endif; ?>

<div class="branding" role="banner"><div class="limiter">

<?php if ($logo): ?>
<a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="logo">
<img src="/<?php print $directory; ?>/logo.svg" alt="<?php print t('Home'); ?>" />
<?php /* <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" /> */ ?>
</a>
<?php endif; ?>

<?php if ($site_name || $site_slogan): ?>
<div id="name-and-slogan">

<?php if ($site_name): ?>
<?php if ($title): ?>
<div id="site-name"><strong>
<a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
</strong></div>

<?php else: /* Use h1 when the content title is empty */ ?>
<h1 id="site-name">
<a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
</h1>
<?php endif; ?>
<?php endif; ?>

<?php if ($site_slogan): ?>
<div id="site-slogan"><?php print $site_slogan; ?></div>
<?php endif; ?>

</div> <!-- /#name-and-slogan -->
<?php endif; ?>

</div></div> <!-- /.limiter, /.branding -->

<div id="main-wrapper"><div id="main" class="limiter<?php /*  if ($main_menu) { print ' with-navigation'; } */ ?>">

<?php if($messages) : ?>
<div class="message-wrapper">
<?php print $messages; ?>
</div>
<?php endif; ?>

<div id="content" class="column" role="main"><div class="section">

<?php if ($page['highlighted']): ?>
<div id="highlighted"><?php print render($page['highlighted']); ?></div>
<?php endif; ?>

<?php if ($tabs): ?>
<div class="tabs clearfix"><?php print render($tabs); ?></div>
<?php endif; ?>

<?php print render($title_prefix); ?>
<?php if ($title): ?>
<h1 class="title" id="page-title"><?php print $title; ?></h1>
<?php endif; ?>
<?php print render($title_suffix); ?>

<?php print render($page['help']); ?>

<?php if ($action_links): ?>
<ul class="action-links"><?php print render($action_links); ?></ul>
<?php endif; ?>

<?php if($page['content_top']) : ?>
<div class="content-top">
<?php print render($page['content_top']); ?>
</div>
<?php endif; ?>

<?php print render($page['content']); ?>
<?php /* print $feed_icons; */ ?>

<?php if($page['content_bottom']) : ?>
<div class="content-bottom">
<?php print render($page['content_bottom']); ?>
</div>
<?php endif; ?>


</div></div> <!-- /.section, /#content -->

<?php if ($page['sidebar_first']): ?>
<aside id="sidebar-first" class="column sidebar" role="complementary"><div class="section">
<?php print render($page['sidebar_first']); ?>
</div></aside> <!-- /.section, /#sidebar-first -->
<?php endif; ?>

<?php if ($page['sidebar_second']): ?>
<aside id="sidebar-second" class="column sidebar" role="complementary"><div class="section">
<?php print render($page['sidebar_second']); ?>
</div></aside> <!-- /.section, /#sidebar-second -->
<?php endif; ?>

</div></div> <!-- /#main, /#main-wrapper -->

<?php if($page['pre_footer']) : ?>
<div id="pre-footer" role="contentinfo"><div class="limiter">
<?php print render($page['pre_footer']); ?>
</div></div> <!-- /.limiter, /#pre-footer -->
<?php endif; ?>

<footer class="page-footer"><div class="limiter">

<div class="footer-first footer-block">
<?php print render($page['footer_first']); ?>
</div>

<div class="footer-second footer-block">
<?php print render($page['footer_second']); ?>
</div>

<div class="footer-third footer-block">
<?php print render($page['footer_third']); ?>
</div>

<div class="final-footer" role="contentinfo">
<?php print render($page['final_footer']); ?>
</div> <!-- /.final-footer -->

</div></footer> <!-- /.limiter, /.page-footer -->

</div> <!-- /.page-wrapper -->
