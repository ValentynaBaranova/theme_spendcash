<?php

if (!defined('ABSPATH')) {
    exit;
}

define('SPENDVEST_VERSION', '1.0.0');
define('SPENDVEST_DIR', get_template_directory());
define('SPENDVEST_URI', get_template_directory_uri());

function spendvest_get_thank_you_url() {
    $thank_url = home_url('/thank-you/');
    $thank_page = get_page_by_path('thank-you', OBJECT, 'page');
    if ($thank_page instanceof WP_Post) {
        $thank_url = get_permalink($thank_page);
    }

    return apply_filters('spendvest_thank_you_url', $thank_url);
}

add_filter('upload_mimes', function ($mimes) {
    if (!current_user_can('manage_options')) {
        return $mimes;
    }

    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    if (!current_user_can('manage_options')) {
        return $data;
    }

    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (strtolower($ext) === 'svg') {
        $data['ext'] = 'svg';
        $data['type'] = 'image/svg+xml';
        $data['proper_filename'] = $filename;
    }

    return $data;
}, 10, 4);

function spendvest_meta_pixel_head() {
    ?>
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '2720462801648169');
    fbq('track', 'PageView');
    </script>
    <!-- End Meta Pixel Code -->
    <?php
}
add_action('wp_head', 'spendvest_meta_pixel_head', 20);

function spendvest_meta_pixel_noscript() {
    ?>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=2720462801648169&ev=PageView&noscript=1"
    alt=""
    /></noscript>
    <?php
}
add_action('wp_body_open', 'spendvest_meta_pixel_noscript', 20);

function spendvest_meta_pixel_lead() {
    if (!is_page_template('template/template-thank-you.php')) {
        return;
    }
    ?>
    <!-- Meta Pixel Lead Event -->
    <script>
    (function () {
        if (typeof fbq !== 'function') return;

        var eventID = '';
        try {
            eventID = sessionStorage.getItem('sv_event_id') || '';
        } catch (e) {}

        if (!eventID) {
            eventID = 'lead_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        fbq('track', 'Lead', {}, { eventID: eventID });

        try { sessionStorage.removeItem('sv_event_id'); } catch (e) {}
    })();
    </script>
    <!-- End Meta Pixel Lead Event -->
    <?php
}
add_action('wp_head', 'spendvest_meta_pixel_lead', 21);

function spendvest_scripts() {
    if (is_admin()) {
        return;
    }

    $style_file = SPENDVEST_DIR . '/assets/css/main.css';
    $anim_file = SPENDVEST_DIR . '/assets/css/animations.css';
    $media_file = SPENDVEST_DIR . '/assets/css/responsive.css';
    $script_file = SPENDVEST_DIR . '/assets/js/main.js';

    wp_enqueue_style(
        'spendvest-main',
        SPENDVEST_URI . '/assets/css/main.css',
        array(),
        file_exists($style_file) ? filemtime($style_file) : SPENDVEST_VERSION
    );

    wp_enqueue_style(
        'spendvest-animations',
        SPENDVEST_URI . '/assets/css/animations.css',
        array('spendvest-main'),
        file_exists($anim_file) ? filemtime($anim_file) : SPENDVEST_VERSION
    );

    wp_enqueue_style(
        'spendvest-media',
        SPENDVEST_URI . '/assets/css/responsive.css',
        array('spendvest-main', 'spendvest-animations'),
        file_exists($media_file) ? filemtime($media_file) : SPENDVEST_VERSION
    );

    wp_enqueue_script(
        'gsap',
        'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
        array(),
        '3.12.5',
        true
    );

    wp_enqueue_script(
        'gsap-scrolltrigger',
        'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js',
        array('gsap'),
        '3.12.5',
        true
    );

    wp_enqueue_script(
        'gsap-motionpath',
        'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/MotionPathPlugin.min.js',
        array('gsap'),
        '3.12.5',
        true
    );

    wp_enqueue_script(
        'spendvest-main',
        SPENDVEST_URI . '/assets/js/main.js',
        array('gsap', 'gsap-scrolltrigger', 'gsap-motionpath'),
        file_exists($script_file) ? filemtime($script_file) : SPENDVEST_VERSION,
        true
    );

    if (is_page_template('template/template-home.php')) {
        $calc_file = SPENDVEST_DIR . '/assets/js/calculator.js';

        wp_enqueue_script(
            'spendvest-calculator',
            SPENDVEST_URI . '/assets/js/calculator.js',
            array('spendvest-main'),
            file_exists($calc_file) ? filemtime($calc_file) : SPENDVEST_VERSION,
            true
        );
    }

    $thank_url = spendvest_get_thank_you_url();

    wp_localize_script(
        'spendvest-main',
        'spendvestTheme',
        array(
            'thankYouUrl' => $thank_url,
        )
    );
}
add_action('wp_enqueue_scripts', 'spendvest_scripts');

add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

add_action('after_setup_theme', function () {
    add_theme_support('menus');
    add_theme_support('post-thumbnails');
    add_post_type_support('post', 'thumbnail');
    add_post_type_support('page', 'thumbnail');

    register_nav_menus([
        'main' => 'Main Menu',
        'footer' => 'Footer Menu',
    ]);
});

add_action('admin_init', function () {
    $post_id = $_GET['post'] ?? $_POST['post_ID'] ?? null;

    if (!$post_id) return;

    $template = get_post_meta($post_id, '_wp_page_template', true);

    if ($template === 'template/template-home.php') {
        remove_post_type_support('page', 'editor');
    }
});

add_action('after_setup_theme', function(){
    add_theme_support('title-tag');
}, 5);


function spendvest_dl_is_likely_mobile_user_agent() {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }
    return (bool) preg_match(
        '/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',
        $_SERVER['HTTP_USER_AGENT']
    );
}


