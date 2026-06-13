<?php
/*
Template Name: Home Page
*/
get_header();

$hero = get_field('hero');
$hero_title = $hero['title'] ?? '';
$hero_subtitle = $hero['subtitle'] ?? '';
$hero_bg = $hero['bg_image'] ?? null;
$hero_bg_url = '';
if ($hero_bg) {
    $hero_bg_url = is_array($hero_bg) ? ($hero_bg['url'] ?? '') : wp_get_attachment_image_url($hero_bg, 'full');
}

$works = get_field('works');
$works_title = $works['title'] ?? '';
$works_text = $works['text'] ?? '';
$works_disclaimer = $works['disclaimer'] ?? '';
$works_title_card_one = $works['title_card_one'] ?? '';
$works_bg_card_one = $works['bg_card_one'] ?? null;
$works_title_card_two = $works['title_card_two'] ?? '';
$works_bg_card_two = $works['bg_card_two'] ?? null;
$works_title_card_three = $works['title_card_three'] ?? '';
$works_bg_card_three = $works['bg_card_three'] ?? null;
$works_card_one_bg_url = '';
if (!empty($works_bg_card_one)) {
    $works_card_one_bg_url = is_array($works_bg_card_one) ? ($works_bg_card_one['url'] ?? '') : wp_get_attachment_image_url($works_bg_card_one, 'full');
}
$works_card_two_bg_url = '';
if (!empty($works_bg_card_two)) {
    $works_card_two_bg_url = is_array($works_bg_card_two) ? ($works_bg_card_two['url'] ?? '') : wp_get_attachment_image_url($works_bg_card_two, 'full');
}
$works_card_three_bg_url = '';
if (!empty($works_bg_card_three)) {
    $works_card_three_bg_url = is_array($works_bg_card_three) ? ($works_bg_card_three['url'] ?? '') : wp_get_attachment_image_url($works_bg_card_three, 'full');
}
$works_has_cards = !empty($works_title_card_one) || !empty($works_title_card_two) || !empty($works_title_card_three)
    || !empty($works_card_one_bg_url) || !empty($works_card_two_bg_url) || !empty($works_card_three_bg_url);
 
$investing = get_field('investing');
$investing_text= $investing['investing_text'] ?? '';
$white_text = $investing['white_text'] ?? '';
$investing_disclaimer = $investing['disclaimer'] ?? '';


