<?php
error_log('Loaded: admin.php');
if (!defined('ABSPATH')) exit;

// 1. ADMIN SIDEBAR PAGE (already working)
add_action('admin_menu', function() {
    add_menu_page(
        'AI Software Search Admin',
        'AI Software Search',
        'manage_options',
        'pais_admin',
        'pais_admin_page',
        'dashicons-search',
        25
    );
});

function pais_admin_page() {
    echo '<div class="wrap"><h1>AI Software Search Admin Tools</h1>
    <p>This is where you will add reset ratings, clean comments, settings, etc.</p>';
    // Show the dashboard stats here too!
    pais_render_dashboard_widget();
    echo '</div>';
}

// 2. DASHBOARD SUMMARY WIDGET
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'pais_dashboard_widget',
        'AI Software Search — Overview',
        'pais_render_dashboard_widget'
    );
});

function pais_render_dashboard_widget() {
    global $wpdb;
    $post_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'");
    $ratings_row = $wpdb->get_row("SELECT AVG(rating) as avg_rating, COUNT(*) as num_ratings FROM {$wpdb->prefix}pais_ratings");
    $avg_rating = $ratings_row && $ratings_row->num_ratings > 0 ? round($ratings_row->avg_rating, 2) : 0;
    $num_ratings = $ratings_row ? intval($ratings_row->num_ratings) : 0;
    $comment_count = $wpdb->get_var("
        SELECT COUNT(*) FROM {$wpdb->comments} 
        WHERE comment_post_ID IN (
            SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post'
        ) AND comment_approved = '1'
    ");

    echo '<ul style="line-height:1.6em;font-size:1.11em;margin:0;padding:0 0 0 1.4em;">';
    echo '<li><strong>Published Posts:</strong> ' . number_format($post_count) . '</li>';
    echo '<li><strong>Total Star Ratings:</strong> ' . number_format($num_ratings) . '</li>';
    echo '<li><strong>Average Star Rating:</strong> ' . ($num_ratings > 0 ? '<span style="color:#ffc107;font-weight:bold">' . $avg_rating . ' / 5</span>' : '—') . '</li>';
    echo '<li><strong>Approved Comments:</strong> ' . number_format($comment_count) . '</li>';
    echo '</ul>';
    echo '<hr style="margin:1.2em 0 0.7em 0;">';
    echo '<div style="font-size:1em"><strong>How to display the search UI:</strong><br>
        Use the shortcode <code>[popular_ai_software_search]</code> in any page or post, or add it to a template with <code>echo do_shortcode(\'[popular_ai_software_search]\');</code>
        </div>';
}
