# WP Post Popularity

WordPress plugin which keeps track of all time post popularity. The plugin doesn't do anything else other than that. You can reset post views under Settings->Post Popularity but keep in mind that if you are testing you need to reset your session history as well in order to track the count for already visited post again right away.

### How to use 

If you want to show the most popular posts you can use following arguments to create a query

	$args = array(   
		'meta_key' => 'post_popularity',   
		'orderby' => 'meta_value_num',   
		'order' => 'DESC'   
	);   
	$query = new WP_Query($args);   

And then inside a loop you can get a view count value like this: 

    echo get_post_meta(get_the_ID(), 'post_popularity', true);