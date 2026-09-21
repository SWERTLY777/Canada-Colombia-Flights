<?php
/**
 * Plugin Name: Flatlogic SEO Metadata
 * Description: Automatically adds SEO and Social Media (Open Graph, Twitter) metadata to the site.
 * Version: 1.0.0
 */

function fl_seo_metadata_head() {
    if (is_admin()) return;

    $projectDescription = $_SERVER['PROJECT_DESCRIPTION'] ?? '';
    $projectImageUrl = $_SERVER['PROJECT_IMAGE_URL'] ?? '';

    $siteName = get_bloginfo('name');
    $siteDescription = get_bloginfo('description');

    $metaDescription = !empty($projectDescription) ? $projectDescription : $siteDescription;
    $metaTitle = is_front_page() ? $siteName : get_the_title() . ' | ' . $siteName;

    // Determine the current URL more accurately
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $current_url = $protocol . $host . $_SERVER['REQUEST_URI'];
    ?>
    <?php if ($metaDescription): ?>
    <!-- Meta description -->
    <meta name="description" content="<?php echo esc_attr($metaDescription); ?>" />
    <!-- Open Graph meta tags -->
    <meta property="og:description" content="<?php echo esc_attr($metaDescription); ?>" />
    <!-- Twitter meta tags -->
    <meta property="twitter:description" content="<?php echo esc_attr($metaDescription); ?>" />
    <?php endif; ?>
    <meta property="og:title" content="<?php echo esc_attr($metaTitle); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo esc_url($current_url); ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr($siteName); ?>" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:title" content="<?php echo esc_attr($metaTitle); ?>" />
    <?php if ($projectImageUrl): ?>
    <!-- Open Graph image -->
    <meta property="og:image" content="<?php echo esc_url($projectImageUrl); ?>" />
    <!-- Twitter image -->
    <meta property="twitter:image" content="<?php echo esc_url($projectImageUrl); ?>" />
    <?php endif; ?>
    <?php
}
add_action('wp_head', 'fl_seo_metadata_head', 1);
