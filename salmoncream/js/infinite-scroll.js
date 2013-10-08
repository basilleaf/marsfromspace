/*global INFSCR */
/* hacked by Lisa B */

jQuery(function($) {

    var count = 2;
	var total = INFSCR.max_num_pages;
    var time_last_load = new Date() / 1000;
    var the_end_msg = false;

	function loadArticle(pageNumber) {
		$('#infinite-loader').fadeIn('fast');
        $.ajax({
            url: INFSCR.wpurl + "/wp-admin/admin-ajax.php",
            type:'POST',
            data: "action=infinite_scroll&page_no=" + pageNumber + "&post_type=" + INFSCR.post_type + "&skill=" + INFSCR.skill,
            success: function(html){
				$('#infinite-loader').fadeOut();
                $(html).appendTo("#content").hide().fadeIn();
            }
        });
        return false;
    }

	$(document).scroll(function() {
		if ( (window.innerHeight + window.scrollY) >= $(document).height()*0.75) {
			if (count > total) {
                if (!the_end_msg) {
                    the_end_msg = true;
                    $("<p>The End!</p>").appendTo("#content").hide().fadeIn();
                }
				return false;
			}else {
                time_now = new Date() / 1000;
                if( ( (time_now - time_last_load) > 5.0) ||
                    ((window.innerHeight + window.scrollY) === $(document).height()) ) {
                    // either 10 seconds have gone by or user is sitting at bottom of page
                    time_last_load = time_now;
                    loadArticle(count);
                    count++;
                }
			}
		}
	});

});
