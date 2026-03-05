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
* Template alternatif "compact" pour les blocs.
* Exemple d'utilisation : $module->renderBlockById(1, 'contentblocks-compact');
* Personnalisez ce fichier ou créez d'autres templates selon vos besoins.
*}

{if isset($block)}
    <div class="trackinstudio-contentblocks-container trackinstudio-contentblocks--compact">
        <div class="contentblock-item contentblock-item--compact">
            <div class="contentblock-inner contentblock-inner--compact">
                {if $block.image_filename}
                    <div class="contentblock-image">
                        <img src="{$images_dir|escape:'html':'UTF-8'}{$block.image_filename|escape:'html':'UTF-8'}"
                                alt="{$block.title|escape:'html':'UTF-8'}"
                                class="img-fluid" />
                    </div>
                {/if}
                <div class="contentblock-content">
                    <h3 class="contentblock-title">{$block.title|escape:'html':'UTF-8'}</h3>
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
    <div class="trackinstudio-contentblocks-container trackinstudio-contentblocks--compact">
        {foreach $blocks as $block}
            <div class="contentblock-item contentblock-item--compact">
                <div class="contentblock-inner contentblock-inner--compact">
                    {if $block.image_filename}
                        <div class="contentblock-image">
                            <img src="{$images_dir|escape:'html':'UTF-8'}{$block.image_filename|escape:'html':'UTF-8'}"
                                    alt="{$block.title|escape:'html':'UTF-8'}"
                                    class="img-fluid" />
                        </div>
                    {/if}
                    <div class="contentblock-content">
                        <h3 class="contentblock-title">{$block.title|escape:'html':'UTF-8'}</h3>
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
{/if}
