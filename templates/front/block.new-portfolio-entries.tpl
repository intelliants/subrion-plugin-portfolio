{if !empty($newPortfolioEntries)}
    <div class="m-t m-b js-portfolio-filters">
        <div class="btn btn-primary m-b-5" data-filter="*">{lang key='all'}</div>
        {if !empty($newPortfolioEntriesCategories)}
            {foreach $newPortfolioEntriesCategories as $id => $title}
                <div class="btn btn-default m-b-5" data-filter=".cat-{$id}">{$title|escape}</div>
            {/foreach}
        {/if}
    </div>

    <div class="ia-items portfolio-entries">
        <div class="row">
            {foreach $newPortfolioEntries as $entry}
                <div class="col-md-3 * cat-{$entry.category_id}">
                    <div class="ia-item ia-item--card m-b">
                        {if $entry.gallery}
                            <a href="{$entry.link}" class="ia-item__image">
                                {ia_image file=$entry.gallery|array_shift type='thumbnail' title=$entry.title|escape}
                            </a>
                        {/if}

                        <div class="ia-item__content">
                            <h4 class="ia-item__title text-center">
                                <a href="{$entry.link}">{$entry.title|escape}</a>
                            </h4>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>

        <div class="m-t text-center">
            <a class="btn btn-primary text-uppercase" href="{$smarty.const.IA_URL}portfolio/">{lang key='view_all_portfolio_entries'}</a>
        </div>
    </div>
{else}
    <div class="alert alert-info">{lang key='no_portfolio_entries'}</div>
{/if}

{ia_add_media files='js:_IA_URL_modules/portfolio/js/front/isotope.pkgd.min'}

{ia_add_js}
$(function(){
    var $grid = $('.new-portfolio-entries .row').isotope(),
        $filterBtn = $('.js-portfolio-filters > .btn');

    $filterBtn.click(function() {
        $filterBtn.removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');

        var filterValue = $(this).attr('data-filter');

        $grid.isotope({ filter: filterValue });
    });
});
{/ia_add_js}