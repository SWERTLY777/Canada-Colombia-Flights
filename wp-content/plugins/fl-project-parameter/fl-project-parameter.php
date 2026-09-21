<?php
/**
 * Plugin Name: Flatlogic URL Preserver
 * Description: Automatically attaches fl_project to all links on the page.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Function to fetch the ID once per request
function fl_get_env_project_id() {
    static $cached_id = null;
    if ($cached_id !== null) return $cached_id;

    $cached_id = getenv('PROJECT_ID');
    
    if (!$cached_id) {
        $envPath = ABSPATH . '../.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), 'PROJECT_ID=') === 0) {
                    $cached_id = trim(str_replace('PROJECT_ID=', '', $line), "\"' ");
                    break;
                }
            }
        }
    }
    return $cached_id;
}

// The core function to modify the URL
function fl_append_project_param( $url ) {
    $pid = fl_get_env_project_id();
    if ( ! $pid || empty( $url ) || strpos( $url, '#' ) === 0 ) {
        return $url;
    }

    // Only append if it's an internal link
    if ( strpos( $url, home_url() ) === 0 || ( strpos( $url, '/' ) === 0 && strpos( $url, '//' ) !== 0 ) ) {
        return add_query_arg( 'fl_project', $pid, $url );
    }

    return $url;
}

// Apply to all link filters
$filters = [
    'post_link', 'page_link', 'post_type_link', 
    'term_link', 'attachment_link', 
    'author_link', 'category_link', 'tag_link'
];

foreach ( $filters as $f ) {
    add_filter( $f, 'fl_append_project_param' );
}

// Special case: Navigation Menus
add_filter( 'nav_menu_link_attributes', function( $atts ) {
    if ( isset( $atts['href'] ) ) {
        $atts['href'] = fl_append_project_param( $atts['href'] );
    }
    return $atts;
}, 10, 1 );
