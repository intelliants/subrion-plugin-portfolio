{if !empty($entries)}
    {if !empty($categories)}
        <div class="ia-categories m-b">
            {include file='ia-categories.tpl' categories=$categories item='portfolio_categories' show_amount=false num_columns=3 icons=true}
        </div>
    {/if}

    <div class="portfolio-entries">
        <div class="row">
            {foreach $entries as $entry}
            {include file='module:portfolio/list-portfolio.tpl'}

            {if $entry@iteration % 4 == 0 && !$entry@last}
        </div>
        <div class="row">
            {/if}
            {/foreach}
        </div>
    </div>

    {navigation aTotal=$pagination.total aTemplate=$pagination.url aItemsPerPage=$pagination.limit aNumPageItems=5}
{else}
    <div class="alert alert-info">{lang key='no_portfolio_entries_in_category'}</div>
{/if}