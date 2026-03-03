{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file=$layout}

{block name='breadcrumb'}{/block}

{block name='content_columns'}
    {block name='left_column'}{/block}

    {block name='content_wrapper'}
        <div id="content-wrapper" class="wrapper__content">
            {hook h="displayContentWrapperTop"}



            {block name='content'}
                <!-- TODO INSIDE -->
                <section class="hero-home">
                    <div class="container">
                        <div class="hero-home__content">
                            <h1 class="hero-home__title">{l s='Votre studio d\'enregistrement près de Colmar' d='Shop.Theme.Global'}
                            </h1>
                            <p class="hero-home__description">
                                {l s='À deux pas de Colmar, notre studio d’enregistrement à Sainte-Croix-en-Plaine vous accompagne de l’enregistrement au mastering, avec écoute, exigence et un cadre propice à la création.' d='Shop.Theme.Global'}
                            </p>
                            <a href="{$link->getPageLink('contact', true)|escape:'html'}"
                                class="btn btn-primary">{l s='Nous contacter' d='Shop.Theme.Global'}</a>
                        </div>
                        <div class="hero-home__featured">
                            {assign var="home_services" value=['edition', 'enregistrement', 'mastering', 'mixage']}
                            {foreach from=$home_services item=service}
                                <div class="hero-home__featured-item hero-home__featured-item--{$service}">
                                    <span class="hero-home__featured-item-title">{$service}</span>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </section>
                {block name='page_header_container'}
                    {block name='page_title' hide}
                        <header class="page-header">
                            <h1 class="h1">{$smarty.block.child}</h1>
                        </header>
                    {/block}
                {/block}

                {block name='page_content_container'}
                    <section id="content" class="page-content page-home">
                        {block name='page_content_top'}{/block}

                        {block name='page_content'}
                            {block name='hook_home'}
                                {$HOOK_HOME nofilter}
                            {/block}
                        {/block}
                    </section>
                {/block}

                {block name='page_footer_container'}
                    <footer class="page-footer">
                        {block name='page_footer'}
                            <!-- Footer content -->
                        {/block}
                    </footer>
                {/block}
                <!-- TODO INSIDE -->
            {/block}

            {hook h="displayContentWrapperBottom"}
        </div>
    {/block}

    {block name='right_column'}{/block}
{/block}