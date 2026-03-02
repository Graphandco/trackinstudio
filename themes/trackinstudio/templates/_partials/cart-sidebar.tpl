{**
 * Panier latéral (sidebar) - rendu direct depuis $cart Smarty
 * data-refresh-url : page à charger pour rafraîchir le contenu (updateCart)
 *}
<div id="cart-sidebar" class="cart-sidebar" aria-hidden="true" data-refresh-url="{$urls.pages.index}">
  <div class="cart-sidebar__backdrop" data-cart-sidebar-close></div>
  <div class="cart-sidebar__panel">
    <div class="cart-sidebar__header">
      <h2 class="cart-sidebar__title">{l s='Your cart' d='Shop.Theme.Checkout'}</h2>
      <button type="button" class="cart-sidebar__close" data-cart-sidebar-close aria-label="{l s='Close' d='Shop.Theme.Global'}">
        <i class="material-icons">close</i>
      </button>
    </div>
    <div class="cart-sidebar__body">
      <div class="cart-sidebar__empty js-cart-sidebar-empty" {if isset($cart) && $cart.products_count > 0}style="display:none;"{/if}>
        <p>{l s='Your cart is empty' d='Shop.Theme.Checkout'}</p>
      </div>
      <div class="cart-sidebar__products js-cart-sidebar-products" {if !isset($cart) || $cart.products_count == 0}style="display:none;"{/if}>
        {if isset($cart.products) && is_array($cart.products) && $cart.products|@count > 0}
          {foreach from=$cart.products item=product}
            {assign var="imgUrl" value=$urls.no_picture_image.bySize.medium_default.url}
            {if isset($product.default_image.bySize.medium_default.url)}{assign var="imgUrl" value=$product.default_image.bySize.medium_default.url}{/if}
            {if $imgUrl == $urls.no_picture_image.bySize.medium_default.url && isset($product.default_image.bySize.default_xs.url)}{assign var="imgUrl" value=$product.default_image.bySize.default_xs.url}{/if}
            {if $imgUrl == $urls.no_picture_image.bySize.medium_default.url && isset($product.default_image.medium.url)}{assign var="imgUrl" value=$product.default_image.medium.url}{/if}
            {assign var="qty" value=$product.quantity|default:$product.cart_quantity|default:1}
            <div class="cart-sidebar__product" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute|default:0}" data-id-customization="{$product.id_customization|default:0}" data-update-url="{$product.update_quantity_url|default:''}">
              <div class="cart-sidebar__product-image">
                <a href="{$product.url}">
                  <img src="{$imgUrl}" alt="{$product.name|escape:'quotes'}" loading="lazy" />
                </a>
              </div>
              <div class="cart-sidebar__product-details">
                <a href="{$product.url}" class="cart-sidebar__product-name">{$product.name}</a>
                <div class="cart-sidebar__product-price">{$product.price}</div>
                <div class="cart-sidebar__product-qty">
                  <button type="button" class="cart-sidebar__qty-btn js-cart-qty-down" aria-label="{l s='Decrease' d='Shop.Theme.Actions'}">−</button>
                  <span class="cart-sidebar__qty-value">{$qty}</span>
                  <button type="button" class="cart-sidebar__qty-btn js-cart-qty-up" aria-label="{l s='Increase' d='Shop.Theme.Actions'}">+</button>
                </div>
              </div>
              <div class="cart-sidebar__product-actions">
                <a href="{$product.remove_from_cart_url}" class="cart-sidebar__remove js-cart-remove" data-link-action="delete-from-cart" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute|default:0}" data-id-customization="{$product.id_customization|default:0}" data-product-url="{$product.url}" data-product-name="{$product.name|escape:'htmlall':'UTF-8'}" rel="nofollow" title="{l s='Remove' d='Shop.Theme.Checkout'}">
                  <i class="material-icons">delete_outline</i>
                </a>
              </div>
            </div>
          {/foreach}
        {/if}
      </div>
      <div class="cart-sidebar__loading js-cart-sidebar-loading" style="display:none;">
        <div class="spinner-border" role="status"></div>
      </div>
    </div>
    <div class="cart-sidebar__footer js-cart-sidebar-footer" {if !isset($cart) || $cart.products_count == 0}style="display:none;"{/if}>
      <div class="cart-sidebar__total">
        <span class="cart-sidebar__total-label">{l s='Total' d='Shop.Theme.Checkout'}</span>
        <span class="cart-sidebar__total-value js-cart-sidebar-total">{if isset($cart.totals.total.value)}{$cart.totals.total.value}{elseif isset($cart.totals.total.amount)}{$cart.totals.total.amount}{else}{/if}</span>
      </div>
      <div class="cart-sidebar__actions">
        <a href="{$urls.pages.cart}" class="btn btn-outline-primary cart-sidebar__btn-cart js-cart-sidebar-btn-cart">{l s='View cart' d='Shop.Theme.Checkout'}</a>
        <a href="{$urls.pages.order}" class="btn btn-primary cart-sidebar__btn-checkout js-cart-sidebar-btn-checkout">{l s='Proceed to checkout' d='Shop.Theme.Actions'}</a>
      </div>
    </div>
  </div>
</div>
