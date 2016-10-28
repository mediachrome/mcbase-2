(function ($) {

  Drupal.behaviors.mcbase_breakpoints = {
      
      attach: function(context, settings) {
      
      $('body').prepend('<div id="responsive-indicator"><div id="mcbase-viewport"></div><span class="min-1140">min 1140</span><span class="min-960">min 960 / max 1140</span><span class="min-800">min 800 / max 960</span><span class="min-768">min 768 / max 800</span><span class="min-600">min 600 / max 768</span><span class="min-480">min 480 / max 600</span><span class="min-320">min 320 / max 480</span>');
      
      /**
       * detect window size from value set by CSS
       * with thanks to Matt Wilcox for the window.resize stuff
       * http://mattwilcox.net/archive/entry/id/1088/
       * IE < 9 doesn't use getComputedStyle so 
       */
      
      if(window.getComputedStyle) {
        var dev_mode = Drupal.settings.mcbase.dev_mode;
        alert('dev_mode = ' + dev_mode);
        var current_size; // defaults to blank so it's always analysed on first load
        var did_resize  = true; // defaults to true so it's always analysed on first load
        var navigation_classes = $('#navigation').attr("class"); // store the default #navigation classes
        var raw_navigation = $("#navigation").html(); // grab the unaltered #navigation contents and store it for simpler resets on resize
             
                
        // on window resize, set the didResize to true and print out the viewport size
        
        $(window).resize(function() {
          did_resize = true;
        });
        
        // on window resize, print the viewport size 
        
        // http://stackoverflow.com/questions/21042102/is-there-a-free-extension-for-safari-that-displays-the-viewport-size
        $(window).resize(function() {
          var myViewportWidth = $(window).width();
          var ems = myViewportWidth / parseFloat($("body").css("font-size"));
          $('#mcbase-viewport').html(myViewportWidth+"px | "+ems+ "ems");
        });

        
        // every 1/4 second, check if the browser was resized
        setInterval(function() {
          if(did_resize) {
            did_resize = false;
           
            // Define the screen width by using the variable set by breakpoint.css in body:after
            var new_size = window.getComputedStyle(document.body,':after').getPropertyValue('content');
            // tidy up after inconsistent browsers (some include quotation marks, they shouldn't) 
            new_size = new_size.replace(/"/g, "");
            new_size = new_size.replace(/'/g, "");
                     
            // sweep clean before we make any changes
            // if the new breakpoint is different to the old one, do some stuff
            
            if (new_size !== current_size) {
              // alert('new size does not equal the current stored size');                   
              // replace #navigation content)
              $("#navigation .limiter").remove();
              $("#navigation").html(raw_navigation);
              
              // run some custom script triggered by screen size
              // these values may need to be changed to match the values being
              // set by breakpoint.css
              
              if (new_size === 'phone') {}
              if (new_size === 'tablet') {}
              if (new_size === 'standard') {}
              if (new_size === 'widescreen') {}
              if (new_size === 'unlimited') {}
             
              
              current_size = new_size;
              // if we're in development mode, print this out somewhere we can see
              if(dev_mode === 1) {
              if ($("#responsive-indicator").length) {
              $("#responsive-indicator .current-size").remove();
              $("#responsive-indicator").append("<div class='current-size'></div>");
              $(".current-size").html('js: ' + current_size + ' / css: ' + new_size);
              }}
            }
          }
        }, 250);
      }
    }
  };
}(jQuery));
