<?php
/**
 * 404 template.
 */
get_header();
?>

<main id="main" class="site__main site__main--404">
    <div class="container">
        <section class="error-404" aria-labelledby="error-404-title">
            <h1 id="error-404-title" class="error-404__title">404</h1>
            <p class="error-404__text"><?php esc_html_e('Looks like this page took a wrong turn.', 'spendvest'); ?></p>
            <p class="error-404__text"><?php esc_html_e('Let\'s get you back on track.', 'spendvest'); ?></p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="error-404__link"><?php esc_html_e('Back to home', 'spendvest'); ?></a>
        </section>
    </div>
</main>

<?php get_footer();