function spendvest_get_request_path_normalized() {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
    $path = wp_parse_url($request_uri, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        $path = '/';
    }

    $home_raw = wp_parse_url(home_url('/'), PHP_URL_PATH);
    $home_path = is_string($home_raw) ? '/' . trim($home_raw, '/') : '/';
    if ($home_path !== '/') {
        $path = preg_replace('#^' . preg_quote($home_path, '#') . '#', '', $path);
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }
    }

    return strtolower($path);
}


function spendvest_serve_well_known_doc($doc_basename) {
    $file = SPENDVEST_DIR . '/assets/doc/' . ltrim($doc_basename, '/');
    if (!is_readable($file)) {
        status_header(404);
        nocache_headers();
        exit;
    }

    $body = file_get_contents($file);
    if ($body === false || $body === '') {
        status_header(500);
        nocache_headers();
        exit;
    }

    json_decode($body);
    if (json_last_error() !== JSON_ERROR_NONE) {
        status_header(500);
        nocache_headers();
        exit;
    }

    status_header(200);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: public, max-age=3600');

    echo $body;
    exit;
}

add_action(
    'init',
    function () {
        if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }

        $path = spendvest_get_request_path_normalized();

        if ($path === '/.well-known/apple-app-site-association' || $path === '/.well-known/apple-app-site-association/') {
            spendvest_serve_well_known_doc('apple-app-site-association.json');
        }

        if ($path === '/.well-known/assetlinks.json' || $path === '/.well-known/assetlinks.json/') {
            spendvest_serve_well_known_doc('assetlinks.json');
        }
    },
    1
);

function spendvest_register_dl_rewrite_rules() {
    add_rewrite_rule('^dl/(.+)/?$', 'index.php?spendvest_dl=1&spendvest_dl_kind=dl&spendvest_dl_path=$matches[1]', 'top');
    add_rewrite_rule('^dl/?$', 'index.php?spendvest_dl=1&spendvest_dl_kind=dl&spendvest_dl_path=', 'top');
    add_rewrite_rule('^ref/(.+)/?$', 'index.php?spendvest_dl=1&spendvest_dl_kind=ref&spendvest_dl_path=$matches[1]', 'top');
    add_rewrite_rule('^ref/?$', 'index.php?spendvest_dl=1&spendvest_dl_kind=ref&spendvest_dl_path=', 'top');
}

add_action('init', function () {
    spendvest_register_dl_rewrite_rules();
}, 5);

