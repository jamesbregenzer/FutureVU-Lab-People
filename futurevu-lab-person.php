<?php
/*
 * Plugin Name: FutureVU Lab People
 * Plugin URI: https://vanderbilt.edu/web
 * Description: Create a custom post type which displays lab person/ som person
 * Version: 1.0
 * Author: Web Comm
 * Author URI: https://vanderbilt.edu/web
 * Updated: 2019-07-22
 */

add_action('init', 'create_lab_person');

function create_lab_person() {
    $labels = array (
        'name'  =>  _x('Lab People', 'post type general name'),
        'singular_name' =>   _x('Lab People', 'post type singular name'),
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Person',
        'edit' => 'Edit',
        'edit_item' => 'Edit Person',
        'new_item' => 'New Person',
        'view' => 'View',
        'view_item' => 'View Person',
        'search_items' => 'Search Person',
        'not_found' => 'No Person Found',
        'not_found_in_trash' => 'No Person found in Trash',
        'parent' => 'Parent Person',

    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_position' => 5,
        'supports' => array('title', 'thumbnail', 'custom-fields'),
        'taxonomies' => array('post_tag','category'),
        'menu_icon' => 'dashicons-welcome-learn-more',
        'has_archive' => true,
    );

    register_post_type( 'lab_people', $args);
}

/**
 * Force using template
 * Registers archive and single templates for a custom post type vu lab person.
*/

add_filter('template_include', function ($template) {
    if (is_singular('lab_people')) {
        $custom_template = plugin_dir_path(__FILE__) . 'single-lab_person.php';
        if (file_exists($custom_template)) return $custom_template;
    }

    if (is_post_type_archive('lab_people')) {
        $custom_archive = plugin_dir_path(__FILE__) . 'archive-lab_person.php';
        if (file_exists($custom_archive)) return $custom_archive;
    }

    return $template;
});


//add image size
add_image_size( 'img-300-200', 300, 200, true );
add_image_size( 'img-300-300', 300, 300, true );
add_image_size( 'img-150-150', 150, 150, true );
add_image_size( 'img-108-144-list', 108, 144, true );
add_image_size( 'img-142-190-grid', 142, 190, true );

//disable CURL ssl verify
//TODO: remember to delete this after finishing migration
add_action( 'http_api_curl', 'lab_person_curl_header', 10, 3 );

function lab_person_curl_header( $handle, $r, $url ) {
    curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );
}

//Add Shortcode to support display people by tag

add_shortcode('LabPeople', 'shortcode_display_lab_people_by_tag');

