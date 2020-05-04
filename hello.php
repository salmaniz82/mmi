<?php 


$baseDirectoryRootPath = $_SERVER['DOCUMENT_ROOT'];
include $baseDirectoryRootPath."/"."wp-config.php";
include $baseDirectoryRootPath."/"."wp-blog-header.php";
global $wpdb;



$args = array(  
        'post_type' => 'appointments',
        'post_status' => 'publish',
        'posts_per_page' => -1, 
        'orderby' => 'title', 
        'order' => 'ASC'
        
    );

    $loop = new WP_Query( $args ); 
        
    while ( $loop->have_posts() ) : $loop->the_post(); 

    	/*
        $featured_img = wp_get_attachment_image_src( $post->ID );
        */


        /*
        echo $post->ID; 
	    print the_title();
        */

        echo "<pre>";



        var_dump($post);


        echo "</pre>";
        
        
        
    endwhile;

    wp_reset_postdata(); 





 ?>