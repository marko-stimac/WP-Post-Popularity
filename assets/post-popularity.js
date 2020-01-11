jQuery(document).ready(function ($) {

	// Call function for increasing views after 1 sec
	setTimeout(function () {
		incrementPostPopularity();
	}, 1000);

	// Increase post popularity for single pages
	function incrementPostPopularity() {

		jQuery.ajax({
			data: {
				action: 'set_post_popularity',
				post_id: postpopularity.post_id,
				is_singular: postpopularity.is_singular,
				visited_pages: postpopularity.visited_pages,
				nonce: postpopularity.nonce
			},
			type: 'POST',
			url: postpopularity.url,
			success: function (resp) {
				// Add to session post id
				addPostToSession(postpopularity.post_id);
				console.log(resp);
			},
			error: function (error) {
				console.log(error);
			}

		});

	}

	// Add to sessions
	function addPostToSession(post_id) {

		jQuery.ajax({
			data: {
				action: 'add_post_to_session',
				nonce: postpopularity.nonce,
				post_id: post_id
			},
			type: 'POST',
			url: postpopularity.url,
			success: function (resp) {
				console.log(resp);
			},
			error: function (error) {
				console.log(error);
			}
		});

	}

});