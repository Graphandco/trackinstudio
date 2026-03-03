{**
 * Override du panier - ajoute les données pour la sidebar latérale
 *}
{capture name="cartSidebarData" assign="cartSidebarData"}
{ldelim}
  "products": [
    {if isset($cart.products) && is_array($cart.products) && $cart.products|@count > 0}
    {foreach from=$cart.products item=product name=ploop}
    {ldelim}
      "id_product": {$product.id_product|intval},
      "id_product_attribute": {$product.id_product_attribute|intval},
      "id_customization": {$product.id_customization|default:0|intval},
      "name": "{$product.name|escape:'javascript' nofilter}",
      "quantity": {if isset($product.quantity)}{$product.quantity|intval}{elseif isset($product.cart_quantity)}{$product.cart_quantity|intval}{else}1{/if},
      "price": "{$product.price|strip_tags|escape:'javascript' nofilter}",
      "total": "{$product.total|strip_tags|escape:'javascript' nofilter}",
      "remove_from_cart_url": "{$product.remove_from_cart_url|escape:'javascript' nofilter}",
      "url": "{$product.url|escape:'javascript' nofilter}",
      "image": "{if $product.default_image && isset($product.default_image.bySize.medium_default.url)}{$product.default_image.bySize.medium_default.url|escape:'javascript' nofilter}{elseif $product.default_image && isset($product.default_image.bySize.default_xs.url)}{$product.default_image.bySize.default_xs.url|escape:'javascript' nofilter}{elseif $product.default_image && isset($product.default_image.medium.url)}{$product.default_image.medium.url|escape:'javascript' nofilter}{else}{$urls.no_picture_image.bySize.medium_default.url|escape:'javascript' nofilter}{/if}"
    {rdelim}{if !$smarty.foreach.ploop.last},{/if}
    {/foreach}
    {/if}
  ],
  "totals": {ldelim}"total": "{$cart.totals.total.amount|strip_tags|escape:'javascript' nofilter}"{rdelim},
  "products_count": {$cart.products_count|intval},
  "cart_url": "{$cart_url|escape:'javascript' nofilter}",
  "checkout_url": "{$urls.pages.order|escape:'javascript' nofilter}"
{rdelim}
{/capture}
<div id="_desktop_cart">
  <div class="header-block d-flex align-items-center blockcart cart-preview js-cart-sidebar-trigger {if $cart.products_count > 0}header-block--active{else}inactive{/if}" data-refresh-url="{$refresh_url}" data-cart-url="{$cart_url}" data-checkout-url="{$urls.pages.order}">
    <script type="application/json" data-cart-json>{$cartSidebarData|strip}</script>
    {if $cart.products_count > 0}
      <a class="header-block__action-btn position-relative" href="#" role="button" aria-label="{l s='View cart (%d products)' d='Shop.Theme.Checkout' sprintf=[$cart.products_count]}">
    {else}
      <span class="header-block__action-btn">
    {/if}

    <i class="material-icons header-block__icon" aria-hidden="true">shopping_cart</i>
    {* <span class="d-none d-md-flex header-block__title">{l s='Cart' d='Shop.Theme.Checkout'}</span> *}
    <span class="header-block__badge">{$cart.products_count}</span>

    {if $cart.products_count > 0}
      </a>
    {else}
      </span>
    {/if}
  </div>
</div>
