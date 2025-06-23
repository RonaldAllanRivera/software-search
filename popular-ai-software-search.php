<?php
/*
Plugin Name: Popular AI Software Search
Description: Advanced search plugin with Alpine.js, AJAX search, keyword autosuggest, and Elementor support.
Version: 0.1
Author: Ronald Allan Rivera
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// 1. Enqueue Alpine.js and CSS only on frontend where shortcode/widget is used
function pais_enqueue_scripts() {
    // Alpine.js (latest CDN, non-conflicting)
    wp_enqueue_script(
        'alpinejs',
        'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js',
        [],
        null,
        true
    );

    // Your plugin's CSS
    wp_enqueue_style(
        'pais-style',
        plugins_url( 'assets/css/pais-style.css', __FILE__ ),
        [],
        '0.1'
    );
}
add_action( 'wp_enqueue_scripts', 'pais_enqueue_scripts' );

// 2. Register Shortcode
function pais_render_search_shortcode( $atts ) {
    ob_start();
    ?>
    <div id="pais-search-root" x-data>
        <h2>Popular AI Software Search</h2>
        <div>
            <!-- Placeholder: You’ll replace this with the real search UI soon! -->
            <input type="text" placeholder="Type keyword..." disabled style="opacity:0.5;">
            <select disabled style="opacity:0.5;"><option>Category (coming soon)</option></select>
            <button disabled>Search</button>
        </div>
        <div>
            <em>Search UI coming soon...</em>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'popular_ai_software_search', 'pais_render_search_shortcode' );

// 3. Plugin Activation Hook (for later DB setup)
register_activation_hook( __FILE__, 'pais_on_activate' );
function pais_on_activate() {
    // Future: DB table setup or scheduled tasks
}
