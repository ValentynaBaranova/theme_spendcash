<?php
/**
 * Posts page template.
 */
get_header();

$posts_page_id = (int) get_option('page_for_posts');
$hero = get_field('hero', 'option');

$hero_title = $hero['title'] ?? '';
$hero_subtitle = $hero['subtitle'] ?? '';
$hero_label = $hero['label'] ?? __('Blog', 'spendvest');
$hero_bg = $hero['bg_image'] ?? null;
$hero_bg_url = '';

if ($hero_bg) {
    $hero_bg_url = is_array($hero_bg) ? ($hero_bg['url'] ?? '') : wp_get_attachment_image_url($hero_bg, 'full');
}

$blog_categories = get_categories([
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

$active_category_slug = isset($_GET['category']) ? sanitize_title(wp_unslash((string) $_GET['category'])) : 'all';
if ($active_category_slug === '') {
    $active_category_slug = 'all';
}

$available_category_slugs = !empty($blog_categories) ? wp_list_pluck($blog_categories, 'slug') : [];
if ($active_category_slug !== 'all' && !in_array($active_category_slug, $available_category_slugs, true)) {
    $active_category_slug = 'all';
}

$paged = max(1, (int) get_query_var('paged'));

$posts_query_args = [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => (int) get_option('posts_per_page'),
    'paged'               => $paged,
    'ignore_sticky_posts' => true,
];

if ($active_category_slug !== 'all') {
    $posts_query_args['category_name'] = $active_category_slug;
}

$posts_query = new WP_Query($posts_query_args);
?>

<main id="main" class="site__main">
    <section class="hero hero--blog"<?php echo $hero_bg_url ? ' style="--hero-bg: url(' . esc_url($hero_bg_url) . ');"' : ''; ?>>
        <div class="container">
            <div class="hero__inner">
                <?php if (!empty($hero_label)) : ?>
                    <div class="hero__blog-label"><?php echo esc_html($hero_label); ?></div>
                <?php endif; ?>

                <?php if (!empty($hero_title)) : ?>
                    <h1 class="hero__title"><?php echo esc_html($hero_title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($hero_subtitle)) : ?>
                    <p class="hero__subtitle"><?php echo esc_html($hero_subtitle); ?></p>
                <?php endif; ?>

                <div class="hero__blog-filters" data-blog-filters data-blog-filter-mode="server">
                    <button type="button" class="hero__blog-filter<?php echo $active_category_slug === 'all' ? ' is-active' : ''; ?>" data-blog-filter="all">
                        <?php esc_html_e('All', 'spendvest'); ?>
                    </button>

                    <?php if (!empty($blog_categories)) : ?>
                        <?php foreach ($blog_categories as $category) : ?>
                            <button type="button" class="hero__blog-filter<?php echo $active_category_slug === $category->slug ? ' is-active' : ''; ?>" data-blog-filter="<?php echo esc_attr($category->slug); ?>">
                                <?php echo esc_html($category->name); ?>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="blog-list">
        <div class="container">
            <?php if ($posts_query->have_posts()) : ?>
                <div class="blog-grid" data-blog-posts>
                    <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                        <?php
                        $post_categories = get_the_terms(get_the_ID(), 'category');
                        $category_slugs = [];
                        if (!empty($post_categories) && !is_wp_error($post_categories)) {
                            foreach ($post_categories as $post_category) {
                                $category_slugs[] = $post_category->slug;
                            }
                        }
                        $data_categories = implode(' ', array_unique($category_slugs));
                        ?>
                        <article class="blog-card anim-reveal-opacity" data-blog-post data-blog-categories="<?php echo esc_attr($data_categories); ?>">
                            <a class="blog-card__image-link" href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', ['class' => 'blog-card__image']); ?>
                                <?php else : ?>
                                    <div class="blog-card__image blog-card__image--placeholder" aria-hidden="true"></div>
                                <?php endif; ?>
                            </a>

                            <div class="blog-card__content">
                                <div class="blog-card__top">
                                    <h3 class="blog-card__title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <p class="blog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
                                </div>
                                <time class="blog-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(strtoupper(get_the_date('F j, Y'))); ?>
                                </time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php
                $total_pages = (int) $posts_query->max_num_pages;
                $pagination_add_args = [];
                if ($active_category_slug !== 'all') {
                    $pagination_add_args['category'] = $active_category_slug;
                }
                $number_links = paginate_links([
                    'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                    'format'    => '?paged=%#%',
                    'current'   => $paged,
                    'total'     => $total_pages,
                    'mid_size'  => 2,
                    'end_size'  => 0,
                    'prev_next' => false,
                    'type'      => 'array',
                    'add_args'  => $pagination_add_args,
                ]);

                $pagination_links = [];

                if ($paged > 1) {
                    $prev_url = get_pagenum_link($paged - 1);
                    if (!empty($pagination_add_args)) {
                        $prev_url = add_query_arg($pagination_add_args, $prev_url);
                    }
                    $pagination_links[] = '<a class="page-numbers prev" href="' . esc_url($prev_url) . '">&#8592;</a>';
                } else {
                    $pagination_links[] = '<span class="page-numbers prev">&#8592;</span>';
                }

                if (!empty($number_links) && is_array($number_links)) {
                    $pagination_links = array_merge($pagination_links, $number_links);
                }

                if ($paged < $total_pages) {
                    $next_url = get_pagenum_link($paged + 1);
                    if (!empty($pagination_add_args)) {
                        $next_url = add_query_arg($pagination_add_args, $next_url);
                    }
                    $pagination_links[] = '<a class="page-numbers next" href="' . esc_url($next_url) . '">&#8594;</a>';
                } else {
                    $pagination_links[] = '<span class="page-numbers next">&#8594;</span>';
                }
                ?>
                <?php if ($total_pages > 1 && !empty($pagination_links)) : ?>
                    <nav class="blog-pagination anim-reveal-opacity" aria-label="<?php esc_attr_e('Posts', 'spendvest'); ?>">
                        <ul class="blog-pagination__list">
                            <?php foreach ($pagination_links as $link) : ?>
                                <?php
                                $item_classes = 'blog-pagination__item';
                                if (strpos($link, 'prev') !== false) {
                                    $item_classes .= ' is-prev';
                                }
                                if (strpos($link, 'next') !== false) {
                                    $item_classes .= ' is-next';
                                }
                                ?>
                                <li class="<?php echo esc_attr($item_classes); ?>">
                                    <?php echo wp_kses_post($link); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

                <p class="blog-list__empty" data-blog-empty hidden><?php esc_html_e('No posts found in this category.', 'spendvest'); ?></p>
            <?php else : ?>
                <p class="blog-list__empty"><?php esc_html_e('No posts found.', 'spendvest'); ?></p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
wp_reset_postdata();
get_footer();
?>
