{*
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
* @author    Régis Daum
* @copyright 2007-2024
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
* Template par défaut pour l'affichage d'un ou plusieurs blocs.
* Créer d'autres templates (ex: contentblocks-compact.tpl) pour des designs alternatifs.
*}

{if isset($block)}
    {* Affichage d'un seul bloc (via renderBlockById) *}
    <div id="contentblocks-section-{$block.id_contentblock}">
        <div class="trackinstudio-contentblocks-container">
            <div class="contentblock-item">
                <div class="contentblock-inner container">
                    {if $block.image_filename}
                        <div class="contentblock-image">
                            <img src="{$images_dir|escape:'html':'UTF-8'}{$block.image_filename|escape:'html':'UTF-8'}"
                                alt="{$block.title|escape:'html':'UTF-8'}" class="img-fluid" />
                        </div>
                    {/if}
                    <div class="contentblock-content">
                        <h2 class="title-font-h2">
                            {* <img src="{$urls.child_theme_assets}img/interrupteur.png" alt="..." /> *}
                            <img src="{$urls.child_img_url}interrupteur.png" alt="Interrupteur" class="img-interrupteur" />
                            {$block.title|escape:'html':'UTF-8'}
                        </h2>
                        <div class="contentblock-description">
                            {$block.description nofilter}
                        </div>
                        {if !empty($block.button_url) && !empty($block.button_label)}
                            <a href="{$block.button_url|escape:'html':'UTF-8'}" class="btn btn-primary contentblock-btn">
                                {$block.button_label|escape:'html':'UTF-8'}
                            </a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {elseif isset($blocks) && count($blocks) > 0}
        {* Affichage de plusieurs blocs (via renderBlocksByPage) *}
        <div class="trackinstudio-contentblocks-container">
            {foreach $blocks as $block}
                <div class="contentblock-item">
                    <div class="contentblock-inner container">
                        <div class="contentblock-image">
                            {if $block.image_filename}
                                <img src="{$images_dir|escape:'html':'UTF-8'}{$block.image_filename|escape:'html':'UTF-8'}"
                                    alt="{$block.title|escape:'html':'UTF-8'}" class="img-fluid" />
                            {/if}
                        </div>
                        <div class="contentblock-content">
                            <h2 class="title-font-h2 text-rose">{$block.title|escape:'html':'UTF-8'}</h2>
                            <div class="contentblock-description">
                                {$block.description nofilter}
                            </div>
                            {if !empty($block.button_url) && !empty($block.button_label)}
                                <a href="{$block.button_url|escape:'html':'UTF-8'}" class="btn btn-primary contentblock-btn">
                                    {$block.button_label|escape:'html':'UTF-8'}
                                </a>
                            {/if}
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/if}