$investing_video = $investing['video'] ?? null;
$investing_video_url = '';
$investing_video_mime = '';
$investing_video_ext = '';
if (!empty($investing_video)) {
    if (is_array($investing_video)) {
        $investing_video_url = $investing_video['url'] ?? '';
        $investing_video_mime = $investing_video['mime_type'] ?? '';
    } elseif (is_numeric($investing_video)) {
        $investing_video_url = wp_get_attachment_url((int) $investing_video) ?: '';
        $investing_video_mime = get_post_mime_type((int) $investing_video) ?: '';
    } elseif (is_string($investing_video)) {
        $investing_video_url = $investing_video;
        $filetype = wp_check_filetype($investing_video_url);
        $investing_video_mime = $filetype['type'] ?? '';
    }
}
if (!empty($investing_video_url)) {
    $investing_video_ext = strtolower(pathinfo(parse_url($investing_video_url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
}

$works_title_formatted = $works_title;

if (!empty($works_title)) {
    $works_title_formatted = preg_replace(
        '/^(?:<h2([^>]*)>)?(.*?)(<br\s*\/?>)/i',
        '<h2$1><span class="title-gradient">$2</span>$3',
        $works_title,
        1
    );
}

$steps = get_field('steps');
$step_one = $steps['step_one'] ?? [];
$step_two = $steps['step_two'] ?? [];
$step_three = $steps['step_three'] ?? [];
$step_four = $steps['step_four'] ?? [];
$step_five = $steps['step_five'] ?? [];
$steps_button_text = $steps['button_text'] ?? '';
$steps_button_link = $steps['button_link'] ?? null;

$step_one_label = $step_one['step_label'] ?? '';
$step_one_title = $step_one['step_title'] ?? '';
$step_one_text = $step_one['step_text'] ?? '';
$step_one_has_content = !empty($step_one_label) || !empty($step_one_title) || !empty($step_one_text);

$step_two_label = $step_two['step_label'] ?? '';
$step_two_title = $step_two['step_title'] ?? '';
$step_two_text = $step_two['step_text'] ?? '';
$step_two_has_content = !empty($step_two_label) || !empty($step_two_title) || !empty($step_two_text);

$step_three_label = $step_three['step_label'] ?? '';
$step_three_title = $step_three['step_title'] ?? '';
$step_three_text = $step_three['step_text'] ?? '';
$step_three_has_content = !empty($step_three_label) || !empty($step_three_title) || !empty($step_three_text);

$step_four_label = $step_four['step_label'] ?? '';
$step_four_title = $step_four['step_title'] ?? '';
$step_four_text = $step_four['step_text'] ?? '';
$step_four_disclaimer = $step_four['disclaimer'] ?? '';
$step_four_has_content = !empty($step_four_label) || !empty($step_four_title) || !empty($step_four_text);


$step_five_label = $step_five['step_label'] ?? '';
$step_five_title = $step_five['step_title'] ?? '';
$step_five_text = $step_five['step_text'] ?? '';
$step_five_image = $step_five['image'] ?? null;
$step_five_image_url = '';
if (!empty($step_five_image)) {
    $step_five_image_url = is_array($step_five_image) ? ($step_five_image['url'] ?? '') : wp_get_attachment_image_url($step_five_image, 'full');
}
$step_five_has_content = !empty($step_five_label) || !empty($step_five_title) || !empty($step_five_text)  || (!empty($steps_button_text) || !empty($steps_button_link));

$has_works_steps = !empty($step_one_has_content) || !empty($step_two_has_content) || !empty($step_three_has_content) || !empty($step_four_has_content) || !empty($step_five_has_content);

$pricing = get_field('pricing') ?: [];
$pricing_title = $pricing['title'] ?? '';
$pricing_text = $pricing['text'] ?? '';
$pricing_disclaimer = $pricing['disclaimer'] ?? '';
$pricing_free_title = $pricing['free_title'] ?? '';
$pricing_free_price = $pricing['free_price'] ?? '';
$pricing_free_period = $pricing['free_period'] ?? '';
$pricing_free_list = $pricing['free_list'] ?? [];
$pricing_premium_title = $pricing['premium_title'] ?? '';
$pricing_premium_price = $pricing['premium_price'] ?? '';
$pricing_premium_period = $pricing['premium_period'] ?? '';
$pricing_premium_changes = $pricing['premium_changes'] ?? '';
$pricing_premium_list = $pricing['premium_list'] ?? [];
$pricing_premium_bg = $pricing['premium_bg'] ?? null;
$pricing_premium_bg_url = '';
if (!empty($pricing_premium_bg)) {
    $pricing_premium_bg_url = is_array($pricing_premium_bg)
        ? ($pricing_premium_bg['url'] ?? '')
        : wp_get_attachment_image_url($pricing_premium_bg, 'full');
}
$pricing_has_free_card = !empty($pricing_free_title) || $pricing_free_price !== '' || !empty($pricing_free_period) || !empty($pricing_free_list);
$pricing_has_premium_card = !empty($pricing_premium_title) || $pricing_premium_price !== '' || !empty($pricing_premium_period) || !empty($pricing_premium_list) || !empty($pricing_premium_bg_url);
$pricing_has_section = !empty($pricing_title) || !empty($pricing_text) || !empty($pricing_disclaimer) || $pricing_has_free_card || $pricing_has_premium_card;

$faq = get_field('faq');
$faq_title = $faq['title'] ?? '';
$faq_text = $faq['text'] ?? '';
$faq_items = $faq['faqs'] ?? [];

$faq_title_formatted = $faq_title;
if (!empty($faq_title)) {
    $faq_title_formatted = preg_replace(
        '/<br\s*\/?>\s*(.*)$/is',
        '<br><span class="title-gradient">$1</span>',
        $faq_title,
        1
    );
}


$calc_title = get_field('title') ?? '';
$calc_disclaimer = get_field('calc_disclaimer') ?? '';
$calc_left = get_field('calc_left') ?: [];
$calc_right = get_field('calc_right') ?: [];
$calc_left_title = $calc_left['title'] ?? '';
$calc_left_description = $calc_left['description'] ?? '';
$calc_cta_text = $calc_left['cta_text'] ?? 'START WITH {pct} IN THE APP';
$calc_cta_link = $calc_left['cta_link'] ?? null;
$calc_cta_url = is_array($calc_cta_link) ? ($calc_cta_link['url'] ?? '') : '';
$calc_cta_target = is_array($calc_cta_link) && !empty($calc_cta_link['target']) ? $calc_cta_link['target'] : '';
$calc_cta_parts = explode('{pct}', $calc_cta_text, 2);
$calc_cta_has_pct = strpos($calc_cta_text, '{pct}') !== false;
$calc_right_title = $calc_right['title'] ?? '';
$calc_right_description = $calc_right['description'] ?? '';

$contact = get_field('contact');
$waitlist_title = $contact['waitlist_title'] ?? '';
$waitlist_icon = $contact['waitlist_icon_black'] ?? null;
$waitlist_text = $contact['waitlist_text'] ?? '';
$waitlist_icon_url = '';
$waitlist_icon_alt = '';
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

<?php
$theme_imgs = SPENDVEST_URI . '/assets/images/';
?>
<main id="main" class="site__main">
    <?php if (!empty($hero_title) || !empty($hero_subtitle) || !empty($hero_bg_url)) : ?>
        <section class="hero"<?php echo $hero_bg_url ? ' style="--hero-bg: url(' . esc_url($hero_bg_url) . ');"' : ''; ?>>
            <div class="container">
                <div class="hero__inner">
                    <?php if (!empty($hero_title)) : ?>
                        <h1 class="hero__title"><?php echo esc_html($hero_title); ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($hero_subtitle)) : ?>
                        <p class="hero__subtitle"><?php echo esc_html($hero_subtitle); ?></p>
                    <?php endif; ?>
                    <div class="contact__form hero__form" data-check-icon="<?php echo esc_url($theme_imgs . 'check.svg'); ?>" data-success-text="You’re on the list">
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
                    <div class="hero__phone-block hero__phone-block--bg-anim" style="--phone-bg: url(<?php echo esc_url($theme_imgs . 'iPhone%2017%20Pro.png'); ?>);" aria-hidden="true">
                        <div class="hero__phone-bg" aria-hidden="true"></div>
                        <div class="hero__phone-ui">
                            <div class="hero__phone-status">
                                <img src="<?php echo esc_url($theme_imgs . 'Time.svg'); ?>" alt="" class="time anim-fade-in-slow anim-delay-7">
                                    <div class="hero__phone-status-left">
                                        <img src="<?php echo esc_url($theme_imgs . 'Connection.svg'); ?>" alt="" class="anim-fade-in-slow anim-delay-8">
                                        <img src="<?php echo esc_url($theme_imgs . 'Wifi.svg'); ?>" alt="" class="anim-fade-in-slow anim-delay-9">
                                        <img src="<?php echo esc_url($theme_imgs . 'Battery.svg'); ?>" alt="" class="anim-fade-in-slow anim-delay-10">
                                    </div>
                                </div>
                                <div class="hero__phone-header">
                                    <div class="hero__phone-header-left">
                                        <img src="<?php echo esc_url($theme_imgs . 'Avatar.svg'); ?>" alt="" class="anim-rise-up-slow anim-delay-9">
                                        <span class="name anim-fade-in-slow anim-fade-in-slow anim-delay-10">Hello, Nick!</span>
                                    </div>
                                    <div class="hero__phone-header-right">
                                        <img src="<?php echo esc_url($theme_imgs . 'gift.svg'); ?>" alt="" class="anim-fade-in-slow anim-delay-11">
                                        <img src="<?php echo esc_url($theme_imgs . 'ring.svg'); ?>" alt="" class="anim-fade-in-slow anim-delay-12">
                                    </div>
                                </div>
                            <div class="hero__phone-card anim-cards-grow anim-delay-11">
                                <div class="hero__phone-card-info">
                                        <div class="hero__phone-card-label">Total Account Value</div>
                                        <div class="hero__phone-card-value" data-counter-start="3.270" data-counter-end="3.450" data-counter-duration="1600" data-counter-delay="2200">$3,270</div>
                                        <div class="hero__phone-card-badge">ROI +4.56% | $150.12</div>
                                </div>
                                <img src="<?php echo esc_url($theme_imgs . 'totalimage.svg'); ?>" alt="" class="hero__phone-card-image anim-rotate-180 anim-delay-11">
                            </div>
                            <div class="hero__phone-actions">
                                <img src="<?php echo esc_url($theme_imgs . 'Sell.svg'); ?>" alt="" class="anim-fade-in-up anim-delay-13">
                                <img src="<?php echo esc_url($theme_imgs . 'Withdraw_new.svg'); ?>" alt="" class="anim-fade-in-up anim-delay-14">
                                <img src="<?php echo esc_url($theme_imgs . 'Invests.svg'); ?>" alt="" class="anim-fade-in-up anim-delay-15">
                                <img src="<?php echo esc_url($theme_imgs . 'Refer.svg'); ?>" alt="" class="anim-fade-in-up anim-delay-16">
                            </div>
                            <div class="hero__phone-cards ">
                                <div class="hero__phone-card-item anim-fade-in-up anim-delay-16">
                                    <img src="<?php echo esc_url($theme_imgs . 'CashBalance.svg'); ?>" alt="">
                                </div>
                                <div class="hero__phone-card-item anim-fade-in-up anim-delay-16">
                                    <img src="<?php echo esc_url($theme_imgs . 'AllDividends.svg'); ?>" alt="">
                                </div>
                            </div>
                        </div>
                        <img src="<?php echo esc_url($theme_imgs . 'Withdraw.svg'); ?>" alt="" class="hero__phone-float anim-withdrawd-in anim-delay-2">
                        <img src="<?php echo esc_url($theme_imgs . 'Dividend.svg'); ?>" alt="" class="hero__phone-float anim-dividend-in anim-delay-3">
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>    

    <?php if (!empty($works_title) || !empty($works_text) || !empty($works_has_cards)) : ?>
        <section id="product" class="works anim-reveal">
            <div class="container">
                <div class="works__header">
                    <?php if (!empty($works_title)) : ?>
                        <div class="works__title"><?php echo wp_kses_post($works_title_formatted); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($works_text)) : ?>
                        <div class="works__text"><?php echo wp_kses_post($works_text); ?></div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($works_has_cards)) : ?>
                    <div class="works__cards">
                        <?php if (!empty($works_title_card_one) || !empty($works_card_one_bg_url)) : ?>
                            <div class="works__card anim-reveal works__card_one"<?php echo $works_card_one_bg_url ? ' style="background-image: url(' . esc_url($works_card_one_bg_url) . ');"' : ''; ?>>
                                <?php if (!empty($works_title_card_one)) : ?>
                                    <div class="works__card-title"><?php echo wp_kses_post($works_title_card_one); ?></div>
                                <?php endif; ?>
                                <div class="hero__phone-card">
                                    <div class="hero__phone-card-info">
                                    <div class="hero__phone-card-label">Total Account Value</div>
                                    <div class="hero__phone-card-info-content ">
                                        <div class="hero__phone-card-value" data-counter-start="3.270" data-counter-end="3.450" data-counter-duration="1600" data-counter-delay="500">$3,270</div>
                                        <div class="hero__phone-card-badge">ROI +4.56% | $150.12</div>
                                        </div>
                                    </div>
                                <img src="<?php echo esc_url($theme_imgs . 'totalimage.svg'); ?>" alt="" class="hero__phone-card-image anim-rotate-180">
                                </div>
                                <div class="hero__phone-cards">
                                    <div class="hero__phone-card-item">
                                    <img src="<?php echo esc_url($theme_imgs . 'CashBalance.svg'); ?>" alt="" class="">
                                    <span class="hero__phone-card-price anim-price-reveal">$15.20</span>
                                    </div>
                                    <div class="hero__phone-card-item">
                                    <img src="<?php echo esc_url($theme_imgs . 'AllDividends.svg'); ?>" alt="" class="">
                                    <span class="hero__phone-card-price anim-price-reveal">$3.00</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>



                        <?php if (!empty($works_title_card_two) || !empty($works_card_two_bg_url)) : ?>
                            <div class="works__card anim-reveal"<?php echo $works_card_two_bg_url ? ' style="background-image: url(' . esc_url($works_card_two_bg_url) . ');"' : ''; ?>>
                                <?php if (!empty($works_title_card_two)) : ?>
                                    <div class="works__card-title"><?php echo wp_kses_post($works_title_card_two); ?></div>
                                <?php endif; ?>
                                <div class="works__orbit anim-reveal  anim-on-scroll scroll-fade-in-up">
                                    <img class="works__orbit-arc works__orbit-arc--back" src="<?php echo esc_url($theme_imgs . 'Ellipse_forvard.png'); ?>" alt="">
                                    <div class="works__orbit-center">
                                        <img src="<?php echo esc_url($theme_imgs . 'green_figure.png'); ?>" alt="">
                                    </div>
                                    <svg class="works__orbit-svg" viewBox="0 0 320 220" aria-hidden="true">
                                        <path class="works__orbit-path-line" d="M160,60 A170,50 0 1,1 160,160 A170,50 0 1,1 160,60" transform="rotate(-20 160 110)" />
                                    </svg>
                                    <div class="works__orbit-path">
                                        <div class="works__orbit-icon works__orbit-icon--apple">
                                            <img src="<?php echo esc_url($theme_imgs . 'apple.png'); ?>" alt="">
                                        </div>
                                        <div class="works__orbit-icon works__orbit-icon--starbucks">
                                            <img src="<?php echo esc_url($theme_imgs . 'Starbucks.png'); ?>" alt="">
                                        </div>
                                        <div class="works__orbit-icon works__orbit-icon--nike">
                                            <img src="<?php echo esc_url($theme_imgs . 'nike.png'); ?>" alt="">
                                        </div>
                                    </div>
                                    <img class="works__orbit-arc works__orbit-arc--front" src="<?php echo esc_url($theme_imgs . 'Ellipse_facade.png'); ?>" alt="">
                                </div>
                            </div>
                        <?php endif; ?>



                        <?php if (!empty($works_title_card_three) || !empty($works_card_three_bg_url)) : ?>
                            <div class="works__card anim-reveal works__card--activity"<?php echo $works_card_three_bg_url ? ' style="background-image: url(' . esc_url($works_card_three_bg_url) . ');"' : ''; ?>>
                                <?php if (!empty($works_title_card_three)) : ?>
                                    <div class="works__card-title"><?php echo wp_kses_post($works_title_card_three); ?></div>
                                <?php endif; ?>
                                <div class="works__activity-phone">
                                    <div class="works__activity-ui">
                                        <div class="works__activity-top">
                                            <span class="works__activity-title">Activity</span>
                                            <div class="works__activity-icons">
                                                <img src="<?php echo esc_url($theme_imgs . 'gift.svg'); ?>" alt="">
                                                <img src="<?php echo esc_url($theme_imgs . 'ring.svg'); ?>" alt="">
                                            </div>
                                        </div>
                                        <div class="works__activity-bottom">
                                            <div class="works__activity-tabs">
                                                <span class="works__activity-tab is-active">Invests</span>
                                                <span class="works__activity-tab">All</span>
                                            </div>
                                            <div class="works__activity-portfolio-title">Portfolio</div>
                                        </div>
                                        <div class="works__activity-portfolio-row">
                                            <div class="works__activity-portfolio-track">
                                                <div class="works__activity-portfolio-item">
                                                    <img src="<?php echo esc_url($theme_imgs . 'apple_big.svg'); ?>" alt="" class="works__activity-portfolio-icon">
                                                    <div class="works__activity-portfolio-name">Apple</div>
                                                    <div class="works__activity-portfolio-price">$1.15</div>
                                                </div>
                                                <div class="works__activity-portfolio-item">
                                                    <img src="<?php echo esc_url($theme_imgs . 'X_big.svg'); ?>" alt="" class="works__activity-portfolio-icon">
                                                    <div class="works__activity-portfolio-name">DAL</div>
                                                    <div class="works__activity-portfolio-price">$2.02</div>
                                                </div>
                                                <div class="works__activity-portfolio-item">
                                                    <img src="<?php echo esc_url($theme_imgs . 'Figma_big.svg'); ?>" alt="" class="works__activity-portfolio-icon">
                                                    <div class="works__activity-portfolio-name">T-Mobile</div>
                                                    <div class="works__activity-portfolio-price">$3.65</div>
                                                </div>
                                                <div class="works__activity-portfolio-item">
                                                    <img src="<?php echo esc_url($theme_imgs . 'apple_big.svg'); ?>" alt="" class="works__activity-portfolio-icon">
                                                    <div class="works__activity-portfolio-name">Apple</div>
                                                    <div class="works__activity-portfolio-price">$0.47</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="works__activity-filters">
                                            <div class="works__activity-filter works__activity-filter--with-icon">
                                                <span>Amount $</span>
                                                <img src="<?php echo esc_url($theme_imgs . 'arrow_down.png'); ?>" alt="" class="works__activity-filter-icon">
                                            </div>
                                            <div class="works__activity-filter works__activity-filter--with-icon">
                                                <span>Status</span>
                                                <img src="<?php echo esc_url($theme_imgs . 'arrow_down.png'); ?>" alt="" class="works__activity-filter-icon">
                                            </div>
                                            <div class="works__activity-filter works__activity-filter--with-icon">
                                                <span>Type</span>
                                                <img src="<?php echo esc_url($theme_imgs . 'arrow_down.png'); ?>" alt="" class="works__activity-filter-icon">
                                            </div>
                                        </div>
                                        <div class="works__activity-date">Monday, 1 Dec 2025</div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($works_disclaimer)) : ?>
                    <div class="disclaimer"><?php echo wp_kses_post($works_disclaimer); ?></div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($investing_text) || !empty($white_text) || !empty($investing_video_url)) : ?>
        <section class="investing">
            <div class="container">
                <div class="investing__title anim-reveal">
                    <?php if (!empty($investing_text)) : ?>
                        <p class="investing__title-gradient"><?php echo wp_kses_post($investing_text); ?></p>
                    <?php endif; ?>
                </div>
                <div class="investing__inner">
                    <?php if (!empty($investing_video_url)) : ?>
                        <video class="investing__video" autoplay muted loop playsinline preload="metadata">
                            <?php
                            $should_print_type = !empty($investing_video_mime)
                                && $investing_video_mime !== 'video/quicktime'
                                && $investing_video_ext !== 'mov';
                            ?>
                            <source src="<?php echo esc_url($investing_video_url); ?>"<?php echo $should_print_type ? ' type="' . esc_attr($investing_video_mime) . '"' : ''; ?>>
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
                    <div class="investing__content">
                    </div>
                    <?php if (!empty($white_text)) : ?>
                        <div class="investing__badge"><?php echo wp_kses_post($white_text); ?></div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($investing_disclaimer)) : ?>
                    <div class="disclaimer"><?php echo wp_kses_post($investing_disclaimer); ?></div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>


    <?php if (!empty($has_works_steps)) : ?>
        <section id="how-it-works" class="works-steps">
            <div class="container">
                <div class="works-steps__list">
                    <?php if (!empty($step_one_has_content)) : ?>
                        <div class="works-steps__item works-steps__item--one">
                            <div class="works-steps__media works-steps__media--one" aria-hidden="true"></div>
                            <div class="anim-reveal">
                                <?php if (!empty($step_one_label)) : ?>
                                    <div class="works-steps__label"><?php echo esc_html($step_one_label); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_one_title)) : ?>
                                    <div class="works-steps__title"><?php echo wp_kses_post($step_one_title); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_one_text)) : ?>
                                    <div class="works-steps__text"><?php echo wp_kses_post($step_one_text); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="works-steps__flow anim-reveal" aria-hidden="true">
                                <img src="<?php echo esc_url($theme_imgs . 'cart.png'); ?>" alt="" class="works-steps__flow-cart">
                                <div class="works-steps__flow-logo">
                                    <img src="<?php echo esc_url($theme_imgs . 'Plaid_logo.png'); ?>" alt="" class="works-steps__flow-logo-img">
                                </div>
                                <div class="works-steps__flow-figure">
                                    <img src="<?php echo esc_url($theme_imgs . 'white_figure.png'); ?>" alt="" class="works-steps__flow-figure-icon anim-rotate-180-loop">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($step_two_has_content)) : ?>
                        <div class="works-steps__item works-steps__item--two">
                            <div class="anim-reveal">
                                <?php if (!empty($step_two_label)) : ?>
                                    <div class="works-steps__label"><?php echo esc_html($step_two_label); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_two_title)) : ?>
                                    <div class="works-steps__title"><?php echo wp_kses_post($step_two_title); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_two_text)) : ?>
                                    <div class="works-steps__text"><?php echo wp_kses_post($step_two_text); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="works-steps__step-two-animation-wrapper anim-reveal">
                                <div class="works-steps__step-two-animation">

                                    <div class="works-steps__step-two-title">Your investment volume</div>
                                    <div class="works-steps__step-two-wrapper">
                                        <div class="works-steps__step-two-control">
                                            <div class="works-steps__step-two-control-icon  works-steps__step-two-control--minus">
                                                <img src="<?php echo esc_url($theme_imgs . 'minus.png'); ?>" alt="" class="works-steps__step-two-control-img">
                                            </div>
                                            <div class="works-steps__step-two-control-text">Min 1%</div>
                                        </div>

                                        <div class="works-steps__step-two-counter">1%</div>

                                        <div class="works-steps__step-two-control">
                                            <div class="works-steps__step-two-control-icon  works-steps__step-two-control--plus">
                                                <img src="<?php echo esc_url($theme_imgs . 'plus.png'); ?>" alt="" class="works-steps__step-two-control-img">
                                            </div>
                                            <div class="works-steps__step-two-control-text">Max 25%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($step_three_has_content)) : ?>
                        <div class="works-steps__item works-steps__item--three">
                            <div class="works-steps__media works-steps__media--three" aria-hidden="true"></div>
                            <div class="anim-reveal">
                                <?php if (!empty($step_three_label)) : ?>
                                    <div class="works-steps__label"><?php echo esc_html($step_three_label); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_three_title)) : ?>
                                    <div class="works-steps__title"><?php echo wp_kses_post($step_three_title); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_three_text)) : ?>
                                    <div class="works-steps__text"><?php echo wp_kses_post($step_three_text); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="works-steps__three-orbit anim-reveal" aria-hidden="true">
                                <div class="works-steps__three-orbit-ellipse works-steps__three-orbit-ellipse--one"></div>
                                <div class="works-steps__three-orbit-ellipse works-steps__three-orbit-ellipse--two"></div>
                                <div class="works-steps__three-orbit-center">
                                    <img src="<?php echo esc_url($theme_imgs . 'Dollar_icon.svg'); ?>" alt="" class="works-steps__three-orbit-icon">
                                </div>
                                
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($step_four_has_content)) : ?>
                        <div class="works-steps__item works-steps__item--four">
                            <div class="works-steps__media works-steps__media--four" aria-hidden="true"></div>
                            <div class="anim-reveal">
                                <?php if (!empty($step_four_label)) : ?>
                                    <div class="works-steps__label"><?php echo esc_html($step_four_label); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_four_title)) : ?>
                                    <div class="works-steps__title"><?php echo wp_kses_post($step_four_title); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_four_text)) : ?>
                                    <div class="works-steps__text"><?php echo wp_kses_post($step_four_text); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="works-steps__four-anim anim-reveal" aria-hidden="true">
                                <div class="works-steps__four-anim-center">
                                    <div class="works-steps__four-anim-cards">
                                        <div class="works-steps__four-anim-cards-track">
                                            <img class="works-steps__four-anim-card" src="<?php echo esc_url($theme_imgs . 'Horizontal_card1.svg'); ?>" alt="">
                                            <img class="works-steps__four-anim-card" src="<?php echo esc_url($theme_imgs . 'Horizontal_card2.svg'); ?>" alt="">
                                            <img class="works-steps__four-anim-card" src="<?php echo esc_url($theme_imgs . 'Horizontal_card3.svg'); ?>" alt="">
                                            <img class="works-steps__four-anim-card" src="<?php echo esc_url($theme_imgs . 'Horizontal_card4.svg'); ?>" alt="">
                                            <img class="works-steps__four-anim-card" src="<?php echo esc_url($theme_imgs . 'Horizontal_card5.svg'); ?>" alt="">
                                            <img class="works-steps__four-anim-card" src="<?php echo esc_url($theme_imgs . 'Horizontal_card6.svg'); ?>" alt="">
                                        </div>
                                    </div>

                                    <div class="works-steps__four-anim-icon works-steps__four-anim-icon--windows icon-cw">
                                        <img src="<?php echo esc_url($theme_imgs . 'Icon-windows.svg'); ?>" alt="">
                                    </div>
                                    <div class="works-steps__four-anim-icon works-steps__four-anim-icon--amazon icon-cw icon-delay">
                                        <img src="<?php echo esc_url($theme_imgs . 'Icon-amazon.svg'); ?>" alt="">
                                    </div>
                                    <div class="works-steps__four-anim-icon works-steps__four-anim-icon--meta icon-ccw">
                                        <img src="<?php echo esc_url($theme_imgs . 'Icon-meta.svg'); ?>" alt="">
                                    </div>
                                    <div class="works-steps__four-anim-icon works-steps__four-anim-icon--google icon-ccw icon-delay">
                                        <img src="<?php echo esc_url($theme_imgs . 'Icon-google.svg'); ?>" alt="">
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($step_four_disclaimer)) : ?>
                                <div class="disclaimer anim-reveal"><?php echo wp_kses_post($step_four_disclaimer); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($step_five_has_content)) : ?>
                        <div class="works-steps__item works-steps__item--five">
                            <div class="works-steps__media works-steps__media--five" aria-hidden="true"></div>
                            <div class="anim-reveal">
                                <?php if (!empty($step_five_label)) : ?>
                                    <div class="works-steps__label"><?php echo esc_html($step_five_label); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_five_title)) : ?>
                                    <div class="works-steps__title"><?php echo wp_kses_post($step_five_title); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($step_five_text)) : ?>
                                    <div class="works-steps__text"><?php echo wp_kses_post($step_five_text); ?></div>
                                <?php endif; ?>

                                <?php if (!empty($steps_button_text) || !empty($steps_button_link)) : ?>
                                    <a class="works-steps__button" href="<?php echo esc_url($steps_button_link); ?>">
                                        <?php echo esc_html($steps_button_text); ?>
                                    </a>
                                <?php endif; ?>                            
                            </div>

                            <div class="works-steps__five-img anim-reveal" aria-hidden="true">
                                <img src="<?php echo esc_url($step_five_image_url); ?>" alt="" class="works-steps__five-img-img">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($pricing_has_section) : ?>
        <section id="pricing" class="pricing">
            <div class="container">
                <div class="pricing__inner">
                    <?php if (!empty($pricing_title) || !empty($pricing_text)) : ?>
                        <div class="pricing__intro anim-reveal">
                            <?php if (!empty($pricing_title)) : ?>
                                <h2 class="pricing__title"><?php echo esc_html($pricing_title); ?></h2>
                            <?php endif; ?>
                            <?php if (!empty($pricing_text)) : ?>
                                <div class="pricing__text"><?php echo wp_kses_post($pricing_text); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($pricing_has_free_card || $pricing_has_premium_card) : ?>
                        <div class="pricing__grid anim-reveal">
                            <?php if ($pricing_has_free_card) : ?>
                                <div class="pricing__card pricing__card--free">
                                    <?php if (!empty($pricing_free_title)) : ?>
                                        <h3 class="pricing__card-title"><?php echo esc_html($pricing_free_title); ?></h3>
                                    <?php endif; ?>
                                    <div class="pricing__price-row">
                                        <?php if ($pricing_free_price !== '' && $pricing_free_price !== null) : ?>
                                            <span class="pricing__price"><?php echo esc_html($pricing_free_price); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($pricing_free_period)) : ?>
                                            <span class="pricing__period"><?php echo esc_html($pricing_free_period); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($pricing_free_list)) : ?>
                                        <ul class="pricing__features">
                                            <?php foreach ($pricing_free_list as $row) : ?>
                                                <?php
                                                $line = isset($row['text']) ? $row['text'] : ($row['item'] ?? $row['line'] ?? '');
                                                $line = ($line === null || $line === '') ? '' : $line;
                                                $row_icon = $row['icon'] ?? null;
                                                $row_icon_url = '';
                                                if (!empty($row_icon)) {
                                                    if (is_array($row_icon)) {
                                                        $row_icon_url = $row_icon['url'] ?? '';
                                                    } elseif (is_numeric($row_icon)) {
                                                        $row_icon_url = (string) wp_get_attachment_image_url((int) $row_icon, 'full') ?: '';
                                                    } elseif (is_string($row_icon)) {
                                                        $row_icon_url = $row_icon;
                                                    }
                                                }
                                                if ($line === '' && $row_icon_url === '') {
                                                    continue;
                                                }
                                                ?>
                                                <li class="pricing__feature">
                                                    <?php if ($row_icon_url) : ?>
                                                        <span class="pricing__check pricing__check--custom" aria-hidden="true">
                                                            <img src="<?php echo esc_url($row_icon_url); ?>" alt="" width="24" height="24" decoding="async" loading="lazy" />
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($line !== '') : ?>
                                                        <span class="pricing__feature-text"><?php echo esc_html($line); ?></span>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($pricing_has_premium_card) : ?>
                                <div class="pricing__card pricing__card--premium"<?php echo $pricing_premium_bg_url ? ' style="--pricing-premium-bg: url(' . esc_url($pricing_premium_bg_url) . ');"' : ''; ?>>
                                    <div class="pricing__card-premium-inner">
                                        <?php if (!empty($pricing_premium_title)) : ?>
                                            <h3 class="pricing__card-title"><?php echo esc_html($pricing_premium_title); ?></h3>
                                        <?php endif; ?>
                                        <div class="pricing__price-row">
                                            <?php if ($pricing_premium_price !== '' && $pricing_premium_price !== null) : ?>
                                                <span class="pricing__price"><?php echo esc_html($pricing_premium_price); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($pricing_premium_period)) : ?>
                                                <span class="pricing__period"><?php echo esc_html($pricing_premium_period); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($pricing_premium_changes)) : ?>
                                                <span class="pricing__period"><?php echo esc_html($pricing_premium_changes); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($pricing_premium_list)) : ?>
                                            <ul class="pricing__features">
                                                <?php foreach ($pricing_premium_list as $row) : ?>
                                                    <?php
                                                    $line = isset($row['text']) ? $row['text'] : ($row['item'] ?? $row['line'] ?? '');
                                                    $line = ($line === null || $line === '') ? '' : $line;
                                                    $row_icon = $row['icon'] ?? null;
                                                    $row_icon_url = '';
                                                    if (!empty($row_icon)) {
                                                        if (is_array($row_icon)) {
                                                            $row_icon_url = $row_icon['url'] ?? '';
                                                        } elseif (is_numeric($row_icon)) {
                                                            $row_icon_url = (string) wp_get_attachment_image_url((int) $row_icon, 'full') ?: '';
                                                        } elseif (is_string($row_icon)) {
                                                            $row_icon_url = $row_icon;
                                                        }
                                                    }
                                                    if ($line === '' && $row_icon_url === '') {
                                                        continue;
                                                    }
                                                    ?>
                                                    <li class="pricing__feature">
                                                        <?php if ($row_icon_url) : ?>
                                                            <span class="pricing__check pricing__check--custom" aria-hidden="true">
                                                                <img src="<?php echo esc_url($row_icon_url); ?>" alt="" width="24" height="24" decoding="async" loading="lazy" />
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($line !== '') : ?>
                                                            <span class="pricing__feature-text"><?php echo esc_html($line); ?></span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pricing_disclaimer)) : ?>
                        <div class="disclaimer anim-reveal"><?php echo wp_kses_post($pricing_disclaimer); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($faq_title) || !empty($faq_text) || !empty($faq_items)) : ?>
        <section id="faq" class="faq">
            <div class="container faq__inner">
                <div class="faq__intro anim-reveal">
                    <?php if (!empty($faq_title)) : ?>
                        <div class="faq__title"><?php echo wp_kses_post($faq_title_formatted); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($faq_text)) : ?>
                        <div class="faq__text"><?php echo wp_kses_post($faq_text); ?></div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($faq_items)) : ?>
                    <div class="faq__list">
                        <?php foreach ($faq_items as $index => $item) : ?>
                            <?php
                            $question = $item['question'] ?? '';
                            $answer = $item['answer'] ?? '';
                            ?>
                            <div class="faq__item <?php echo $index === 0 ? ' is-open' : ''; ?>">
                                <button class="faq__question" type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <h3><?php echo esc_html($question); ?></h3>
                                    <span class="faq__icon" aria-hidden="true"><?php echo $index === 0 ? '−' : '+'; ?></span>
                                </button>
                                <div class="faq__answer">
                                    <div class="faq__answer-inner">
                                        <?php echo wp_kses_post($answer); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
	
    <section id="calculator" class="calculator">
        <div class="container">
            <?php if (!empty($calc_title)) : ?>
                <div class="calculator__heading anim-reveal">
                    <h2 class="pricing__title"><?php echo esc_html($calc_title); ?></h2>
                </div>
            <?php endif; ?>

            <div class="calculator__grid anim-reveal">
                <div class="calculator__panel calculator__panel--left">
                    <?php if (!empty($calc_left_title)) : ?>
                        <h3 class="calculator__panel-title"><?php echo esc_html($calc_left_title); ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($calc_left_description)) : ?>
                        <div class="calculator__panel-desc"><?php echo wp_kses_post($calc_left_description); ?></div>
                    <?php endif; ?>

                    <form class="calculator__form" action="#" novalidate>
                        <div class="calculator__fields-row">
                            <div class="calculator__field">
                                <label class="calculator__label" for="calc-income">Annual household income</label>
                                <select class="calculator__select" id="calc-income" data-calc-input="income">
                                    <option value="5">$0 – $19,999</option>
                                    <option value="25">$20,000 – $49,999</option>
                                    <option value="50">$50,000 – $99,999</option>
                                    <option value="75">$100,000 – $499,999</option>
                                    <option value="90">$500,000 – $999,999</option>
                                    <option value="100">More than $1,000,000</option>
                                </select>
                            </div>
                            <div class="calculator__field">
                                <label class="calculator__label" for="calc-liquid">Liquid assets</label>
                                <select class="calculator__select" id="calc-liquid" data-calc-input="liquid">
                                    <option value="10">$0 – $19,999</option>
                                    <option value="35" selected>$20,000 – $49,999</option>
                                    <option value="60">$50,000 – $99,999</option>
                                    <option value="80">$100,000 – $499,999</option>
                                    <option value="95">$500,000 – $999,999</option>
                                    <option value="100">More than $1,000,000</option>
                                </select>
                            </div>
                        </div>

                        <div class="calculator__fields-row">
                            <div class="calculator__field">
                                <label class="calculator__label" for="calc-horizon">Investment horizon</label>
                                <select class="calculator__select" id="calc-horizon" data-calc-input="horizon">
                                    <option value="0">Under 1 year</option>
                                    <option value="20">1–2 years</option>
                                    <option value="50" selected>3–5 years</option>
                                    <option value="80">6–10 years</option>
                                    <option value="100">Over 10 years</option>
                                </select>
                            </div>
                            <div class="calculator__field">
                                <label class="calculator__label" for="calc-quick-access">Quick access importance</label>
                                <select class="calculator__select" id="calc-quick-access" data-calc-input="quickAccess">
                                    <option value="0" selected>Very important</option>
                                    <option value="33">Important</option>
                                    <option value="66">Somewhat important</option>
                                    <option value="100">Does not matter</option>
                                </select>
                            </div>
                        </div>

                        <div class="calculator__field">
                            <label class="calculator__label" for="calc-objective">Investment objective</label>
                            <select class="calculator__select" id="calc-objective" data-calc-input="objective">
                                <option value="accumulate">Accumulate wealth</option>
                                <option value="preserve" selected>Preserve wealth</option>
                                <option value="retirement">Wholly fund retirement</option>
                                <option value="speculation">Market speculation</option>
                            </select>
                        </div>

                        <div class="calculator__field calculator__field--slider">
                            <label class="calculator__label" for="calc-years">Adjust projection years</label>
                            <div class="calculator__slider-row">
                                <input class="calculator__range" id="calc-years" type="range" min="1" max="30" step="1" value="10" data-calc-input="years">
                                <span class="calculator__badge" data-calc-badge="years">10 yrs</span>
                            </div>
                        </div>

                        <div class="calculator__field calculator__field--slider">
                            <label class="calculator__label" for="calc-return">Average annual return assumption</label>
                            <div class="calculator__slider-row">
                                <input class="calculator__range" id="calc-return" type="range" min="1" max="12" step="1" value="8" data-calc-input="annualReturn">
                                <span class="calculator__badge" data-calc-badge="annualReturn">8%</span>
                            </div>
                        </div>

                        <div class="calculator__field">
                            <label class="calculator__label" for="calc-spending">Estimated weekly card spending</label>
                            <input class="calculator__input" id="calc-spending" type="number" min="0" step="1" value="1500" data-calc-input="weeklySpending">
                        </div>

                        <p class="calculator__rec-note">Recommended starting point: <span data-calc-rec-pct>5%</span></p>

                        <?php if (!empty($calc_cta_text)) : ?>
                            <a class="calculator__cta" href="<?php echo esc_url($calc_cta_url ?: '#'); ?>"<?php echo $calc_cta_target ? ' target="' . esc_attr($calc_cta_target) . '"' : ''; ?><?php echo $calc_cta_target === '_blank' ? ' rel="noopener noreferrer"' : ''; ?>>
                                <?php if ($calc_cta_has_pct) : ?>
                                    <?php echo esc_html($calc_cta_parts[0]); ?><span data-calc-cta-pct>5%</span><?php echo esc_html($calc_cta_parts[1] ?? ''); ?>
                                <?php else : ?>
                                    <?php echo esc_html($calc_cta_text); ?>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="calculator__panel calculator__panel--right">
                    <?php if (!empty($calc_right_title)) : ?>
                        <h3 class="calculator__panel-title calculator__panel-title--light"><?php echo esc_html($calc_right_title); ?></h3>
                    <?php endif; ?>

                    <div class="calculator__future-value" data-calc-future-value>$0</div>

                    <p class="calculator__summary" data-calc-summary></p>

                    <div class="calculator__stats">
                        <div class="calculator__stat">
                            <span class="calculator__stat-label">Recommended</span>
                            <span class="calculator__stat-value" data-calc-stat-pct>5%</span>
                        </div>
                        <div class="calculator__stat">
                            <span class="calculator__stat-label">Weekly limit</span>
                            <span class="calculator__stat-value" data-calc-stat-limit>$25</span>
                        </div>
                        <div class="calculator__stat">
                            <span class="calculator__stat-label">Risk style</span>
                            <span class="calculator__stat-value" data-calc-stat-risk>Conservative</span>
                        </div>
                    </div>

                    <div class="calculator__pills">
                        <span class="calculator__pill">Invested: <span data-calc-invested>$0</span></span>
                        <span class="calculator__pill">Estimated growth: <span data-calc-growth>$0</span></span>
                    </div>

                    <div class="calculator__chart-wrap">
                        <div class="calculator__chart" data-calc-chart role="img" aria-label="Portfolio growth chart"></div>
                        <div class="calculator__legend">
                            <span class="calculator__legend-item">
                                <span class="calculator__legend-swatch calculator__legend-swatch--invested" aria-hidden="true"></span>
                                Invested
                            </span>
                            <span class="calculator__legend-item">
                                <span class="calculator__legend-swatch calculator__legend-swatch--growth" aria-hidden="true"></span>
                                Estimated return
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($calc_disclaimer)) : ?>
                <div class="disclaimer anim-reveal"><?php echo wp_kses_post($calc_disclaimer); ?></div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer();