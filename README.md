# WP Post Popularity

Wordpress plugin which keeps track of all time post popularity

### How to use 

Plugin works out of the box but if you want to show the most popular posts you can use following arguments

	$args = array(   
		'meta_key' => 'post_popularity',   
		'orderby' => 'meta_value_num',   
		'order' => 'DESC'   
	);   
	$query = new WP_Query($args);   

Inside loop you can get view count value

     get_post_meta(get_the_ID(), 'post_popularity', true)