<?php
/**
 * Header template.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
    $header_logo = get_field('header_logo', 'option');
    $dark_logo = get_field('dark_logo', 'option');
    $button_text = get_field('button_text', 'option');
    $home_url = esc_url(home_url('/'));
    $site_name = esc_attr(get_bloginfo('name'));
    $has_dark_logo = !empty($dark_logo);
    $is_post_single = is_singular('post') || is_page_template('template/template-simple.php') || is_page_template('template/template-dl.php') || is_404();
?>

<div class="page-wrapper">
    <header class="site-header<?php echo $is_post_single ? ' site-header--post' : ''; ?>" role="banner">
        <div class="container site-header__inner">
            <div class="site-header__logo<?php echo $has_dark_logo ? ' site-header__logo--has-dark' : ''; ?>">
                <?php if (is_array($header_logo) && !empty($header_logo['url'])) : ?>
                    <a href="<?php echo $home_url; ?>" class="site-header__logo-link site-header__logo-link--light" aria-label="<?php echo $site_name; ?>">
                        <img src="<?php echo esc_url($header_logo['url']); ?>" alt="<?php echo esc_attr($header_logo['alt'] ?? $site_name); ?>">
                    </a>
                <?php elseif (is_string($header_logo) && filter_var($header_logo, FILTER_VALIDATE_URL)) : ?>
                    <a href="<?php echo $home_url; ?>" class="site-header__logo-link site-header__logo-link--light" aria-label="<?php echo $site_name; ?>">
                        <img src="<?php echo esc_url($header_logo); ?>" alt="<?php echo $site_name; ?>">
                    </a>
                <?php elseif (!empty($header_logo)) : ?>
                    <a href="<?php echo $home_url; ?>" class="site-header__logo-link site-header__logo-link--light" aria-label="<?php echo $site_name; ?>">
                        <?php echo wp_get_attachment_image($header_logo, 'full'); ?>
                    </a>
                <?php endif; ?>

                <?php if ($has_dark_logo) : ?>
                    <?php if (is_array($dark_logo) && !empty($dark_logo['url'])) : ?>
                        <a href="<?php echo $home_url; ?>" class="site-header__logo-link site-header__logo-link--dark" aria-label="<?php echo $site_name; ?>">
                            <img src="<?php echo esc_url($dark_logo['url']); ?>" alt="<?php echo esc_attr($dark_logo['alt'] ?? $site_name); ?>">
                        </a>
                    <?php elseif (is_string($dark_logo) && filter_var($dark_logo, FILTER_VALIDATE_URL)) : ?>
                        <a href="<?php echo $home_url; ?>" class="site-header__logo-link site-header__logo-link--dark" aria-label="<?php echo $site_name; ?>">
                            <img src="<?php echo esc_url($dark_logo); ?>" alt="<?php echo $site_name; ?>">
                        </a>
                    <?php else : ?>
                        <a href="<?php echo $home_url; ?>" class="site-header__logo-link site-header__logo-link--dark" aria-label="<?php echo $site_name; ?>">
                            <?php echo wp_get_attachment_image($dark_logo, 'full'); ?>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <button class="nav-toggle" type="button" aria-controls="site-header-menu-mobile" aria-expanded="false" aria-label="<?php esc_attr_e('Open menu', 'spendvest'); ?>">
                <span class="nav-toggle__line"></span>
                <span class="nav-toggle__line"></span>
                <span class="nav-toggle__line"></span>
            </button>
            <?php if (has_nav_menu('main')) : ?>
                <nav id="site-header-menu-desktop" class="site-header__nav nav-primary nav-primary--desktop" aria-label="<?php esc_attr_e('Main menu', 'spendvest'); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'main',
                        'menu_class'     => 'site-header__menu',
                        'container'      => false,
                    ));
                    ?>
                </nav>
            <?php endif; ?>
            <?php if ($button_text) : ?>
                <a href="#contact" class="site-header__cta">
                    <?php echo esc_html($button_text); ?>
                </a>
            <?php endif; ?>
        </div>
    </header>
    <?php if (has_nav_menu('main')) : ?>
        <nav id="site-header-menu-mobile" class="nav-primary nav-primary--mobile" aria-label="<?php esc_attr_e('Mobile menu', 'spendvest'); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'main',
                'menu_class'     => 'site-header__menu',
                'container'      => false,
            ));
            ?>
        </nav>
    <?php endif; ?>
