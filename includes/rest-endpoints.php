<?php
if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function() {

    // --- Search Endpoint ---
    register_rest_route('popularai/v1', '/search', [
        'methods' => 'GET',
        'callback' => 'pais_rest_search',
        'permission_callback' => '__return_true',
        'args' => [
            'keyword' => ['required' => false],
            'category' => ['required' => false],
            'orderby' => ['required' => false, 'default' => 'date'],
            'order'   => ['required' => false, 'default' => 'desc'],
            'page'    => ['required' => false, 'default' => 1],
            'per_page'=> ['required' => false, 'default' => 10],
        ],
    ]);

    // --- Autosuggest Endpoint ---
    register_rest_route('popularai/v1', '/autosuggest', [
        'methods' => 'GET',
        'callback' => 'pais_rest_autosuggest',
        'permission_callback' => '__return_true',
        'args' => [
            'q' => ['required' => false],
        ],
    ]);
});

// --- Search Callback ---
function pais_rest_search($request) {
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => absint($request['per_page']) ?: 10,
        'paged'          => absint($request['page']) ?: 1,
        'orderby'        => sanitize_text_field($request['orderby']),
        'order'          => sanitize_text_field($request['order']),
        'post_status'    => 'publish',
    ];
    if ($request['category']) {
        $args['category_name'] = sanitize_text_field($request['category']);
    }
    if ($request['keyword']) {
        $args['s'] = sanitize_text_field($request['keyword']);
    }

    $q = new WP_Query($args);
    $posts = [];
    $keyword = trim(sanitize_text_field($request['keyword'] ?? ''));
    $pattern = $keyword !== '' ? '/\b' . preg_quote($keyword, '/') . '\b/i' : false;

    foreach ($q->posts as $post) {
        $title = get_the_title($post);
        $excerpt = get_the_excerpt($post);

        // Only add if keyword matches as whole word (or no keyword)
        if (!$pattern || preg_match($pattern, $title . ' ' . $excerpt)) {
            $posts[] = [
                'ID'        => $post->ID,
                'title'     => $title,
                'excerpt'   => $excerpt,
                'permalink' => get_permalink($post),
                'category'  => get_the_category_list(', ', '', $post->ID),
                'date'      => get_the_date('', $post->ID),
            ];
        }
    }

    return [
        'total' => $q->found_posts,
        'posts' => $posts,
        'max_num_pages' => $q->max_num_pages,
        'current_page' => $args['paged'],
    ];
}


// --- Autosuggest Callback ---
function pais_rest_autosuggest($request) {
    $q = strtolower(sanitize_text_field($request['q']));
    $stopwords = pais_get_stopwords();
    global $wpdb;
    $sql = $wpdb->prepare("SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' LIMIT 3000");
    $results = $wpdb->get_results($sql);
    $keywords = [];
    foreach ($results as $row) {
        $words = preg_split('/\W+/', strtolower($row->post_title));
        foreach ($words as $word) {
            if (strlen($word) < 3) continue;
            if (in_array($word, $stopwords)) continue;
            if ($q && strpos($word, $q) !== 0) continue;
            $keywords[$word] = true;
        }
    }
    $keywords = array_keys($keywords);
    sort($keywords);
    return array_slice($keywords, 0, 10);
}

// --- Helper: Get stopwords as array ---
function pais_get_stopwords() {
    $raw = get_option('pais_stopwords', 'the,an,and,or,but,if,then,so,for,of,on,at,by,with,a');
    $stopwords = array_map('trim', explode(',', strtolower($raw)));
    return array_filter($stopwords);
}
