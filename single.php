<?php
/**
 * Single post template.
 */
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $theme_imgs = SPENDVEST_URI . '/assets/images/';
        $post_url = get_permalink();
        $post_title = get_the_title();
        $post_date = strtoupper(get_the_date('F j, Y'));

        $raw_post_content = get_post_field('post_content', get_the_ID());
        $word_count = str_word_count(wp_strip_all_tags($raw_post_content));
        if ($word_count <= 0) {
            $word_count = (int) preg_match_all('/[\p{L}\p{N}\']+/u', wp_strip_all_tags($raw_post_content), $matches);
        }
        $reading_time = max(1, (int) ceil($word_count / 200));

        $rendered_content = apply_filters('the_content', get_the_content());
        $toc_items = [];

        if (!empty($rendered_content) && class_exists('DOMDocument')) {
            libxml_use_internal_errors(true);
            $dom = new DOMDocument('1.0', 'UTF-8');
            $loaded = $dom->loadHTML('<?xml encoding="UTF-8">' . $rendered_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            if ($loaded) {
                $h2_nodes = [];
                foreach ($dom->getElementsByTagName('h2') as $h2) {
                    $h2_nodes[] = $h2;
                }

                $used_ids = [];
                foreach ($h2_nodes as $index => $h2) {
                    $heading_text = trim(wp_strip_all_tags($h2->textContent));
                    if ($heading_text === '') {
                        continue;
                    }

                    $base_id = sanitize_title($heading_text);
                    if ($base_id === '') {
                        $base_id = 'section-' . ($index + 1);
                    }

                    $final_id = $base_id;
                    $suffix = 2;
                    while (isset($used_ids[$final_id])) {
                        $final_id = $base_id . '-' . $suffix;
                        $suffix++;
                    }
                    $used_ids[$final_id] = true;

                    $h2->setAttribute('id', $final_id);
                    $toc_items[] = [
                        'id' => $final_id,
                        'title' => $heading_text,
                    ];
                }

                $rendered_content = $dom->saveHTML();
            }
            libxml_clear_errors();
        }

        $share_twitter = add_query_arg(
            [
                'url' => $post_url,
                'text' => $post_title,
            ],
            'https://twitter.com/intent/tweet'
        );
        $share_linkedin = add_query_arg(
            [
                'url' => $post_url,
            ],
            'https://www.linkedin.com/sharing/share-offsite/'
        );

        $current_post_id = get_the_ID();
        $current_categories = get_the_terms($current_post_id, 'category');
        $category_ids = [];
        if (!empty($current_categories) && !is_wp_error($current_categories)) {
            $category_ids = wp_list_pluck($current_categories, 'term_id');
        }

        $related_post_ids = [];
        if (!empty($category_ids)) {
            $related_post_ids = get_posts([
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 3,
                'post__not_in'        => [$current_post_id],
                'category__in'        => $category_ids,
                'ignore_sticky_posts' => true,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'fields'              => 'ids',
            ]);
        }

        if (count($related_post_ids) < 3) {
            $fallback_post_ids = get_posts([
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 3 - count($related_post_ids),
                'post__not_in'        => array_merge([$current_post_id], $related_post_ids),
                'ignore_sticky_posts' => true,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'fields'              => 'ids',
            ]);
            $related_post_ids = array_merge($related_post_ids, $fallback_post_ids);
        }

        $related_posts_query = null;
        if (!empty($related_post_ids)) {
            $related_posts_query = new WP_Query([
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 3,
                'post__in'       => $related_post_ids,
                'orderby'        => 'post__in',
            ]);
        }

        $posts_page_id = (int) get_option('page_for_posts');
        $blog_page_url = $posts_page_id ? get_permalink($posts_page_id) : '';
        if (empty($blog_page_url)) {
            $blog_page_url = get_post_type_archive_link('post');
        }
        if (empty($blog_page_url)) {
            $blog_page_url = home_url('/blog/');
        }

        $hero = get_field('hero', 'option');
        $more_articles_title = $hero['more_articles_title'] ?? '';
        $more_button_text = $hero['more_button_text'] ?? '';
        $more_button_link = $hero['more_button_link'] ?? null;
      
        ?>

        <main id="main" class="site__main single-post-page">
            <div class="container">
                <div class="single-post__meta-row anim-reveal">
                    <span><?php echo esc_html($post_date); ?></span>
                    <span aria-hidden="true">&#183;</span>
                    <span><?php echo esc_html(sprintf(__('%d min to read', 'spendvest'), $reading_time)); ?></span>
                </div>

                <h1 class="single-post__title anim-reveal"><?php echo esc_html($post_title); ?></h1>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="single-post__featured anim-reveal">
                        <?php the_post_thumbnail('full', ['class' => 'single-post__featured-image']); ?>
                    </div>
                <?php endif; ?>

                <div class="single-post__layout anim-reveal">
                    <aside class="single-post__sidebar">
                        <?php if (!empty($toc_items)) : ?>
                            <div class="single-post__toc">
                                <h2 class="single-post__toc-title"><?php esc_html_e('Table of contents', 'spendvest'); ?></h2>
                                <ul class="single-post__toc-list">
                                    <?php foreach ($toc_items as $item) : ?>
                                        <li class="single-post__toc-item">
                                            <a href="#<?php echo esc_attr($item['id']); ?>" class="single-post__toc-link"><?php echo esc_html($item['title']); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="single-post__share">
                            <h3 class="single-post__share-title"><?php esc_html_e('Share it:', 'spendvest'); ?></h3>
                            <div class="single-post__share-links">
                                <a href="<?php echo esc_url($share_twitter); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on X', 'spendvest'); ?>">
                                    <img src="<?php echo esc_url($theme_imgs . 'Twitter.svg'); ?>" alt="">
                                </a>
                                <a href="<?php echo esc_url($share_linkedin); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on LinkedIn', 'spendvest'); ?>">
                                    <img src="<?php echo esc_url($theme_imgs . 'linkedin.svg'); ?>" alt="">
                                </a>
                                <button
                                    type="button"
                                    class="single-post__share-web"
                                    data-web-share
                                    data-share-url="<?php echo esc_url($post_url); ?>"
                                    data-share-title="<?php echo esc_attr($post_title); ?>"
                                    aria-label="<?php esc_attr_e('Share this article', 'spendvest'); ?>"
                                >
                                    <img src="<?php echo esc_url($theme_imgs . 'instagram.svg'); ?>" alt="">
                                </button>
                            </div>
                        </div>
                    </aside>

                    <article class="single-post__content">
                        <?php echo $rendered_content; ?>
                    </article>
                </div>
            </div>

                <?php if ($related_posts_query && $related_posts_query->have_posts()) : ?>
                    <section class="single-post__more blog-list">
                        <div class="container">
                            <div class="single-post__more-head anim-reveal">
                                <h2 class="single-post__more-title">
                                    <?php echo esc_html($more_articles_title); ?>
                                </h2>
                                <?php
                                $more_link_url = $blog_page_url;
                                $more_link_target = '_self';
                                $more_link_text = !empty($more_button_text) ? $more_button_text : __('Read more', 'spendvest');

                                if (is_array($more_button_link) && !empty($more_button_link['url'])) {
                                    $more_link_url = $more_button_link['url'];
                                    $more_link_target = !empty($more_button_link['target']) ? $more_button_link['target'] : '_self';
                                    if (empty($more_button_text) && !empty($more_button_link['title'])) {
                                        $more_link_text = $more_button_link['title'];
                                    }
                                } elseif (is_string($more_button_link) && $more_button_link !== '') {
                                    $more_link_url = $more_button_link;
                                }
                                ?>
                                <a class="single-post__more-link" href="<?php echo esc_url($more_link_url); ?>"<?php echo $more_link_target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                    <?php echo esc_html($more_link_text); ?>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>

                            <div class="blog-grid">
                                <?php while ($related_posts_query->have_posts()) : $related_posts_query->the_post(); ?>
                                    <article class="blog-card anim-reveal">
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
                        </div>
                    </section>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>

        </main>
        <?php
    endwhile;
endif;

get_footer();
?>
