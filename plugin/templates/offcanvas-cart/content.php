<?php
/**
 * Offcanvas cart contents. Replaced via WooCommerce cart fragments, so keep the
 * wrapper div and its "wc-offcanvas-cart__content" class.
 *
 * Override: {theme}/woocommerce/offcanvas-cart/content.php
 */

defined('ABSPATH') || exit;

$cart = WC()->cart;
?>
<div class="wc-offcanvas-cart__content uk-flex uk-flex-column uk-flex-1">

    <?php if ($cart && !$cart->is_empty()) : ?>

        <ul class="wc-offcanvas-cart__list uk-list uk-list-divider">
            <?php
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                $visible = apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key);

                if (!$_product instanceof WC_Product || !$_product->exists() || $cart_item['quantity'] <= 0 || !$visible) {
                    continue;
                }

                $cart_item_name = $cart->get_item_product_name($cart_item, $_product);
                $product_name = apply_filters('woocommerce_cart_item_name', $cart_item_name, $cart_item, $cart_item_key);
                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_gallery_thumbnail'), $cart_item, $cart_item_key);
                $product_price = apply_filters('woocommerce_cart_item_price', $cart->get_product_price($_product), $cart_item, $cart_item_key);
                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                ?>
                <li class="woocommerce-mini-cart-item <?php echo esc_attr(apply_filters('woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key)); ?>">
                    <div class="uk-grid-small uk-flex-middle" uk-grid>

                        <div class="uk-width-1-4">
                            <?php if ($product_permalink) : ?>
                                <a href="<?php echo esc_url($product_permalink); ?>" tabindex="-1"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <?php else : ?>
                                <?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php endif; ?>
                        </div>

                        <div class="uk-width-expand">
                            <div class="uk-text-small">
                                <?php if ($product_permalink) : ?>
                                    <a class="uk-link-reset" href="<?php echo esc_url($product_permalink); ?>"><?php echo wp_kses_post($product_name); ?></a>
                                <?php else : ?>
                                    <?php echo wp_kses_post($product_name); ?>
                                <?php endif; ?>
                            </div>
                            <?php echo wc_get_formatted_cart_item_data($cart_item, false, $cart_item_name); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <div class="uk-text-meta">
                                <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        </div>

                        <div class="uk-width-auto">
                            <?php
                            // Classes and data attributes are required by WooCommerce's AJAX remove handler.
                            echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                'woocommerce_cart_item_remove_link',
                                sprintf(
                                    '<a role="button" href="%s" class="remove_from_cart_button uk-icon-link" uk-icon="close" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s"></a>',
                                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                                    /* translators: %s is the product name */
                                    esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                                    esc_attr($product_id),
                                    esc_attr($cart_item_key),
                                    esc_attr($_product->get_sku()),
                                    /* translators: %s is the product name */
                                    esc_attr(sprintf(__('&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce'), wp_strip_all_tags($product_name)))
                                ),
                                $cart_item_key
                            );
                            ?>
                        </div>

                    </div>
                </li>
                <?php
            }
            ?>
        </ul>

        <div class="uk-margin-auto-top">
            <hr>
            <div class="uk-flex uk-flex-between uk-flex-middle uk-margin">
                <strong><?php esc_html_e('Subtotal:', 'woocommerce'); ?></strong>
                <span><?php echo $cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            </div>
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="uk-button uk-button-default uk-width-1-1 uk-margin-small-bottom"><?php esc_html_e('View cart', 'woocommerce'); ?></a>
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="uk-button uk-button-primary uk-width-1-1"><?php esc_html_e('Checkout', 'woocommerce'); ?></a>
        </div>

    <?php else : ?>

        <p><?php esc_html_e('No products in the cart.', 'woocommerce'); ?></p>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="uk-button uk-button-default uk-width-1-1"><?php esc_html_e('Return to shop', 'woocommerce'); ?></a>

    <?php endif; ?>

</div>
