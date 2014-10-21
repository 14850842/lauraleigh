jQuery(document).ready(function() {
	// Can also be used with $(document).ready()
      jQuery('#enquiryModal').on('show.bs.modal', function (e) {
          var wpackage = e.relatedTarget.dataset.package;
          jQuery('#input_2_3').val(wpackage);
        });
});

jQuery(window).load(function() {
      jQuery("#owl-testimonials").owlCarousel({

            navigation : true, // Show next and prev buttons
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            pagination:false,
            autoHeight : true,
            navigationText: ['<div class="nav-buttons"><span class="link"><span class="icon-wrap"><svg class="svg-icon shape-largearrow"><use xlink:href="#shape-largearrow"></use></svg></span><h3>Previous</h3></span>','<div class="nav-buttons"><span class="link"><span class="icon-wrap"><svg class="svg-icon shape-largearrow"><use xlink:href="#shape-largearrow"></use></svg></span><h3>Next</h3></span>'],
            transitionStyle : "fade"
       
            // "singleItem:true" is a shortcut for:
            // items : 1, 
            // itemsDesktop : false,
            // itemsDesktopSmall : false,
            // itemsTablet: false,
            // itemsMobile : false
       
        });


});