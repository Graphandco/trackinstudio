{**
 * Layout principal - étend le parent et ajoute le panier latéral
 * GSAP chargé avant les autres scripts pour l'animation du panier
 *}
{extends file='parent:layouts/layout-both-columns.tpl'}

{block name='javascript_bottom'}
  <script src="{$urls.base_url}themes/trackinstudio/assets/js/gsap.min.js"></script>
  {$smarty.block.parent}
{/block}

{block name='bottom_elements'}
  {include file='_partials/cart-sidebar.tpl'}
  {include file='components/page-loader.tpl'}
  {include file='components/toast-container.tpl'}
  {include file='components/password-policy-template.tpl'}
{/block}
