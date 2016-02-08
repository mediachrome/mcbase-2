
 <?php

/**
 * @file
 * Tweaked output for $main_menu html block to facilitate a CSS driven dropdown 
 * menu with hooks for a js script to implement double tap access for 
 * touch screen devices.
 *
 * Forces an id of #mainmenu on the menu's containing block.
 *
 * For full functionality with Windows touch screen devices,
 * aria-haspopup="true" is also added as an attribute to parent <li> through
 * theme_menu_link() via mcbase template.php
 *
 * 
 *
 */
?>
<!-- thechapterhall block--system--main-menu.tpl.php -->
<?php $tag = $block->subject ? 'section' : 'div'; ?>
<<?php print $tag; ?> id="mainmenu" class="<?php print $classes; ?>"<?php print $attributes; ?>>

  <?php print render($title_prefix); ?>
  <?php if ($block->subject): ?>
    <h2<?php print $title_attributes; ?>><?php print $block->subject ?></h2>
  <?php endif;?>
  <?php print render($title_suffix); ?>
  
  <?php // CSS generated button will use these anchors to show and hide the navigation ?>
  <a href="#mainmenu" title="Show navigation">Show navigation</a>
	<a href="#" title="Hide navigation">Hide navigation</a>


  <?php print $content ?>


</<?php print $tag; ?>> <!-- /.block -->
