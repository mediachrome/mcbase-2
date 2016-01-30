<?php

/**
 * @file
 * Contains theme override functions and preprocess functions for the mcbase theme.
 */
 
// Auto-rebuild the theme registry during theme development.
if (theme_get_setting('mcbase_rebuild_registry') && !defined('MAINTENANCE_MODE')) {
  // Rebuild .info data.
  system_rebuild_theme_data();
  // Rebuild theme registry.
  drupal_theme_rebuild();
}

/**
 * Lifted from Zen, wher the code is stored in secondary .inc file
 *
 * @file
 * Contains infrequently used theme registry build functions.
 */

/**
 * Implements HOOK_theme().
 *
 * We are simply using this hook as a convenient time to do some related work.
 */
function mcbase_theme(&$existing, $type, $theme, $path) {
  // If we are auto-rebuilding the theme registry, warn about the feature.
  if (
    // Only display for site config admins.
    function_exists('user_access') && user_access('administer site configuration')
    && theme_get_setting('mcbase_rebuild_registry')
    // Always display in the admin section, otherwise limit to three per hour.
    && (arg(0) == 'admin' || flood_is_allowed($GLOBALS['theme'] . '_rebuild_registry_warning', 3))
  ) {
    flood_register_event($GLOBALS['theme'] . '_rebuild_registry_warning');
    drupal_set_message(t('For easier theme development, the theme registry is being rebuilt on every page request. It is <em>extremely</em> important to <a href="!link">turn off this feature</a> on production websites.', array('!link' => url('admin/appearance/settings/' . $GLOBALS['theme']))), 'warning', FALSE);
  }

  // hook_theme() expects an array, so return an empty one.
  return array();
}

/**
 * Changes the default meta content-type tag to the shorter HTML5 version
 */
function mcbase_html_head_alter(&$head_elements) {
  $head_elements['system_meta_content_type']['#attributes'] = array(
    'charset' => 'utf-8'
  );
}

/**
 * Changes the search form to use the HTML5 "search" input attribute
 */
function mcbase_preprocess_search_block_form(&$vars) {
  $vars['search_form'] = str_replace('type="text"', 'type="search"', $vars['search_form']);
}

 
/**
 * taxonomy_node_get_terms function.
 * 
 * In order to get body classes based on taxonomy, we need
 * to get the taxonomy values for the current node
 * 
 * @access public
 * @param mixed $node
 * @param string $key (default: 'tid')
 * @return taxonomy terms
 */
 
function taxonomy_node_get_terms($node, $key = 'tid') {
    static $terms;
    
    if (!isset($terms[$node->vid][$key])) {
        $query = db_select('taxonomy_index', 'r');
        $t_alias = $query->join('taxonomy_term_data', 't', 'r.tid = t.tid');
        $v_alias = $query->join('taxonomy_vocabulary', 'v', 't.vid = v.vid');
        $query->fields( $t_alias );
        $query->condition("r.nid", $node->nid);
        $result = $query->execute();
        // dpm($result);
        $terms[$node->vid][$key] = array();
        foreach ($result as $term) {
            $terms[$node->vid][$key][$term->$key] = $term;
        }
    }
   // dpm($terms[$node->vid][$key]);
    return $terms[$node->vid][$key];
}

