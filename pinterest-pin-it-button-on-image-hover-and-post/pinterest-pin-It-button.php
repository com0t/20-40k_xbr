<?php
/**
 * Plugin Name: Weblizar Pin It Button On Image Hover And Post
 * Version: 3.0
 * Description: Weblizar pin it button on image hover plugin provides facility to pins your blog posts, pages and images into your Pinterest account boards.
 * Author: Weblizar
 * Author URI: https://weblizar.com/plugins/
 * Plugin URI: https://wordpress.org/plugins/pinterest-pin-it-button-on-image-hover-and-post/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 */

/**
 * Constant Values & Variables
 */
define("WEBLIZAR_PINIT_PLUGIN_URL", plugin_dir_url(__FILE__));
define("WEBLIZAR_PINIT_TD", "weblizar_pinit");

/**
 * Get Ready Plugin Translation
 */
add_action('plugins_loaded', 'PINITTranslation');
function PINITTranslation()
{
    load_plugin_textdomain(WEBLIZAR_PINIT_TD, false, dirname(plugin_basename(__FILE__)).'/languages/');
}

/**
 * Default Setting
 */
register_activation_hook(__FILE__, 'PiniIt_DefaultSettings');
function PiniIt_DefaultSettings()
{
    add_option("WL_Enable_Pinit_Post", 1);
    add_option("WL_Enable_Pinit_Page", 1);
    add_option("WL_Pinit_Btn_On_Hover", "true");
    add_option("WL_Mobile_Status", 1);
    add_option("WL_Pinit_Btn_Color", "red");
    add_option("WL_Pinit_Btn_Design", "rectangle");
    add_option("WL_Pinit_Btn_Size", "small");
}

function front_jquery() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('wl-pin-main', WEBLIZAR_PINIT_PLUGIN_URL . 'js/main.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'front_jquery');

//Load saved pin it button settings
$PinItOnHover = get_option("WL_Pinit_Btn_On_Hover");

//Show Pin It Button On Image Hover
if ($PinItOnHover == "true") {
    // Add hook for frontend <head></head>
    add_action('wp_head', 'wl_pinit_js');
}
function wl_pinit_js()
{
    $PinItOnHover 	   = get_option("WL_Pinit_Btn_On_Hover");
    $PinItColor		   = get_option("WL_Pinit_Btn_Color");
    $PinItSize		   = get_option("WL_Pinit_Btn_Size");
    $PinItStatus 	   = get_option("WL_Mobile_Status");
    $all_exclude_pages = get_option('excluded_pint_it_pages', array());

    //don't show on mobile
    if (wp_is_mobile() && $PinItStatus == 0) {
        // do nothing - hide pinit button?>
        <script type="text/javascript" async defer
            data-pin-color="<?php echo esc_attr($PinItColor); ?>"
            <?php if ($PinItSize == "large") { ?>data-pin-height="28"
            <?php } ?> data-pin-hover="false"
            src="<?php echo WEBLIZAR_PINIT_PLUGIN_URL."js/pinit.js"; ?>">
        </script><?php
    }
    if (! empty($all_exclude_pages) && is_page($all_exclude_pages)) { ?>
        <script type="text/javascript" async defer
            data-pin-color="<?php echo esc_html($PinItColor); ?>"
            <?php if ($PinItSize == "large") { ?>data-pin-height="28"
            <?php } ?> data-pin-hover="false"
            src="<?php echo WEBLIZAR_PINIT_PLUGIN_URL."js/pinit.js"; ?>">
        </script><?php
    } else { ?>
        <script type="text/javascript" async defer
        data-pin-color="<?php echo esc_html($PinItColor); ?>"
        <?php if ($PinItSize == "large") { ?>data-pin-height="28" <?php } ?>
        data-pin-hover="<?php echo esc_html($PinItOnHover); ?>" src="<?php echo WEBLIZAR_PINIT_PLUGIN_URL."js/pinit.js"; ?>"></script><?php
    }

    // exclude images pin it hover
    $imags_urls = array();
    $imags_urls = get_option('exclude_pin_it_images', array());



    $js = '';
    $js .= 'jQuery(document).ready(function(){';
    $js .= 'jQuery(".is-cropped img").each(function(){';
    $js .= 'jQuery(this).attr("style", "min-height: 120px;min-width: 100px;");';
    $js .= '});';
    $js .= 'jQuery(".avatar").attr("style", "min-width: unset; min-height: unset;");';
    $js .= '});';


    if ((is_array($imags_urls)) && count($imags_urls)) {
        foreach ($imags_urls as $imags_url) {
            if ($imags_url) {
                $js .= 'jQuery(document).ready(function(){';
                $js .= 'var nopin_img_src = "'. esc_url($imags_url).'";';
                $js .= 'jQuery("img").each(function(){';
                $js .= 'if(jQuery(this).attr("src") == nopin_img_src){';
                $js .= 'jQuery(this).attr("data-pin-nopin", "true");';
                $js .= '}';
                $js .= '});';
                $js .= '});';
            }
        }
    }
    wp_add_inline_script('wl-pin-main', $js);
}

//Add Pin It Button After Post Content
function Load_pin_it_button_after_post_content($content)
{
    if (is_single() && get_post_type($post = get_post()) == "post") {
        //check for enable post pin it button
        $PinItPost 		= get_option("WL_Enable_Pinit_Post");
        $PinItStatus 	= get_option("WL_Mobile_Status");
        if (get_option("WL_Enable_Pinit_Post")) {
            if (wp_is_mobile() && $PinItStatus == 0) {
                // do nothing //don't show on mobile
            } else {
                $content .= '<p><a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"  data-pin-color="red" data-pin-height="128"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_28.png" /></a></p>';
            }
        }
    }
    return $content;
}
//add_filter("the_content", "Load_pin_it_button_after_post_content");

//Add Pin It Button After Page Content
function Load_pin_it_button_after_page_content($content)
{
    if (!is_single()  && get_post_type($post = get_post()) == "page") {
        //check for enable page pin it button
        $PinItPage 		   = get_option("WL_Enable_Pinit_Page");
        $PinItStatus 	   = get_option("WL_Mobile_Status");
        $all_exclude_pages = get_option('excluded_pint_it_pages', array());
        if (get_option("WL_Enable_Pinit_Page")) {
            if (wp_is_mobile() && $PinItStatus == 0) {
                // do nothing //don't show on mobile
            } else {
                $content .= '<p><a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark" data-pin-color="red" data-pin-height="128"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_28.png" /></a></p>';
            }
        }
    }
    return $content;
}
add_filter("the_content", "Load_pin_it_button_after_page_content");

//Plugin Settings Admin Menu
add_action('admin_menu', 'WL_PinItButtonPage');
function WL_PinItButtonPage()
{
    $PinItAdminMenu = add_menu_page('PinIt Button Settings', 'PinIt Button', 'administrator', 'pinterest-pinit-button-on-hover', 'pinterest_pinit_button_settings_page', 'dashicons-admin-post');
    add_action('admin_print_styles-' . $PinItAdminMenu, 'PiniIt_Menu_Assets');
}

//Load PinItAdminMenu Pages Assets JS/CSS/Images
function PiniIt_Menu_Assets()
{
    if (is_admin()) {
        wp_register_style('bootstrap', WEBLIZAR_PINIT_PLUGIN_URL.'css/bootstrap.min.css');
        wp_enqueue_style('bootstrap');
        wp_register_style('weblizar-smartech-css', WEBLIZAR_PINIT_PLUGIN_URL.'css/weblizar-smartech.css');
        wp_enqueue_style('weblizar-smartech-css');
        wp_register_style('weblizar-recom', WEBLIZAR_PINIT_PLUGIN_URL.'css/recom.css');
        wp_enqueue_style('weblizar-recom');
        wp_register_style('fontawesome', WEBLIZAR_PINIT_PLUGIN_URL.'css/all.min.css');
        wp_enqueue_style('fontawesome');
        wp_enqueue_script('jquery');
        wp_register_script('bootstrap', WEBLIZAR_PINIT_PLUGIN_URL.'js/bootstrap.min.js');
        wp_enqueue_script('bootstrap');
        wp_register_script('weblizar-smartech-js', WEBLIZAR_PINIT_PLUGIN_URL.'js/weblizar-smartech.js', array('jquery'));
        wp_enqueue_script('weblizar-smartech-js');
    }
}


// function add_custom_css()
// {
// }
// add_action('admin_enqueue_scripts', 'add_custom_css', 99);

function pinterest_pinit_button_settings_page()
{
    require_once("settings.php");
}

//Save Settings
add_action('wp_ajax_save_pinit', 'PinItSaveSettings');
function PinItSaveSettings()
{
    if (isset($_POST['PinItSettingNonce']) || wp_verify_nonce($_POST['PinItSettingNonce'], 'pinitsetting_nonce_action')) {
        // process form data
        $PinItPost 		= sanitize_text_field($_POST['PinItPost']);
        $PinItPage	 	= sanitize_text_field($_POST['PinItPage']);
        $PinItOnHover	= sanitize_text_field($_POST['PinItOnHover']);
        $PinItStatus 	= sanitize_text_field($_POST['PinItStatus']);
        if (isset($_POST['PinItDesign'])) {
            $PinItDesign 	= sanitize_text_field($_POST['PinItDesign']);
        }
        if (isset($_POST['PinItColor'])) {
            $PinItColor 	= sanitize_hex_color($_POST['PinItColor']);
        }
        // $PinItColor 	= sanitize_text_field($_POST['PinItColor']);
        $PinItSize 		= sanitize_text_field($_POST['PinItSize']);

        update_option('WL_Enable_Pinit_Post', $PinItPost);
        update_option('WL_Enable_Pinit_Page', $PinItPage);
        update_option('WL_Pinit_Btn_On_Hover', $PinItOnHover);
        update_option('WL_Mobile_Status', $PinItStatus);
        if (isset($PinItDesign)) {
            update_option('WL_Pinit_Btn_Design', $PinItDesign);
        }
        if (isset($PinItColor)) {
            update_option('WL_Pinit_Btn_Color', $PinItColor);
        }
        update_option('WL_Pinit_Btn_Size', $PinItSize);
    } else {
        print 'Sorry, your nonce did not verify.';
        exit;
    }
}
/*Save Exclude Images*/
add_action('wp_ajax_exclude_image', 'exclude_image_save');
function exclude_image_save()
{
    if (isset($_POST['pinit_exclude_nonce_field']) || wp_verify_nonce($_POST['pinit_exclude_nonce_field'], 'pinit_exclude_nonce_action')) {
        $all_exclude_images = get_option('exclude_pin_it_images');
        $img_url =  esc_url_raw($_POST['img_url']);
        if ($img_url) {
            $all_exclude_images[] = $img_url;
            update_option('exclude_pin_it_images', $all_exclude_images);
        }
    } else {
        print 'Sorry, your nonce did not verify.';
        exit;
    }
}

/* Save exclude pages */
add_action('wp_ajax_exclude_page', 'exclude_save_page');
function exclude_save_page()
{
    //$all_exclude_pages = [];
    if (isset($_POST['pinit_exclude_page_nonce_field']) || wp_verify_nonce($_POST['pinit_exclude_page_nonce_field'], 'pinit_exclude_page_nonce_action')) {
        $all_exclude_pages = get_option('excluded_pint_it_pages');
        $page_name = sanitize_text_field($_POST['page_name']);
        if ($page_name) {
            $all_exclude_pages[] = $page_name;
            update_option('excluded_pint_it_pages', $all_exclude_pages);
        }
    } else {
        print 'Sorry, your nonce did not verify.';
        exit;
    }
}

/*Delete Exclude Images*/
add_action('wp_ajax_delete_exclude_images', 'exclude_image_delete');
function exclude_image_delete()
{
    if (isset($_POST['pinit_exclude_nonce_field']) || wp_verify_nonce($_POST['pinit_exclude_nonce_field'], 'pinit_exclude_nonce_action')) {
        $all_exclude_images = get_option('exclude_pin_it_images');
        $img_ids = ($_POST['img_ids']);
        foreach ($img_ids as $id) {
            unset($all_exclude_images[$id]);
        }
        update_option('exclude_pin_it_images', $all_exclude_images);
    } else {
        print 'Sorry, your nonce did not verify.';
        exit;
    }
}

/*Delete exclude pages*/
add_action('wp_ajax_delete_exclude_pages', 'exclude_image_page');
function exclude_image_page()
{
    if (isset($_POST['pinit_exclude_page_nonce_field']) || wp_verify_nonce($_POST['pinit_exclude_page_nonce_field'], 'pinit_exclude_page_nonce_action')) {
        $all_exclude_pages = get_option('excluded_pint_it_pages');
        $page_ids = ($_POST['page_ids']);
        foreach ($page_ids as $id) {
            unset($all_exclude_pages[$id]);
        }
        update_option('excluded_pint_it_pages', $all_exclude_pages);
    } else {
        print 'Sorry, your nonce did not verify.';
        exit;
    }
}
/*Plugin Setting Link*/
function weblizar_pinitbutt_add_settings_link($links)
{
    $pinitbutt_add_pro_link = '<a href="https://weblizar.com/plugins/pinterest-feed-pro/" target="_blank">'. esc_html__('Get Premium', WEBLIZAR_PINIT_TD) .'</a>';
    array_unshift($links, $pinitbutt_add_pro_link);
    $settings_link_pinitbutt = '<a href="admin.php?page=pinterest-pinit-button-on-hover">' . esc_html__('Settings', WEBLIZAR_PINIT_TD) . '</a>';
    array_unshift($links, $settings_link_pinitbutt);
    return $links;
}

$plugin_pinitbutt = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin_pinitbutt", 'weblizar_pinitbutt_add_settings_link');
