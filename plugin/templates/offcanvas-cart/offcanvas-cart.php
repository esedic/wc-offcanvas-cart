<?php
/**
 * Offcanvas cart wrapper.
 *
 * Override: {theme}/woocommerce/offcanvas-cart/offcanvas-cart.php
 *
 * @var string $id
 * @var bool   $auto_open
 */

defined('ABSPATH') || exit;
?>
<div id="<?php echo esc_attr($id); ?>" class="wc-offcanvas-cart" uk-offcanvas="flip: true; overlay: true"<?php echo $auto_open ? ' data-auto-open' : ''; ?>>
    <div class="uk-offcanvas-bar uk-flex uk-flex-column" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr($id); ?>-title">

        <button class="uk-offcanvas-close" type="button" uk-close aria-label="<?php esc_attr_e('Close', 'woocommerce'); ?>"></button>

        <h3 id="<?php echo esc_attr($id); ?>-title" class="uk-h4 uk-margin-remove-top"><?php esc_html_e('Cart', 'woocommerce'); ?></h3>

        <?php WC_Offcanvas_Cart::renderContent(); ?>

    </div>
</div>
