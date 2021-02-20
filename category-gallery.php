<?php
/*
Plugin Name: Category-Gallery
Description: A Gallery that shows images based on a image's designated category
Author: Jacob W Armstrong
*/

// add categories for attachments
function add_categories_for_attachments() {
    register_taxonomy_for_object_type( 'category', 'attachment' );
}
add_action( 'init' , 'add_categories_for_attachments' );

// add tags for attachments
function add_tags_for_attachments() {
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init' , 'add_tags_for_attachments' );

class CategoryGallery {
    //properties
    private $category;
    private $tags= [];
    
    function __construct($category) {
        add_shortcode('category-gallery', array($this, 'get_gallery_images') );
    }
    
    private function get_gallery_images() {
        //create args based on tag input or not
        $args = array('post_type' => 'attachment', 'post_status' => 'inherit', 'category_name' => $this->category, 'numberposts' => -1 );
        //if tag is added, add to query arguments
        if( $tag != null ) {
            $args['tag_id'] = $tag;
        }
        $images = get_posts($args); 
        if ( $images ) {
            return $images;
        } else {
            // no posts found
            echo 'No images for this category!';
        }
    }
}



//specific img html output for gallery
function catgal_get_attachment_link($post_id, $tag = null) {
    //use wordpress functions to get our img info
    $img['src'] = wp_get_attachment_image_src($post_id, 'sign-gallery')[0];
    $img['title'] = get_the_title($post_id);
    $img['alt'] = get_post_meta($post_id, '_wp_attachment_image_alt', true);
    $img['uri'] = get_permalink($post_id);
    $img['class'] = 'gallery-thumb';
    
    //if tag is in arg, then add it to the uri
    if($tag) {
        $img['uri'] .= '?tag=' . $tag;
    }
    
    //assemble img html
    $html = '<a href="' . $img['uri'] . '">';
    $html .= '<div class="image-info-overlay d-flex justify-content-center align-items-center"><h3>'. $img['title']. '</h3></div>';
    $html .= '<img class="' . $img['class'] .'" src="' . $img['src'] . '" ';
    $html .= '"alt="' . $img['alt'] . '">';
    $html .= '</a>';
    
    //output img html to frontend
    echo $html;
}



function get_all_tags_for_posts($posts) {
    $all_tags = [];
    foreach($posts as $post) {
        $post_tags = get_the_tags($post);

        if ($post_tags) {
            foreach($post_tags as $post_tag) {
                $tag = $post_tag->term_id;
                array_push($all_tags, $tag);
            }
        }
    }
    $all_tags = array_unique($all_tags);
    return $all_tags;
}

//checks if tag is in GET and either returns the tag value or returns false;
function the_selected_tag() {
    global $_GET;
    if ( isset($_GET['tag']) ) {
        $tag = $_GET['tag'];
        return $tag;
    } else {
        $tag = null;
        return false;
    }
}


//returns the current selected image info in our image lightbox
function get_current_img($id) {
    $current_img = [];
    $current_img['src'] = wp_get_attachment_image_src($id, 'original')[0];
    $current_img['alt'] = get_post_meta($id, '_wp_attachment_image_alt', true);
    return $current_img;
}

function get_order_of_image($images, $id) {
    for($i = 0; $i < count($images); $i++) {
        if($images[$i]->ID == $id) {
            $current_img_order = $i;
            return $current_img_order;
        }
    }
}

function append_tag_to_query($link, $tag) {
    $query = '/?tag=' . $tag;
    $newLink = $link . $query;
    return $newLink;
}

function the_next_image($images, $current_order, $tag = null) {
    if( $current_order < count($images) - 1) {
        $nextImg['image'] = $images[($current_order + 1)];
        $link = get_permalink($nextImg['image']);
        if ($tag) {
            $link .= "?tag=" . $tag;
        }
        $nextImg['link'] = $link;
    } else {
        $nextImg = null;
    }
    return $nextImg;
}

function the_previous_image($images, $current_order, $tag = null) {
        if( $current_order > 0 ) {
            $previousImg['image'] = $images[($current_order - 1)];
            $link = get_permalink($previousImg['image']);
            if ($tag) {
                $link .= "?tag=" . $tag;
            }
            $previousImg['link'] = $link;
        } else {
            $previousImg = null;
        }
    return $previousImg;
}


function setup_lightbox_images($id) {
        $category = get_the_category()[0];
        $the_selected_tag = the_selected_tag();
        $images = get_gallery_images( $category->slug, the_selected_tag() );
        $current_image = get_current_img($id);
        $current_order = get_order_of_image($images, $id);
        $next_image = the_next_image($images, $current_order, $the_selected_tag);
        $previous_image = the_previous_image($images, $current_order, $the_selected_tag);
        $data = ['id' => $id,
              'category' => $category,
              'tags' => get_the_tags(),
              'the_selected_tag' => $the_selected_tag,
              'current_image' => $current_image,
              'next_image' => $next_image,
              'previous_image' => $previous_image
             ];
        return $data;
}
add_filter('filter_images', 'setup_lightbox_images');