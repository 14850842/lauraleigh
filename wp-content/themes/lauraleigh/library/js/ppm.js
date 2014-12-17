jQuery(document).ready(function() {
	// Can also be used with $(document).ready()
 

    jQuery('.row-holder .col img,.portraitSizeImages img,.fullSizeImage img,.featureImage img').not('.layout img').imagesLoaded().progress( function( instance, image ) {
        var result = image.isLoaded ? 'loaded' : 'broken';
        jQuery(image.img).wrap(function() {
            var ratio = image.img.height/image.img.clientHeight;
            var height = image.img.clientHeight - (image.img.clientHeight*40)/image.img.naturalHeight;
            return ('<div class="watermark" style="overflow:hidden; height:'+Math.floor(height)+'px;"/>');
        });
    });

    jQuery('#enquiryModal').on('show.bs.modal', function (e) {
        var wpackage = e.relatedTarget.dataset.package;
        jQuery('#input_2_3').val(wpackage);
      });

      var ias = jQuery.ias({
        container:  "#main",
        item:       ".item",
        next:       ".prev-link a"
      });

      ias.extension(new IASSpinnerExtension());            // shows a spinner (a.k.a. loader)
      ias.extension(new IASTriggerExtension({
          html: '<div class="nav-buttons loadmore"><span class="link"><span class="icon-wrap"><svg class="svg-icon shape-plus"><use xlink:href="#shape-plus"></use></svg></span><h3>Load More</h3></span></div>'
      })); // shows a trigger after page 3
      ias.extension(new IASNoneLeftExtension({
        text: 'There are no more pages left to load.'      // override text when no pages left
    }));

    jQuery('.scrollit a').on('click',function(event){
        var jQueryanchor = jQuery(this);
        
        
        jQuery('html, body').stop().animate({
            scrollTop: jQuery(jQueryanchor.attr('href')).offset().top-200
        }, 1500);
        
        event.preventDefault();
    });

    
    var $head = jQuery( 'header' );
      jQuery( '.ha-waypoint' ).each( function(i) {
        var $el = jQuery( this );

        $el.waypoint( function( direction ) {
          if( direction === 'down' ) {
            $head.attr('class', 'header stuck');
          }
          else if( direction === 'up'){
            $head.attr('class', 'header unstuck');
          }
        }, { offset: '100%' } );
      } );
    

});

jQuery(window).load(function() {
      jQuery("#owl-testimonials").owlCarousel({

            navigation : true, // Show next and prev buttons
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            pagination:false,
            autoHeight : true,
            navigationText: ['<div class="nav-buttons"><span class="link"><span class="icon-wrap"><svg class="svg-icon shape-largearrow"><use xlink:href="#shape-largearrow"></use></svg></span><h3>Previous</h3></span></div>','<div class="nav-buttons"><span class="link"><span class="icon-wrap"><svg class="svg-icon shape-largearrow"><use xlink:href="#shape-largearrow"></use></svg></span><h3>Next</h3></span></div>'],

       
            // "singleItem:true" is a shortcut for:
            // items : 1, 
            // itemsDesktop : false,
            // itemsDesktopSmall : false,
            // itemsTablet: false,
            // itemsMobile : false
       
        });


});