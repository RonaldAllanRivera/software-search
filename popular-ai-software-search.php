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

// 4. Add admin menu item and settings page
add_action('admin_menu', 'pais_add_admin_menu');
function pais_add_admin_menu() {
    add_menu_page(
        'Popular AI Search Settings',
        'Popular AI Search',
        'manage_options',
        'pais-settings',
        'pais_render_settings_page',
        'dashicons-search',
        80
    );
}

// 5. Register settings (use WP Settings API)
add_action('admin_init', 'pais_register_settings');
function pais_register_settings() {
    register_setting('pais_settings_group', 'pais_results_per_page', [
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'default' => 10
    ]);
    register_setting('pais_settings_group', 'pais_default_view', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'grid'
    ]);
    register_setting('pais_settings_group', 'pais_enable_ratings', [
        'type' => 'boolean',
        'sanitize_callback' => 'rest_sanitize_boolean',
        'default' => true
    ]);
    register_setting('pais_settings_group', 'pais_enable_comments', [
        'type' => 'boolean',
        'sanitize_callback' => 'rest_sanitize_boolean',
        'default' => true
    ]);
    register_setting('pais_settings_group', 'pais_stopwords', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'the,an,and,or,but,if,then,so,for,of,on,at,by,with,a'
    ]);
}

// 6. Render settings page
function pais_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Popular AI Software Search – Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('pais_settings_group'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="pais_results_per_page">Results Per Page</label></th>
                    <td>
                        <input type="number" id="pais_results_per_page" name="pais_results_per_page" value="<?php echo esc_attr(get_option('pais_results_per_page', 10)); ?>" min="1" max="100">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Default View</th>
                    <td>
                        <select name="pais_default_view" id="pais_default_view">
                            <option value="grid" <?php selected(get_option('pais_default_view', 'grid'), 'grid'); ?>>Grid</option>
                            <option value="list" <?php selected(get_option('pais_default_view', 'grid'), 'list'); ?>>List</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enable Star Ratings</th>
                    <td>
                        <input type="checkbox" name="pais_enable_ratings" value="1" <?php checked(get_option('pais_enable_ratings', 1), 1); ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enable Comments</th>
                    <td>
                        <input type="checkbox" name="pais_enable_comments" value="1" <?php checked(get_option('pais_enable_comments', 1), 1); ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="pais_stopwords">Stopwords for Autosuggest</label></th>
                    <td>
                        <input type="text" id="pais_stopwords" name="pais_stopwords" value="<?php echo esc_attr(get_option('pais_stopwords', 'the,an,and,or,but,if,then,so,for,of,on,at,by,with,a')); ?>" size="60">
                        <p class="description">Comma-separated list (these words will be ignored in autosuggest).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

