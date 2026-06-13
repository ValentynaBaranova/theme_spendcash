<?php
/**
 * Template Name: Simple Page
 */
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $page_info     = function_exists('get_field') ? get_field('page_info') : '';
        $last_updated  = function_exists('get_field') ? get_field('last_updated') : '';
        $summary_date  = function_exists('get_field') ? get_field('summary_date') : '';
        $date_parts    = array_filter([$last_updated, $summary_date]);
        $date_line     = implode(' ', $date_parts);
?>
<div class="page-content page-content--simple">
    <section class="simple-hero">
        <div class="simple-hero__inner">
            <?php if ($page_info) : ?>
                <p class="simple-hero__info"><?php echo esc_html($page_info); ?></p>
            <?php endif; ?>
            <h1 class="simple-hero__title"><?php the_title(); ?></h1>
            <?php if ($date_line) : ?>
                <p class="simple-hero__date"><?php echo esc_html($date_line); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <div class="container">
        <div class="simple-content">
            <div class="simple-content__body entry-content">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</div>
<?php
    endwhile;
endif;

get_footer();
