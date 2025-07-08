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
    // Get ALL matching posts from DB (no limit) for accurate PHP-side filtering
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 1000, // get all, or use a big enough number
        'paged'          => 1,
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


    $order_by = sanitize_text_field($request['orderby']);
    if ($order_by === 'comments') {
        $args['orderby'] = 'comment_count';
    } else {
        $args['orderby'] = in_array($order_by, ['date', 'title']) ? $order_by : 'date';
    }

    $q = new WP_Query($args);
    $posts = [];
    $keyword = trim(sanitize_text_field($request['keyword'] ?? ''));
    $pattern = $keyword !== '' ? '/\b' . preg_quote($keyword, '/') . '\b/i' : false;

    foreach ($q->posts as $post) {
        $title = get_the_title($post);
        $excerpt = get_the_excerpt($post);
        $rating = pais_get_rating_for_post($post->ID);

        // Only add if keyword matches as whole word (or no keyword)
        if (!$pattern || preg_match($pattern, $title . ' ' . $excerpt)) {
            $posts[] = [
                'ID'        => $post->ID,
                'title'     => $title,
                'excerpt'   => $excerpt,
                'permalink' => get_permalink($post),
                'category'  => get_the_category_list(', ', '', $post->ID),
                'date'      => get_the_date('', $post->ID),
                'comments'  => get_comments_number($post->ID),
                'rating' => $rating['avg'],
                'votes'  => $rating['count'],
            ];
        }
    }

    // Paginate the PHP-filtered posts
    $per_page = absint($request['per_page']) ?: 10;
    $page = absint($request['page']) ?: 1;
    $total = count($posts);
    $max_num_pages = max(1, ceil($total / $per_page));
    $offset = ($page - 1) * $per_page;
    $paged_posts = array_slice($posts, $offset, $per_page);

    return [
        'total'         => $total,
        'posts'         => $paged_posts,
        'max_num_pages' => $max_num_pages,
        'current_page'  => $page,
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


add_action('rest_api_init', function() {
    register_rest_route('popularai/v1', '/rate', array(
        'methods' => 'POST',
        'callback' => function($request) {
            global $wpdb;
            $post_id = intval($request->get_param('post_id'));
            $rating = intval($request->get_param('rating'));
            $ip = $_SERVER['REMOTE_ADDR'];

            if ($rating < 1 || $rating > 5 || !$post_id) {
                return new WP_Error('invalid', 'Invalid rating or post.', ['status' => 400]);
            }

            $table = $wpdb->prefix . 'pais_ratings';
            // Optionally: prevent duplicate voting by IP for this post
            $existing = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE post_id=%d AND ip=%s", $post_id, $ip));
            if ($existing) {
                return new WP_Error('duplicate', 'Already voted from this IP.', ['status' => 403]);
            }

            $wpdb->insert($table, [
                'post_id' => $post_id,
                'rating'  => $rating,
                'ip'      => $ip
            ]);
            return ['success' => true];
        },
        'permission_callback' => '__return_true'
    ));
});


function pais_get_rating_for_post($post_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'pais_ratings';
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count FROM $table WHERE post_id = %d",
        $post_id
    ), ARRAY_A);
    
    return [
        'avg' => $result ? round($result['avg_rating'], 1) : 0,
        'count' => $result ? (int)$result['rating_count'] : 0,
    ];
}

// Get categories with post count
function pais_get_categories_with_count() {
    $categories = get_categories([
        'hide_empty' => true, // Only show categories with posts
        'orderby' => 'count',
        'order' => 'DESC',
    ]);
    
    $result = [];
    foreach ($categories as $category) {
        $result[] = [
            'id' => $category->term_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'count' => $category->count,
        ];
    }
    
    return $result;
}

// Add categories with post count endpoint
add_action('rest_api_init', function() {
    register_rest_route('popularai/v1', '/categories', [
        'methods' => 'GET',
        'callback' => 'pais_get_categories_with_count',
        'permission_callback' => '__return_true',
    ]);
});

add_action('rest_api_init', function() {
    register_rest_route('popularai/v1', '/rating', array(
        'methods' => 'GET',
        'callback' => function($request) {
            $post_id = intval($request->get_param('post_id'));
            return pais_get_rating_for_post($post_id);
        },
        'permission_callback' => '__return_true'
    ));
});