function mcbase_preprocess_page(&$vars, $hook) {
  // Removes the enclosing tab html if there are no tabs to display
  if (empty($vars['tabs']['#primary']) && empty($vars['tabs']['#primary'])) {
    $vars['tabs'] = '';
  }
  
  // generate template suggestions per content type
  if (isset($vars['node']->type)) {
    $vars['theme_hook_suggestions'][] = 'page__' . $vars['node']->type;
  }
  // generate template suggestions per vocabulary
  if(arg(0) == 'taxonomy' && arg(1) == 'term') {
    $tid = (int)arg(2);
    $term = taxonomy_term_load($tid);
    if(is_object($term)) {
      $vars['theme_hook_suggestions'][] = 'page__vocabulary__'.$term->vocabulary_machine_name;
    }
  }
}

 
function mcbase_preprocess_html(&$vars) {
  
  // generate body classes per vocabulary
  if(arg(0) == 'taxonomy' && arg(1) == 'term') {
    $tid = (int)arg(2);
    $term = taxonomy_term_load($tid);
    if(is_object($term)) {    
      $vars['classes_array'][] = 'vocabulary-'.$term->vocabulary_machine_name;
   }
  }

// explicitly declare the object
// http://drupal.org/node/1065270#comment-4107538
 
  $vars['rdf'] = new stdClass;
  
// Uses RDFa attributes if the RDF module is enabled
// Lifted from Adaptivetheme for D7, full credit to Jeff Burnz
// ref: http://drupal.org/node/887600
  if (module_exists('rdf')) {
    $vars['doctype'] = '<!DOCTYPE html>' . "\n";
    $vars['rdf']->version = 'version="HTML+RDFa 1.1"';
    $vars['rdf']->namespaces = $vars['rdf_namespaces'];
    $vars['rdf']->profile = ' profile="' . $vars['grddl_profile'] . '"';
  } else {
    $vars['doctype'] = '<!DOCTYPE html>' . "\n";
    $vars['rdf']->version = '';
    $vars['rdf']->namespaces = '';
    $vars['rdf']->profile = '';
  }
  
  
// prep the scripts array for grid overlay and breakpoints
  global $theme_key;
  $base_path = base_path();
  $path_to_mcbase = drupal_get_path('theme', 'mcbase');
  $vars['base_theme'] = $path_to_mcbase;
  // store the various values in an array
  $settings = array(
    'theme_path' => $base_path . $path_to_mcbase,
    'theme_key' => str_replace('_', '-', $theme_key),
    'default_state' => theme_get_setting('mcbase_overlay_default_state'),
    'grid_colour' => theme_get_setting('mcbase_grid_colour'),
    'dev_mode' => theme_get_setting('mcbase_enable_overlay_grid'),
    'breakpoints' => theme_get_setting('mcbase_display_breakpoints'),
  );
  
  // add the $settings array within the mcbase namespace to Drupal.settings
  drupal_add_js(array('mcbase' => $settings), 'setting');
  
  // Breakpoints and overlays are site-specific. 
  // Load them from the child theme
  
  $child_theme_path = path_to_theme();
  drupal_add_js('jQuery.extend(Drupal.settings, { "pathToTheme": "' . path_to_theme() . '" });', 'inline');
  
  if (theme_get_setting('mcbase_enable_overlay_grid')) {
    drupal_add_js($child_theme_path . '/js/grid.js');
    drupal_add_css($child_theme_path . '/css/grid.css');
  }
  if (theme_get_setting('mcbase_display_breakpoints')) {
    drupal_add_js($child_theme_path . '/js/breakpoints.js');
    drupal_add_css($child_theme_path . '/css/breakpoints.css');
  }

  // Add Term name classes on nodes
 if(arg(0)=='node' && is_numeric(arg(1))) {
    $node = node_load(arg(1)); 
    $results = taxonomy_node_get_terms($node);
     // print_r($results);
    if(is_array($results)) {
      foreach ($results as $item) {
        $vars['classes_array'][] = "term-".strtolower(drupal_clean_css_identifier($item->name));
      }
    }
  }

  // Add theme name class to body
  $vars['classes_array'][] = 'theme-'.$theme_key;


  // Store the menu item since it has some useful information.
  $vars['menu_item'] = menu_get_item();
  switch ($vars['menu_item']['page_callback']) {
    case 'views_page':
      // Is this a Views page?
      $vars['classes_array'][] = 'page-views';
      break;
    case 'page_manager_page_execute':
    case 'page_manager_node_view':
    case 'page_manager_contact_site':
      // Is this a Panels page?
      $vars['classes_array'][] = 'page-panels';
      break;
  }
}


/**
 * Duplicate of theme_menu_local_tasks() but adds clearfix to tabs.
 *
 * From Zen
 */
function mcbase_menu_local_tasks(&$vars) {
  $output = '';

  if ($primary = drupal_render($vars['primary'])) {
    $output .= '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $output .= '<ul class="tabs primary clearfix">' . $primary . '</ul>';
  }
  if ($secondary = drupal_render($vars['secondary'])) {
    $output .= '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $output .= '<ul class="tabs secondary clearfix">' . $secondary . '</ul>';
  }

  return $output;
}


/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return
 *   A string containing the breadcrumb output.
 */
function mcbase_breadcrumb($vars) {
  $breadcrumb = $vars['breadcrumb'];
  // Determine if we are to display the breadcrumb.
  $show_breadcrumb = theme_get_setting('breadcrumb_display');
  if ($show_breadcrumb == 'yes') {

    // Optionally get rid of the homepage link.
    $show_breadcrumb_home = theme_get_setting('breadcrumb_home');
    if (!$show_breadcrumb_home) {
      array_shift($breadcrumb);
    }

    // Return the breadcrumb with separators.
    if (!empty($breadcrumb)) {
      $separator = filter_xss(theme_get_setting('breadcrumb_separator'));
      $trailing_separator = $title = '';

      // Add the title and trailing separator
      if (theme_get_setting('breadcrumb_title')) {
        if ($title = drupal_get_title()) {
          $trailing_separator = $separator;
        }
      }
      // Just add the trailing separator
      elseif (theme_get_setting('breadcrumb_trailing')) {
        $trailing_separator = $separator;
      }

      // Assemble the breadcrumb
      return implode($separator, $breadcrumb) . $trailing_separator . $title;
    }
  }
  // Otherwise, return an empty string.
  return '';
}

