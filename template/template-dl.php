<?php
if (!defined('ABSPATH')) {
    exit;
}

$spendvest_dl_path = get_query_var('spendvest_dl_path');
$spendvest_dl_path = is_string($spendvest_dl_path) ? trim($spendvest_dl_path, '/') : '';

$spendvest_dl_kind_raw = get_query_var('spendvest_dl_kind');
$spendvest_dl_kind = ($spendvest_dl_kind_raw === 'ref') ? 'ref' : 'dl';

if (!spendvest_dl_is_likely_mobile_user_agent()) {
    wp_safe_redirect(home_url('/'));
    exit;
}

$spendvest_dl_open_app_url = $spendvest_dl_path === ''
    ? user_trailingslashit(home_url('/' . $spendvest_dl_kind))
    : user_trailingslashit(home_url('/' . $spendvest_dl_kind . '/' . $spendvest_dl_path));

$spendvest_dl_deep_link = $spendvest_dl_path === ''
    ? 'spendvest://' . $spendvest_dl_kind
    : 'spendvest://' . $spendvest_dl_kind . '/' . $spendvest_dl_path;
$spendvest_dl_deep_link = apply_filters('spendvest_dl_deep_link', $spendvest_dl_deep_link, $spendvest_dl_path, $spendvest_dl_kind);

$spendvest_dl_app_store = apply_filters('spendvest_dl_app_store_url', '');
$spendvest_dl_play_store = apply_filters('spendvest_dl_play_store_url', '');

$spendvest_dl_app_store_href = $spendvest_dl_app_store !== '' ? $spendvest_dl_app_store : '';
$spendvest_dl_play_store_href = $spendvest_dl_play_store !== '' ? $spendvest_dl_play_store : '';

get_header();
?>
<div
    id="dl-landing"
    class="page-content page-content--simple dl-landing"
    data-home-url="<?php echo esc_url(home_url('/')); ?>"
    data-open-app-url="<?php echo esc_attr($spendvest_dl_deep_link); ?>"
    data-universal-link-url="<?php echo esc_url($spendvest_dl_open_app_url); ?>"
    data-dl-kind="<?php echo esc_attr($spendvest_dl_kind); ?>"
    data-dl-path="<?php echo esc_attr($spendvest_dl_path); ?>"
    data-app-store-url="<?php echo esc_attr($spendvest_dl_app_store_href !== '' ? $spendvest_dl_app_store_href : '#'); ?>"
    data-play-store-url="<?php echo esc_attr($spendvest_dl_play_store_href !== '' ? $spendvest_dl_play_store_href : '#'); ?>"
>

<div class="header-wrapper"></div>
    <section class="simple-hero-wrapper">
        <div class="simple-hero__inner dl-landing__inner">            
            <p class="dl-landing__lead">
                <?php esc_html_e('Tap the button below to open this link in the app. If the app is not installed, use the store links.', 'spendvest'); ?>
            </p>
            <div class="dl-landing__actions">
                <a class="dl-landing__btn dl-landing__btn--primary dl-landing__open-app" href="<?php echo esc_attr($spendvest_dl_deep_link); ?>">
                    <?php esc_html_e('Open in app', 'spendvest'); ?>
                </a>
                <a
                    class="dl-landing__btn dl-landing__btn--ghost<?php echo $spendvest_dl_app_store === '' ? ' is-placeholder' : ''; ?>"
                    href="<?php echo $spendvest_dl_app_store_href !== '' ? esc_url($spendvest_dl_app_store_href) : '#'; ?>"
                    <?php echo $spendvest_dl_app_store === '' ? 'aria-disabled="true"' : ''; ?>
                    rel="noopener noreferrer"
                    data-dl-store="ios"
                >
                    <?php esc_html_e('Download on the App Store', 'spendvest'); ?>
                </a>
                <a
                    class="dl-landing__btn dl-landing__btn--ghost<?php echo $spendvest_dl_play_store === '' ? ' is-placeholder' : ''; ?>"
                    href="<?php echo $spendvest_dl_play_store_href !== '' ? esc_url($spendvest_dl_play_store_href) : '#'; ?>"
                    <?php echo $spendvest_dl_play_store === '' ? 'aria-disabled="true"' : ''; ?>
                    rel="noopener noreferrer"
                    data-dl-store="android"
                >
                    <?php esc_html_e('Get it on Google Play', 'spendvest'); ?>
                </a>
                <a class="dl-landing__btn dl-landing__btn--ghost dl-landing__home" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php esc_html_e('Back to website', 'spendvest'); ?>
                </a>
            </div>
            <p class="dl-landing__note">                
            </p>
        </div>
    </section>
</div>
<?php
get_footer();
