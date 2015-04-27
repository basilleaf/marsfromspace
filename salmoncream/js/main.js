/*globals WPURLS */

jQuery(function($) {

	 // if this is detail page, make teh big image link back to the hirise original page
	 // the linkback element holds the original hiris url  
	 if ($('.linkback').is(':visible')) {
	 	// the linkback element holds the original hiris url  
	 	// link the featured image to this url
	 	link = $('.linkback').data('linkback')
	 	console.log(link)
	 	$('img.wp-post-image').wrap('<a href = "' + link + '"></a>');
	 	console.log('ok')
	 }

	/**
	 * Mobile Navigation
	 */
	var mobileNav = $('.navigation-main').clone().removeClass('navigation-main').addClass('navigation-main-mobile').attr('id', 'site-navigation-mobile').append('<h1 class="menu-toggle"></h1>');

	$('.site-header').append(mobileNav);

	$('.menu-toggle').on( 'click', function() {

		$(this).toggleClass('toggled-on');

		$('.navigation-main-mobile .menu').slideToggle();

	});


	/**
	 * slideToggle Comments
	 */
	$('.toggle-comments').on('click', function(e) {

		$(this).toggleClass('comments-hidden');

		$('.comments-wrapper').slideToggle();

		e.preventDefault();

	});

	/**
	 * Enable dropkick.js for select elements
	 */
	$('select').dropkick({

		change: function (value) {

			var name = $(this).attr('name');

			// for archive dropdowns

			if ( name === 'archive-dropdown') {

				location.href = value;

			}

			// for category dropdowns

			if ( name === 'cat' ) {

				location.href = WPURLS.siteurl + "?cat=" + value;

			}

		}

	});

	/**
	 * Enable flexslider
	 */
	$('.flexslider').flexslider({

		animation: "slide",
		controlNav: false,
		pauseOnHover: true

    });

	/**
	 * Main Sub-Navigation Positioning Fix
	 */
    var offset;

    $('.navigation-main .sub-menu').each(function() {

		var $this = $(this);

		offset = $this.parent('li').offset();

		offset.right = $(window).width() - (offset.left + $this.parent('li').outerWidth(true));

		if ( $('body').hasClass('rtl') ) {

			$this.css('padding-left', offset.left);

		} else {

			$this.css('padding-right', offset.right);

		}

    });

});
