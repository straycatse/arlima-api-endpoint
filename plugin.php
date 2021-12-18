<?php

/**

 */
/*
Plugin Name: Arlima API Endpoint
Plugin URI: http://straycat.me
Description: Adds API Endpoint for Arlima Lists
Author: Stray Cat AB
Version: 0.1.0
Author URI: http://straycat.me
*/


function getArlimaPost($data)
{
    global $wpdb;
    $value = $wpdb->get_var($wpdb->prepare(
        " SELECT al_title FROM {$wpdb->prefix}arlima_articlelist WHERE al_id = '15' "
    ));

    $arlima_list = $wpdb->get_var($wpdb->prepare(
        " SELECT alv_id FROM {$wpdb->prefix}arlima_articlelist_version WHERE alv_al_id = %d ORDER BY alv_id DESC LIMIT 1 ",
        $data['id']
    ));

    $arlima_articles = $wpdb->get_results($wpdb->prepare(
        " SELECT * FROM {$wpdb->prefix}arlima_articlelist_article WHERE ala_alv_id = %d ",
        $arlima_list
    ));

    $arlimaPosts = [];

    foreach ($arlima_articles as $post) {
        $post->ala_options = unserialize($post->ala_options);
        $post->ala_image = unserialize($post->ala_image);
        $arlimaPosts[] = $post;
    }

    return $arlimaPosts;

}

add_action('rest_api_init', function () {
    register_rest_route('arlima/v1', '/list/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'getArlimaPost',
    ));
});