<?php
/**
 * Plugin Name: WC Offcanvas Cart
 * Description: Opens a UIkit offcanvas sidebar with the cart contents when a product is added to the cart (YOOtheme).
 * Version: 1.0.1
 * Requires Plugins: woocommerce
 * Text Domain: wc-offcanvas-cart
 */

defined('ABSPATH') || exit;

final class WC_Offcanvas_Cart
{
    const ID = 'wc-offcanvas-cart';
    const SESSION_KEY = 'wc_offcanvas_cart_open';
    const TOGGLE_CLASS = 'wc-offcanvas-cart-toggle';
    const VERSION = '1.0.1';

    public static function init()
    {
        if (!class_exists('WooCommerce')) {
            return;
        }

        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueScripts']);
        add_action('wp_footer', [__CLASS__, 'render']);
        add_action('woocommerce_add_to_cart', [__CLASS__, 'flagNonAjaxAdd']);
        add_filter('woocommerce_add_to_cart_redirect', [__CLASS__, 'redirectAfterAdd']);
        add_filter('woocommerce_add_to_cart_fragments', [__CLASS__, 'addFragment']);
        add_filter('wp_nav_menu_objects', [__CLASS__, 'addMenuBadge'], 20);

        // The offcanvas replaces the "added to cart" notice; WooCommerce skips empty notices.
        add_filter('wc_add_to_cart_message_html', '__return_empty_string');
    }

    /**
     * Offcanvas is not needed where the full cart is already visible.
     */
    private static function isEnabled()
    {
        return !is_admin() && !is_cart() && !is_checkout();
    }

    public static function enqueueScripts()
    {
        if (!self::isEnabled()) {
            return;
        }

        // Keeps the offcanvas in sync when pages are served from cache.
        wp_enqueue_script('wc-cart-fragments');

        wp_enqueue_script(
            self::ID,
            plugins_url('assets/offcanvas-cart.js', __FILE__),
            ['jquery', 'wc-add-to-cart'],
            self::VERSION,
            ['in_footer' => true, 'strategy' => 'defer']
        );
    }

    /**
     * Non-AJAX adds (single product forms) reload the page, so remember to open the offcanvas on the next render.
     */
    public static function flagNonAjaxAdd()
    {
        if (wp_doing_ajax() || WC()->is_rest_api_request() || !WC()->session) {
            return;
        }

        WC()->session->set(self::SESSION_KEY, true);
    }

    /**
     * Post/Redirect/Get after a non-AJAX add, so browser back/refresh doesn't resubmit the form and add the product again.
     */
    public static function redirectAfterAdd($url)
    {
        if ($url || wp_doing_ajax() || 'yes' === get_option('woocommerce_cart_redirect_after_add')) {
            return $url;
        }

        return remove_query_arg(['add-to-cart', 'quantity', 'variation_id', 'product_id']);
    }

    public static function render()
    {
        if (!self::isEnabled()) {
            return;
        }

        $autoOpen = false;

        if (WC()->session && WC()->session->get(self::SESSION_KEY)) {
            $autoOpen = true;
            WC()->session->set(self::SESSION_KEY, null);
        }

        self::template('offcanvas-cart.php', ['id' => self::ID, 'auto_open' => $autoOpen]);
    }

    public static function renderContent()
    {
        self::template('content.php');
    }

    public static function addFragment($fragments)
    {
        ob_start();
        self::renderContent();
        $fragments['div.wc-offcanvas-cart__content'] = ob_get_clean();
        $fragments['[data-wc-offcanvas-cart-count]'] = self::badge();

        return $fragments;
    }

    /**
     * Appends a cart quantity badge to menu items with the toggle CSS class, using YOOtheme's menu item title suffix.
     */
    public static function addMenuBadge($items)
    {
        if (!WC()->cart || !function_exists('YOOtheme\app')) {
            return $items;
        }

        $config = \YOOtheme\app(\YOOtheme\Config::class);

        foreach ($items as $item) {
            if (in_array(self::TOGGLE_CLASS, (array) $item->classes, true)) {
                $config->set("~theme.menu.items.{$item->ID}.title-suffix", self::badge());
            }
        }

        return $items;
    }

    private static function badge()
    {
        $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

        return $count
            ? sprintf('<span class="uk-badge" data-wc-offcanvas-cart-count>%d</span>', $count)
            : '<span data-wc-offcanvas-cart-count></span>';
    }

    /**
     * Templates can be overridden in {theme}/woocommerce/offcanvas-cart/{name}.
     */
    private static function template($name, array $args = [])
    {
        wc_get_template('offcanvas-cart/' . $name, $args, '', plugin_dir_path(__FILE__) . 'templates/');
    }
}

add_action('plugins_loaded', ['WC_Offcanvas_Cart', 'init']);