function shortcode_display_lab_people_by_tag( $atts)
{

    $shortcode_attributes = shortcode_atts(array(
        'tag' => '',
        'style' => 'list',
        'title' => 'show',
    ), $atts);

    wp_reset_query();

    //query people post by tags
    $args = array(
        'post_type' => 'lab_people',
        'depth' => 1,
        'posts_per_page' => -1,
        'post_status' => array('publish'),
        'meta_key' => 'last_family_name',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'tag_slug__in' => $shortcode_attributes['tag'],

    );

    $wp_query = new WP_Query($args);

    $shortcode_string = '';

    if ($wp_query->have_posts()) {
        if($shortcode_attributes['style'] == 'grid'){
	        if($shortcode_attributes['title'] == 'hide') {
		        
	        } else {
		        $shortcode_string = '<h2>' . $shortcode_attributes['tag'] . '</h2>';
	        }
            
            $shortcode_string .= '<div class="row">';
            while ($wp_query->have_posts()) {
                $wp_query->the_post();

                $shortcode_string .= '<div class="col-xs-6 col-sm-4 col-md-3 people-swatch">';

                $shortcode_string .= '<div class="people-photo">';
                $shortcode_string .= '<a href="' . get_the_permalink() . '">';
                if (has_post_thumbnail()) {
                    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), "img-142-190-grid" );
                    $shortcode_string .= '<img src="' . $thumbnail[0] . '" alt="image_thumb" />';
                } else {
                    $shortcode_string .= '<img src="' . get_stylesheet_directory_uri() . '/default-person-pic-150.png' . '"/>';
                }
                $shortcode_string .= '</a>';
                $shortcode_string .= '</div>';

                $shortcode_string .= '<div class="people-stats">';

                $shortcode_string .= '<strong class="people-name">';
                $shortcode_string .= '<a href="' . get_the_permalink() . '">';
                $shortcode_string .= get_field('first_given_name') . ' ' . get_field('middle_name_or_initial') . ' ' . get_field('last_family_name');

                if(get_field('suffix__credentials') && !empty(get_field('suffix__credentials'))) {
                    $shortcode_string .= ', ' . get_field('suffix__credentials');
                }

                $shortcode_string .= '</a>';
                $shortcode_string .= '</strong>';

                $shortcode_string .= '<div class="people-title">';

                if (have_rows('title_and_department')) {
                    while (have_rows('title_and_department')) {
                        the_row();

                        $have_title = 0;
                        $have_office = 0;

                        if (get_sub_field('title__position') !== '') {
                            $shortcode_string .= get_sub_field('title__position');
                            $have_title = 1;
                        }

                        if ($have_title && get_sub_field('department__center__office') !== ''){
                            $shortcode_string .= ', ' . get_sub_field('department__center__office') . '<br />';
                            $have_office = 1;
                        } elseif (get_sub_field('department__center__office') !== '') {
                            $shortcode_string .= get_sub_field('department__center__office') . '<br />';
                            $have_office = 1;
                        }

                        if ($have_title && !$have_office){
                            $shortcode_string .= '<br />';
                        }

                        if ($have_title || $have_office){
                            //we only want to show one title/office
                            break;
                        }

                    }
                }
                $shortcode_string .= '</div>';

                $shortcode_string .= '<div class="people-email">';
                if (get_field('email')) {
                    $shortcode_string .= '<a href="mailto:' . get_field('email') . '">' . '<i class="fa fa-envelope" aria-hidden="true"></i> '  . '</a>';
                }
                /*
                if (have_rows('phone_numbers')) {
                    while (have_rows('phone_numbers')) {
                        the_row();
                        if (get_sub_field('number')) {
                            $shortcode_string .= ' <a href="tel: ' . get_sub_field('number') . '">' . '<i class="fa fa-phone" aria-hidden="true"></i>: ' . get_sub_field('number') . '</a>';
                        }
                    }
                }
                */
                $shortcode_string .= '</div>';


                $shortcode_string .= '</div>'; //close of people-stats div

                $shortcode_string .= '</div>'; //close of people-swatch

            }

            $shortcode_string .= '</div>'; //close of row div
        } else {
            //default is list style
            //$shortcode_string = '<h2>' . $shortcode_attributes['tag'] . '</h2>';
            if($shortcode_attributes['title'] == 'hide') {
		        
	        } else {
		        $shortcode_string = '<h2>' . $shortcode_attributes['tag'] . '</h2>';
	        }

            while ($wp_query->have_posts()) {
                $wp_query->the_post();

                $shortcode_string = $shortcode_string . '<div class="media"> ';
                $shortcode_string .= '<div class="media-left">';
                $shortcode_string .= '<a href="' . get_the_permalink() . '">';
                if (has_post_thumbnail()) {
                    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), "img-108-144-list" );
                    $shortcode_string .= '<img src="' . $thumbnail[0] . '" alt="image_thumb" />';
                } else {
                    $shortcode_string .= '<img src="' . get_stylesheet_directory_uri() . '/default-person-pic-150.png' . '"/>';
                }
                $shortcode_string .= '</a>';
                $shortcode_string .= '</div>';

                $shortcode_string .= '<div class="media-body">';
                $shortcode_string .= '<h3 class="media-heading">';
                $shortcode_string .= '<a href="' . get_the_permalink() . '">';
                $shortcode_string .= get_field('first_given_name') . ' ' . get_field('middle_name_or_initial') . ' ' . get_field('last_family_name');

                if(get_field('suffix__credentials') && !empty(get_field('suffix__credentials'))) {
                    $shortcode_string .= ', ' . get_field('suffix__credentials');
                }

                $shortcode_string .= '</a>';
                $shortcode_string .= '</h3>';

                $shortcode_string .= '<p class="person-title">';

                if (have_rows('title_and_department')) {
                    while (have_rows('title_and_department')) {
                        the_row();

                        $shortcode_string .= get_sub_field('title__position');

                        if (get_sub_field('title__position') !== '' && get_sub_field('department__center__office') !== '') {
                            $shortcode_string .= ', ';
                        }

                        $shortcode_string .= get_sub_field('department__center__office');

                        $shortcode_string .= '<br />';
                    }
                }
                $shortcode_string .= '</p>';

                if (get_field('brief_description')) {
                    $shortcode_string .= '<p>';
                    $shortcode_string .= get_field('brief_description');
                    $shortcode_string .= '</p>';
                }

                $shortcode_string .= '<ul>';

                if(have_rows('address')){
                    $shortcode_string .= '<li>';
                    while(have_rows('address')){
                        the_row();
                        if(get_sub_field('building') || get_sub_field('street_address')) {
                            $shortcode_string .= '<p>';
                            $shortcode_string .= '<i class="fa fa-building-o" aria-hidden="true"></i>: ';

                            if(get_sub_field('room__suite') && !empty(get_sub_field('room__suite')) || get_sub_field('building') && !empty(get_sub_field('building'))) {
                                $shortcode_string .= get_sub_field('room__suite') . ' ' . get_sub_field('building') . '<br />';
                            }

                            if(get_sub_field('street_address')){
                                $shortcode_string .= get_sub_field('street_address') . '<br />';
                            }
                            if(get_sub_field('street_address_2')){
                                $shortcode_string .= get_sub_field('street_address_2') . '<br />';
                            }
                            if(get_sub_field('city')){
                                $shortcode_string .= get_sub_field('city') . ', ' . get_sub_field('state') . ' - ' . get_sub_field('zip');
                            }
                            $shortcode_string .= '</p>';
                        }
                    }

                    $shortcode_string .= '</li>';
                }

                if (get_field('email')) {
                    $shortcode_string .= '<li> <i class="fa fa-envelope" aria-hidden="true"></i>: <a href="mailto:' . get_field('email') . '"> ' . get_field('email') . '</a></li>';
                }
                if (have_rows('phone_numbers')) {
                    while (have_rows('phone_numbers')) {
                        the_row();
                        if (get_sub_field('number')) {
                            $shortcode_string .= '<li> <i class="fa fa-phone" aria-hidden="true"></i>: <a href="tel: ' . get_sub_field('number') . '"> ' . get_sub_field('number') . '</a></li>';
                        }
                    }
                }
                $shortcode_string .= '</ul>';

                $shortcode_string .= '</div>';
                $shortcode_string .= '<hr />';
                $shortcode_string .= '</div>';

            }
        }

        wp_reset_query();

    } else {
        $shortcode_string = '<p>There is no people with ' . $shortcode_attributes['tag'] . ' tag</p>';
    }

    return $shortcode_string;
}


