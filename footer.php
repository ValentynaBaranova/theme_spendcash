<?php
/**
 * Footer template.
 */
?>

<?php
    $footer_logo = get_field('footer_logo', 'option');
    $footer_copy = get_field('copyright', 'option');
    $footer_socials = get_field('socials', 'option');
    $theme_imgs = SPENDVEST_URI . '/assets/images/';
    $social_text = get_field('social_text', 'option');
    $email = get_field('email', 'option');
    $disclaimer = get_field('disclaimer', 'option');

    $posts_page_id = (int) get_option('page_for_posts');
    $front_page_id = (int) get_option('page_on_front');
    $contact = $front_page_id ? get_field('contact', $front_page_id) : [];
    if (empty($contact) && $posts_page_id) {
        $contact = get_field('contact', $posts_page_id);
    }
    if (empty($contact)) {
        $contact = get_field('contact');
    }


    $contact_title = $contact['title'] ?? '';
    $contact_text = $contact['text'] ?? '';
    $contact_bg = $contact['contact_bg'] ?? null;
    $waitlist_title = $contact['waitlist_title'] ?? '';
    $waitlist_icon = $contact['waitlist_icon'] ?? null;
    $waitlist_text = $contact['waitlist_text'] ?? '';
    $contact_bg_url = '';
    $waitlist_icon_url = '';
    $waitlist_icon_alt = '';
    if (!empty($contact_bg)) {
        $contact_bg_url = is_array($contact_bg) ? ($contact_bg['url'] ?? '') : wp_get_attachment_image_url($contact_bg, 'full');
    }
    if (!empty($waitlist_icon)) {
        if (is_array($waitlist_icon)) {
            $waitlist_icon_url = $waitlist_icon['url'] ?? '';
            $waitlist_icon_alt = $waitlist_icon['alt'] ?? '';
        } elseif (is_numeric($waitlist_icon)) {
            $waitlist_icon_url = wp_get_attachment_image_url((int) $waitlist_icon, 'full');
            $waitlist_icon_alt = get_post_meta((int) $waitlist_icon, '_wp_attachment_image_alt', true);
        } elseif (is_string($waitlist_icon)) {
            $waitlist_icon_url = $waitlist_icon;
        }
    }
?>
    <footer class="site-footer" role="contentinfo">
        
        <div class="site-footer__bg" aria-hidden="true"></div>
        <div class="site-footer__inner">
            <?php if (!empty($contact_title) || !empty($contact_text) || !empty($contact_bg_url)) : ?>
                <section id="contact" class="contact"<?php echo $contact_bg_url ? ' style="--contact-bg: url(' . esc_url($contact_bg_url) . ');"' : ''; ?>>
                    <div class="contact__inner">
                        <div class="contact__content anim-reveal">
                            <?php if (!empty($contact_title)) : ?>
                                <div class="contact__title"><?php echo wp_kses_post($contact_title); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($contact_text)) : ?>
                                <div class="contact__text"><?php echo wp_kses_post($contact_text); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="contact__form" data-check-icon="<?php echo esc_url($theme_imgs . 'check.svg'); ?>" data-success-text="You’re on the list">
                            <?php echo do_shortcode('[contact-form-7 id="cc293b6" title="Contact form"]'); ?>
                            <?php if (!empty($waitlist_title) || !empty($waitlist_icon_url)) : ?>
                                <div class="contact__waitlist">
                                    <div class="contact__waitlist-trigger">
                                        <?php if (!empty($waitlist_icon_url)) : ?>
                                            <img class="contact__waitlist-icon" src="<?php echo esc_url($waitlist_icon_url); ?>" alt="<?php echo esc_attr($waitlist_icon_alt); ?>">
                                        <?php endif; ?>
                                        <?php if (!empty($waitlist_title)) : ?>
                                            <span class="contact__waitlist-title"><?php echo esc_html($waitlist_title); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($waitlist_text)) : ?>
                                        <div class="contact__waitlist-tooltip">
                                            <?php echo wp_kses_post($waitlist_text); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <div class="container">
                <?php if (!empty($disclaimer)) : ?>
                    <div class="site-footer__disclaimer"><?php echo wp_kses_post($disclaimer); ?></div>
                <?php endif; ?>
                <div class="site-footer__row site-footer__row--top">
                    <?php if (has_nav_menu('footer')) : ?>
                        <nav class="site-footer__nav" aria-label="<?php esc_attr_e('Main menu', 'spendvest'); ?>">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'main',
                            'menu_class'     => 'site-header__menu',
                            'container'      => false,
                        ));
                        ?>
                        </nav>
                    <?php endif; ?>
                    <div class="site-footer__socials">
                        <?php if (!empty($social_text)) : ?>
                            <p class="site-footer__social_text"><?php echo esc_html($social_text); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($footer_socials) && is_array($footer_socials)) : ?>
                            <?php foreach ($footer_socials as $social) : ?>
                                <?php
                                $icon = $social['icon'] ?? null;
                                $link = $social['link'] ?? '';
                                if (empty($icon) || empty($link)) {
                                    continue;
                                }
                                ?>
                                <a href="<?php echo esc_url($link); ?>" class="site-footer__social" target="_blank" rel="noopener noreferrer">
                                    <?php if (is_array($icon) && !empty($icon['url'])) : ?>
                                        <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt'] ?? ''); ?>">
                                    <?php elseif (is_string($icon) && filter_var($icon, FILTER_VALIDATE_URL)) : ?>
                                        <img src="<?php echo esc_url($icon); ?>" alt="">
                                    <?php else : ?>
                                        <?php echo wp_get_attachment_image($icon, 'full'); ?>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($footer_logo)) : ?>
                    <img src="<?php echo esc_url($footer_logo['url']); ?>" alt="<?php echo esc_attr($footer_logo['alt'] ?? ''); ?>" class="site-footer__logo  anim-reveal-low">               
                <?php endif; ?>

    <div class="site-footer__bottom">
                    <?php if (!empty($footer_copy)) : ?>
                        <p class="site-footer__copy d-block"><?php echo esc_html($footer_copy); ?></p>
                    <?php endif; ?>
                    <?php if (has_nav_menu('footer')) : ?>
                        <nav class="site-footer__nav site-footer__nav--bottom" aria-label="<?php esc_attr_e('Footer menu', 'spendvest'); ?>">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer',
                                'menu_class'     => 'site-footer__menu',
                                'container'      => false,
                            ));
                            ?>
                        </nav>
                    <?php endif; ?>
                    <?php if (!empty($email)) : ?>
                        <p class="site-footer__email">
                            Contact: 
                            <a href="mailto:<?php echo antispambot($email); ?>">
                                <?php echo antispambot($email); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($footer_copy)) : ?>
                        <p class="site-footer__copy d-none"><?php echo esc_html($footer_copy); ?></p>
                    <?php endif; ?>
                </div>   
            </div>    
        </div>
    </footer>
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