/**
 * Implements hook_css_alter().
 * @TODO: Once http://drupal.org/node/901062 is resolved, determine whether
 * this can be implemented in the .info file instead.
 *
 * Omitted:
 * - color.css
 * - contextual.css
 * - dashboard.css
 * - field_ui.css
 * - image.css
 * - locale.css
 * - shortcut.css
 * - simpletest.css
 * - toolbar.css
 */
function mcbase_css_alter(&$css) {
  $exclude = array(
    /* 'misc/vertical-tabs.css' => FALSE, */
    'modules/aggregator/aggregator.css' => FALSE,
    'modules/block/block.css' => FALSE,
    'modules/book/book.css' => FALSE,
    'modules/comment/comment.css' => FALSE,
    'modules/dblog/dblog.css' => FALSE,
    'modules/file/file.css' => FALSE,
    'modules/filter/filter.css' => FALSE,
    'modules/forum/forum.css' => FALSE,
    'modules/help/help.css' => FALSE,
    'modules/menu/menu.css' => FALSE,
    'modules/node/node.css' => FALSE,
    'modules/openid/openid.css' => FALSE,
    'modules/poll/poll.css' => FALSE,
    'modules/profile/profile.css' => FALSE,
    'modules/search/search.css' => FALSE,
    'modules/statistics/statistics.css' => FALSE,
    'modules/syslog/syslog.css' => FALSE,
    'modules/system/admin.css' => FALSE,
    'modules/system/maintenance.css' => FALSE,
    'modules/system/system.css' => FALSE,
    'modules/system/system.admin.css' => FALSE,
    /* 'modules/system/system.base.css' => FALSE, */
    'modules/system/system.maintenance.css' => FALSE,
    'modules/system/system.menus.css' => FALSE,
    /* 'modules/system/system.messages.css' => FALSE, */
    /* 'modules/system/system.theme.css' => FALSE, */
    'modules/taxonomy/taxonomy.css' => FALSE,
    'modules/tracker/tracker.css' => FALSE,
    'modules/update/update.css' => FALSE,
    'modules/user/user.css' => FALSE,
  );
  $css = array_diff_key($css, $exclude);
}

/**
 * mcbase_form_alter function.
 * 
 * @access public
 * @param mixed &$form
 * @param mixed &$form_state
 * @param mixed $form_id
 * @return modified form values
 */
function mcbase_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    $form['search_block_form']['#title'] = t('Search'); // Change the text on the label element
    $form['search_block_form']['#title_display'] = 'invisible'; // Toggle label visibilty
    $form['search_block_form']['#attributes']['placeholder'] = "Search..."; // Proper HTML 5 attribute
    $form['actions']['submit'] = array('#type' => 'image_button', '#src' => base_path() . path_to_theme() . '/images/search.png');
    unset($form['search_block_form']['#default_value']);
    /* $form['search_block_form']['#default_value'] = t('Search...'); // Set a default value for the textfield */
    /* $form['search_block_form']['#size'] = 25; */  // define size of the textfield
    /* $form['actions']['submit']['#value'] = t('GO!'); // Change the text on the submit button */
    
// Add extra attributes to the text box
    /* $form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = 'Search'; this.style.color = '#aaaaaa';}"; */
    /* $form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search') {this.value = ''; this.style.color = '#000000';}"; */    
  }
}

/**
 * Set a class on the iframe body element for WYSIWYG editors. This allows you
 * to easily override the background for the iframe body element.
 * This only works for the WYSIWYG module: http://drupal.org/project/wysiwyg
 */
function mcbase_wysiwyg_editor_settings_alter(&$settings, &$context) {
  $settings['bodyClass'] = 'wysiwygeditor';
}

/**
 * Have to override the whole theme_links() just to add the 
 * always useful odd even classes
 *
 */


function mcbase_links($vars) {
  $links = $vars['links'];
  $attributes = $vars['attributes'];
  $heading = $vars['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading.
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }
    
    $output .= '<a href="#nav" title="Show navigation">Show navigation</a>';
	$output .= '<a href="#" title="Hide navigation">Hide navigation</a>';



    $output .= '<ul' . drupal_attributes($attributes) . '>';
    

    foreach ($links as $key => $link) {
      $class = array($key);

      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
          && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $class[] = 'active';
      }

      $output .= '<li' . drupal_attributes(array('class' => $class)) . '>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      elseif (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $output .= "</li>\n";
    }

    $output .= '</ul>';
  }

  return $output;
}
