<?php
/*
Plugin Name: Popular AI Software Search
Description: AJAX-powered, vanilla JS WordPress plugin for advanced post search with autosuggest, grid/list view, and Elementor support.
Version: 0.1
Author: Ronald Allan Rivera
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Prevent direct access

// Load REST endpoints
add_action('plugins_loaded', function() {
    require_once __DIR__ . '/includes/rest-endpoints.php';
});


// 1. Enqueue Vanilla JS and CSS only where shortcode/widget is present
add_action('wp_enqueue_scripts', function() {
    if (is_singular() && has_shortcode(get_post(get_the_ID())->post_content, 'popular_ai_software_search')) {
        wp_enqueue_script(
            'pais-search-js',
            plugins_url('assets/js/search.js', __FILE__),
            [],
            '0.1',
            true
        );
        wp_enqueue_style(
            'pais-style',
            plugins_url('assets/css/styles.css', __FILE__),
            [],
            '0.1'
        );
        wp_localize_script('pais-search-js', 'pais_vars', [
        'rest_url' => esc_url_raw( get_rest_url() ),
        ]);
    }
});

// 2. Register Shortcode
function pais_render_search_shortcode($atts) {
    ob_start(); ?>
    <div id="pais-search-root">
        <div id="pais-search-form">
            <input type="text" id="pais-keyword" placeholder="Type keyword..." autocomplete="off">
            <select id="pais-category">
                <option value="">All Categories</option>
                <!-- More categories will be loaded here -->
            </select>
            <button id="pais-search-btn" type="button">Search</button>
        </div>
        <div id="pais-autosuggest" style="position:relative;"></div>
        <div id="pais-results"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('popular_ai_software_search', 'pais_render_search_shortcode');
