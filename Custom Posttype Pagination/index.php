<?php

//------------------------------------------------------------------//
// blog MODULE
//------------------------------------------------------------------//

	// Query 'blog' custom post type
	//--------------------------------------------------------------------
	$variables		= array(); // create empty array
	
	// set paginate rules
	if ( get_query_var('paged') ) { $paged = get_query_var('paged'); } else if ( get_query_var('page') ) {$paged = get_query_var('page'); } else {$paged = 1; }
	$temp = $wp_query;  // re-sets query
	$wp_query = null;   // re-sets query
	
	// create args
	$args = array( 'post_type' => 'blog', 'posts_per_page' => 1, 'paged' => $paged, 'order' => 'DESC');
  
  // slightly different WP loop setup than my normal
  $wp_query = new WP_Query();
  $wp_query->query( $args );
  
  // loop through the post
  while ($wp_query->have_posts()) : $wp_query->the_post(); 

		// variables
		$postTitle		= ( get_field('blog_post_title') ? get_field('blog_post_title') : get_the_title() );
		$displayPost	= dateCheck( get_field('blog_post_date') );

		// load post data into an array
		$variables[] = array(
			'postID'			=> get_the_id(),
			'title' 			=> ucwords($postTitle),
			'true_title' 		=> ucwords(get_the_title()),
			'slug'				=> basename(get_permalink()),
			'permalink' 		=> get_the_permalink(),
			'activePost'		=> $displayPost,
			'content'			=> get_the_content(),
			'acf-fields'		=> get_fields(get_the_id()),		
		);

		//echo '<pre>', print_r($variables, true), '</pre>';
		
		echo get_the_title().'<br>';
		echo get_the_content().'<br>';
				
	// Endwhile - don't reset query if we're pagination
	//--------------------------------------------------------------------
	endwhile;

?>
<nav>
<?php
	paginate(); 
	$wp_query = null;
	$wp_query = $temp; // Reset
?>
</nav>


