$(document).ready(function(){

	var mobile_width = 960;
	// Target your .container, .wrapper, .post, etc.

	function init() {
		$(".flex-video").fitVids();
		$('.flexslider').flexslider({
		    animation: "slide",
		    directionNav: false,
		    start: function(){
		    	if($(window).width() > mobile_width) {
					var s = skrollr.init();

					var s = $("#header");
				    var pos = s.position();
				    $(window).scroll(function() {
				        var windowpos = $(window).scrollTop();
				        if (windowpos >= pos.top) {
				            s.addClass("fixed");
				            $('#header-shim').show();
				        } else {
				            s.removeClass("fixed");
				            $('#header-shim').hide();
				        }
				    });
			    }
		    },
		});

		// desktop nav
		var $product_nav = $('#product-nav');
		$('#main-nav #main-nav-products a').click(function(e){
			e.preventDefault();
			if($product_nav.css("display") === "block") {
				$product_nav.slideUp("fast");
			} else {
				$product_nav.slideDown("fast");
			}
		});

		var $product_info = $('#product-info');
		$('#product-info-link').click(function(e){
			e.preventDefault();
			if($product_info.css("display") === "block") {
				$product_info.slideUp();
			} else {
				$product_info.slideDown();
			}
		});

		var $product_cf_container = $('#product-contact-form-container');
		$('#product-contact-form-link').click(function(e){
			e.preventDefault();
			if($product_cf_container.css("display") === "block") {
				$product_cf_container.slideUp();
			} else {
				$product_cf_container.slideDown();
			}
		});

		var $product_ingredients_container = $('#product-ingredients-container');
		$('#product-ingredients-link').click(function(e){
			e.preventDefault();
			if($product_ingredients_container.css("display") === "block") {
				$product_ingredients_container.slideUp();
			} else {
				$product_ingredients_container.slideDown();
			}
		});

		// Mobile Navigation
	    $('#mobile-toggle').click(function(e){
	        e.preventDefault();

	        if($(document.body).hasClass('nav-open')) {
	        	$(document.body).removeClass('nav-open');
		        $('#viewport').animate({right: 0});
		        $('#mobile-nav-container').animate({right: -250});
	        } else {
	        	$(document.body).addClass('nav-open');
		        $('#viewport').animate({right: 250});
		        $('#mobile-nav-container').animate({right: 0});
	        }
	    });

		$('#mobile-nav-container #main-nav-products a').click(function(e){
			e.preventDefault();
			$('#inner-mobile-nav').animate({left: -250});
		});
		$('#mobile-nav-container #back-to-menu-link a').click(function(e){
			e.preventDefault();
			$('#inner-mobile-nav').animate({left: 0});
		});

		// Contact Form Code

		var form_processing = false;

		$('#product-contact-form').submit(function(ev) {
	        // Prevent the form from actually submitting
	        ev.preventDefault();

	        if( form_processing === false ) {
	            form_processing = true;

	            // Get the post data
	            var data = $(this).serialize();

	            // Send it to the server
	            $.post('/', data, function(response) {
	                if (response.success) {
	                    $('#product-contact-form').fadeOut();
	                    $('#product-contact-form-notice').hide().html("Thanks for sharing your thoughts.").fadeIn();
	                } else {
	                    form_processing = false;
	                    $('#product-contact-form-notice').hide().html("Oops. " + response.error.fromEmail[0]).fadeIn();
	                }
	            });
	        }
	    });
	}


	$("#over-21").click(function(e){
		e.preventDefault();
		$('body').removeClass('age-check');

		var data = {
            "action": "restrictByAge/over21"
        };

        $.ajax({
            type: "POST",
            url: "/",
            data: data,
            dataType: "json",
            success: function() {
            	$('#viewport').removeClass('hidden');
            	init();
                $('#age-check-container').fadeOut();
            }
        });
	});

	$("#under-21").click(function(e){
		e.preventDefault();
		$('.under21-hide').hide();
		$('#under-21-notice').fadeIn();
	});

	if( !$('body').hasClass("age-check") ) {
		init();
	}

        $("#main-nav-products").click(function() {
          var scrollTarget = 420;
          if ($(document.body).scrollTop() <= scrollTarget) {
            $("html, body").stop().animate({scrollTop: scrollTarget}, '500', 'swing', function() {
               console.log('done');
            });
          }
        });
});
