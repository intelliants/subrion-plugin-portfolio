{if !empty($entries)}
    <div class="portfolio-entries">
        <div class="row">
            {foreach $entries as $entry}
                {include file='extra:portfolio/list-portfolio'}

                {if $entry@iteration % 4 == 0 && !$entry@last}
                    </div>
                    <div class="row">
                {/if}
            {/foreach}
        </div>
    </div>

    {navigation aTotal=$pagination.total aTemplate=$pagination.url aItemsPerPage=$pagination.limit aNumPageItems=5}
{else}
    <div class="alert alert-info">{lang key='no_portfolio_entries'}</div>
{/if}