
    
    //image on visible float up
        var logos = $('.fade-in');
        var $window = $(window);

        function check_if_in_view() {
          var window_height = $window.height();
          var window_top_position = $window.scrollTop();
          var window_bottom_position = (window_top_position + window_height);
            

          $.each(logos, function() {
            var $element = $(this);
            var element_height = $element.outerHeight();
            var element_top_position = $element.offset().top;
            var element_bottom_position = (element_top_position + element_height);
              
            var $container = $element.parent();
            console.log($container);
            var container_height = $container.outerHeight();
            var container_top_position = $container.offset().top;
            var container_bottom_position = (container_top_position + container_height);

            //check to see if this current container is within viewport
            if (container_top_position <= window_bottom_position) {
              $element.addClass('in-view');
              //set counter to 1 so it only runs once.
              counter = 1;
            }
          });
        }
        
        $window.on('scroll resize', check_if_in_view );
        $window.trigger('scroll');


