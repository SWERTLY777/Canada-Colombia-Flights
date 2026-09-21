<?php
/**
 * Plugin Name: Flatlogic Homepage Layout
 */

add_action("wp_enqueue_scripts", function () {
    wp_register_style("flatlogic-homepage-layout", false);
    wp_enqueue_style("flatlogic-homepage-layout");
    wp_add_inline_style("flatlogic-homepage-layout", <<<'CSS'
body.home .wp-block-post-title {
  display: none !important;
}

body.home .entry-content.alignfull.wp-block-post-content.has-global-padding.is-layout-constrained {
  width: min(1320px, calc(100vw - 3rem)) !important;
  max-width: min(1320px, calc(100vw - 3rem)) !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
  box-sizing: border-box !important;
}

body.home .entry-content.alignfull.wp-block-post-content.has-global-padding.is-layout-constrained > .docs-hero,
body.home .entry-content.alignfull.wp-block-post-content.has-global-padding.is-layout-constrained > .docs-section {
  width: 100% !important;
  max-width: none !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  box-sizing: border-box !important;
}

body.home .entry-content.alignfull.wp-block-post-content.has-global-padding.is-layout-constrained > .docs-hero {
  padding-left: clamp(1.5rem, 4vw, 4rem) !important;
  padding-right: clamp(1.5rem, 4vw, 4rem) !important;
}

body.home .docs-hero > * {
  max-width: 980px !important;
}

body.home .docs-hero h1,
body.home .docs-section h2,
body.home .docs-section h3 {
  max-width: none !important;
}

body.home .docs-hero p.is-style-lead,
body.home .docs-lead {
  max-width: 72ch !important;
}

body.home .fl-docs-card-grid,
body.home .fl-docs-article-grid,
body.home .fl-support-grid,
body.home .fl-docs-related__grid {
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
}
CSS
    );
}, 20);
