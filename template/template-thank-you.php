<?php
/**
 * Template Name: Thank You
 *
 * @package Spendvest
 */

if (!defined('ABSPATH')) {
    exit;
}

$front_id = (int) get_option('page_on_front');
$hero_bg_url = '';
if ($front_id) {
    $hero = function_exists('get_field') ? (get_field('hero', $front_id) ?: []) : [];
    $hero_bg = $hero['bg_image'] ?? null;
    if ($hero_bg) {
        $hero_bg_url = is_array($hero_bg) ? ($hero_bg['url'] ?? '') : wp_get_attachment_image_url($hero_bg, 'full');
    }
}

$hero_attr = $hero_bg_url
    ? sprintf(' style="%s"', esc_attr('--hero-bg: url(' . esc_url($hero_bg_url) . ')'))
    : '';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <?php wp_head(); ?>
</head>
<body <?php body_class('thank-you-page'); ?>>
<?php wp_body_open(); ?>
<main id="main" class="site__main site__main--thank-you">
    <section class="hero thank-you-hero"<?php echo $hero_attr; ?>>
        <div class="container">
            <div class="hero__inner thank-you-hero__inner">
                <p class="thank-you-hero__main-title"><?php esc_html_e('Thank you', 'spendvest'); ?>  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/check.svg" alt="Thank you"></p>
                <p class="thank-you-hero__title"><?php esc_html_e('You’re on the list', 'spendvest'); ?></p>
                <p class="thank-you-hero__subtitle"><?php esc_html_e('Thank you for your message. It has been sent.', 'spendvest'); ?></p>
                <p class="thank-you-hero__note"><?php esc_html_e('We’ll email you with updates about Spendvest', 'spendvest'); ?></p>
                <a class="thank-you-hero__cta works-steps__button" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php esc_html_e('Back to home', 'spendvest'); ?>
                </a>
            </div>
        </div>
    </section>
</main>
<?php wp_footer(); ?>
</body>
</html>
