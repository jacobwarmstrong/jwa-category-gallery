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
    public $category;
    private $tags= [];
    private $images = [];
    private $htmlImages = [];
    private $link = 'what';
    
    function __construct() {
        add_shortcode('category-gallery', array($this, 'return_htmlImages') );
    }
    
    public function return_htmlImages($atts) {
            //if category is specified in shortcode, then use it
            if(!empty($atts)){
                $category = $atts['category'];
            //else grab all sign-product categories
            } else {
                $category = '3d-cnc-routed-signs,vehicle-wraps,channel-letter-signs,dimensional-letters,informational-signs,monument-signs,police-emergency-vehicles,label-decals,wayfinding-signs';
            }
            $this->images = $this->get_gallery_images($category);
            if(!empty($images)) {
                $tags = get_all_tags_for_posts($images);
            }
            if(!empty($this->images)) {
                foreach ($this->images as $image) {
                    $id = $image->ID;
                    array_push($this->htmlImages, $this->get_attachment_link($id) );
                }
            }
            $thestuff = '<div class="charmer-gallery row">';
                foreach ($this->htmlImages as $html) {
                 $thestuff .= $html;
            }
            $thestuff .= '</div>';
               return $thestuff;
    }
    
    public function get_gallery_images($category) {
        //create args based on tag input or not
        $args = array('post_type' => 'attachment', 'post_status' => 'inherit', 'category_name' => $category, 'numberposts' => -1, 'orderby' => 'rand');
        //if tag is added, add to query arguments
        $images = get_posts($args); 
        if ( $images ) {
            return $images;
        } else {
            // no posts found
            echo 'No images for this category!';
        }
    }
    
    //specific img html output for gallery
    public function get_attachment_link($post_id, $tag = null) {
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
        $html = '<div class="col-md-4 image-boundaries">';
        $html .= '<a href="' . $img['uri'] . '">';
        $html .= '<div class="image-info-overlay d-flex justify-content-center align-items-center"><h3>'. $img['title']. '</h3></div>';
        $html .= '<img class="' . $img['class'] .'" src="' . $img['src'] . '" ';
        $html .= '"alt="' . $img['alt'] . '">';
        $html .= '</a>';
        $html .= '</div>';

        //return html
        return $html;
    }
}

function catgal_get_images_setup() {
    new CategoryGallery();
}
add_action('setup_theme', 'catgal_get_images_setup');








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