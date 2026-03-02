{**
 * Layout principal - étend le parent et ajoute le panier latéral
 *}
{extends file='parent:layouts/layout-both-columns.tpl'}

{block name='bottom_elements'}
  {include file='_partials/cart-sidebar.tpl'}
  {include file='components/page-loader.tpl'}
  {include file='components/toast-container.tpl'}
  {include file='components/password-policy-template.tpl'}
{/block}
