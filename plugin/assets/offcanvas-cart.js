(function ($) {
  const el = document.getElementById('wc-offcanvas-cart')

  if (!el || !window.UIkit) {
    return
  }

  const open = () => UIkit.offcanvas(el).show()

  // WooCommerce triggers its cart events through jQuery, so they can't be caught with addEventListener.
  // Fragments (including the offcanvas content) are already replaced by WooCommerce before this runs.
  $(document.body).on('added_to_cart', open)

  // Toggle links keep the cart URL as fallback (no JS, cart/checkout pages, opening in a new tab).
  document.addEventListener('click', (e) => {
    const link = e.target.closest('.wc-offcanvas-cart-toggle > a, a.wc-offcanvas-cart-toggle')

    if (!link || e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) {
      return
    }

    e.preventDefault()
    open()
  })

  // Restored from back/forward cache: the page comes back exactly as it was left, including an open offcanvas.
  window.addEventListener('pageshow', (e) => {
    if (e.persisted && el.classList.contains('uk-open')) {
      UIkit.offcanvas(el).hide()
    }
  })

  // Back/forward without bfcache re-renders the cached HTML, which still carries the auto-open flag.
  const nav = performance.getEntriesByType('navigation')[0]

  if (el.hasAttribute('data-auto-open') && (!nav || nav.type !== 'back_forward')) {
    open()
  }
})(jQuery)
