<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Detect the language assigned to the current Casa Andina page.
 */
function casa_andina_current_language(): string
{
    $post = get_queried_object();
    if ($post instanceof WP_Post) {
        $stored = sanitize_key((string) get_post_meta($post->ID, '_casa_language', true));
        if (in_array($stored, ['en', 'fr', 'es'], true)) {
            return $stored;
        }

        $slug_map = [
            'fr' => 'fr', 'itineraires' => 'fr', 'offres' => 'fr', 'agence' => 'fr', 'contact-fr' => 'fr', 'merci' => 'fr',
            'es' => 'es', 'rutas' => 'es', 'experiencias' => 'es', 'agencia' => 'es', 'cotizacion' => 'es', 'gracias' => 'es',
        ];
        if (isset($slug_map[$post->post_name])) {
            return $slug_map[$post->post_name];
        }
    }

    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    if ($path === 'es' || str_starts_with($path, 'es/')) {
        return 'es';
    }
    if ($path === 'fr' || str_starts_with($path, 'fr/')) {
        return 'fr';
    }

    return 'en';
}

add_action('wp_enqueue_scripts', function () {
    $theme = wp_get_theme();
    $base_version = $theme->get('Version');
    $ui_file = get_stylesheet_directory() . '/assets/site.css';
    $ui_version = file_exists($ui_file) ? (string) filemtime($ui_file) : $base_version;

    wp_enqueue_style('voyage-colombia', get_stylesheet_uri(), [], $base_version);
    wp_enqueue_style('voyage-colombia-ui', get_stylesheet_directory_uri() . '/assets/site.css', [], $ui_version);
});

add_action('after_setup_theme', function () {
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('title-tag');
});

add_filter('body_class', function (array $classes): array {
    $lang = casa_andina_current_language();
    $classes[] = 'casa-lang-' . $lang;

    $post = get_queried_object();
    if ($post instanceof WP_Post && in_array($post->post_name, ['home', 'fr', 'es'], true)) {
        $classes[] = 'casa-language-home';
    }

    return $classes;
});

add_filter('language_attributes', function (string $output): string {
    $lang = casa_andina_current_language();
    $locale = ['en' => 'en-CA', 'fr' => 'fr-CA', 'es' => 'es-CO'][$lang] ?? 'en-CA';

    if (preg_match('/lang=("|\')[^"\']+("|\')/i', $output)) {
        return (string) preg_replace('/lang=("|\')[^"\']+("|\')/i', 'lang="' . esc_attr($locale) . '"', $output, 1);
    }

    return trim($output . ' lang="' . esc_attr($locale) . '"');
});

/**
 * Publish alternate-language URLs for the corresponding page group.
 */
add_action('wp_head', function (): void {
    if (!is_page()) {
        return;
    }

    $sets = [
        ['en' => 'home', 'fr' => 'fr', 'es' => 'es'],
        ['en' => 'routes', 'fr' => 'itineraires', 'es' => 'rutas'],
        ['en' => 'offers', 'fr' => 'offres', 'es' => 'experiencias'],
        ['en' => 'about', 'fr' => 'agence', 'es' => 'agencia'],
        ['en' => 'contact', 'fr' => 'contact-fr', 'es' => 'cotizacion'],
        ['en' => 'thank-you', 'fr' => 'merci', 'es' => 'gracias'],
    ];

    $current = get_queried_object();
    if (!$current instanceof WP_Post) {
        return;
    }

    foreach ($sets as $set) {
        if (!in_array($current->post_name, $set, true)) {
            continue;
        }

        foreach ($set as $lang => $slug) {
            $page = get_page_by_path($slug);
            if ($page instanceof WP_Post) {
                $hreflang = ['en' => 'en-CA', 'fr' => 'fr-CA', 'es' => 'es-CO'][$lang];
                printf("<link rel=\"alternate\" hreflang=\"%s\" href=\"%s\" />\n", esc_attr($hreflang), esc_url(get_permalink($page)));
                if ($lang === 'en') {
                    printf("<link rel=\"alternate\" hreflang=\"x-default\" href=\"%s\" />\n", esc_url(get_permalink($page)));
                }
            }
        }
        break;
    }
}, 2);