add_filter('query_vars', function ($vars) {
    $vars[] = 'spendvest_dl';
    $vars[] = 'spendvest_dl_kind';
    $vars[] = 'spendvest_dl_path';
    return $vars;
});

add_filter('template_include', function ($template) {
    if ((int) get_query_var('spendvest_dl') !== 1) {
        return $template;
    }
    $dl_template = SPENDVEST_DIR . '/template/template-dl.php';
    return file_exists($dl_template) ? $dl_template : $template;
});

add_filter('body_class', function ($classes) {
    if ((int) get_query_var('spendvest_dl') === 1) {
        $classes[] = 'dl-landing-page';
    }
    return $classes;
});

add_action('after_switch_theme', function () {
    spendvest_register_dl_rewrite_rules();
    flush_rewrite_rules();
});


add_action('init', function () {
    if (get_option('spendvest_dl_rewrite_v3')) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('spendvest_dl_rewrite_v3', '1', true);
}, 999);

/*
add_filter('spendvest_dl_app_store_url', function () {
    return 'https://apps.apple.com/app/…';
});
add_filter('spendvest_dl_play_store_url', function () {
    return 'https://play.google.com/store/apps/details?id=…';
});
*/

add_action('wpcf7_mail_sent', 'spendvest_cf7_send_to_beehiiv');

function spendvest_cf7_send_to_beehiiv($contact_form) {
    try {
        if (!is_object($contact_form) || !method_exists($contact_form, 'id')) {
            return;
        }

        if ((int) $contact_form->id() !== 121) {
            return;
        }

        $beehiiv_api_key        = 'efdPnRzjvyozD9IEWt7xBzoXceGMsG8UdVyYKBlttkcSyhwilev7Q8XCq4MXENp9';
        $beehiiv_publication_id = 'pub_f8a602c2-3e6e-483c-a71c-cc797cd7a9ef';

        $submission = WPCF7_Submission::get_instance();
        if (!$submission) {
            return;
        }

        $data = $submission->get_posted_data();
        $email_field_name = 'email-759';
        $email = isset($data[$email_field_name]) ? sanitize_email($data[$email_field_name]) : '';

        if (empty($email) || !is_email($email)) {
            return;
        }

        $get_tracking_field = static function ($key) {
            if (!isset($_POST[$key])) {
                return '';
            }
            return sanitize_text_field(wp_unslash($_POST[$key]));
        };

        $fbc          = $get_tracking_field('sv_fbc');
        $fbp          = $get_tracking_field('sv_fbp');
        $utm_source   = $get_tracking_field('sv_utm_source');
        $utm_medium   = $get_tracking_field('sv_utm_medium');
        $utm_campaign = $get_tracking_field('sv_utm_campaign');
        $utm_content  = $get_tracking_field('sv_utm_content');
        $event_id     = $get_tracking_field('sv_event_id');

        $payload = array(
            'email'              => $email,
            'status'             => 'subscribed',
            'send_welcome_email' => true,
            'utm_source'         => $utm_source,
            'utm_medium'         => $utm_medium,
            'utm_campaign'       => $utm_campaign,
            'utm_content'        => $utm_content,
            'custom_fields'      => array(
                array('name' => 'fbc',      'value' => $fbc),
                array('name' => 'fbp',      'value' => $fbp),
                array('name' => 'event_id', 'value' => $event_id),
            ),
        );

        error_log('Beehiiv: request payload: ' . wp_json_encode($payload));

        $response = wp_remote_post(
            'https://api.beehiiv.com/v2/publications/' . $beehiiv_publication_id . '/subscriptions',
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $beehiiv_api_key,
                    'Content-Type'  => 'application/json',
                ),
                'body'      => wp_json_encode($payload),
                'timeout'   => 10,
                'sslverify' => true,
            )
        );

        if (is_wp_error($response)) {
            error_log('Beehiiv API error: ' . $response->get_error_message());
            return;
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code >= 300 && $status_code !== 409) {
            error_log(
                'Beehiiv API bad status (' . $status_code . '): ' .
                wp_remote_retrieve_body($response)
            );
        }
    } catch (Throwable $e) {
        error_log('Beehiiv sync fatal: ' . $e->getMessage());
    }
}