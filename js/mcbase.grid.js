/* Based on the Clean script from Drupal 6  */

(function ($) {

  Drupal.behaviors.mcbase = {
    attach: function(context, settings) {
        
    /*
    alert('Mcbase Theme Path:' + Drupal.settings.mcbase.theme_path);
    alert('Mcbase Theme Key:' + Drupal.settings.mcbase.theme_key);
    alert('Mcbase default state:' + Drupal.settings.mcbase.default_state);
    */

      var $grid_image = Drupal.settings.mcbase.theme_path + '/images/blank.png';
      var $theme_key = Drupal.settings.mcbase.theme_key;
      var $default_state = Drupal.settings.mcbase.default_state;
      var $grid_colour = Drupal.settings.mcbase.grid_colour
    
      $("body").prepend('<img src="' + $grid_image + '" id="mcbase-grid" class="' + $default_state + ' ' + $grid_colour + ' ' + $theme_key + '" />');
      $('body').prepend('<a href="#" id="mcbase-grid-toggle" class="' + $default_state + '">GRID</a>');
            
      $('#mcbase-grid-toggle').click(function() {
        $('#mcbase-grid, #mcbase-grid-toggle').toggleClass('grid-disabled').toggleClass('grid-enabled');
      });
    }
  };

}(jQuery));
