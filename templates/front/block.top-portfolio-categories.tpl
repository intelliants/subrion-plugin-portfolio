{if !empty($topPortfolioCategories)}
    <div class="list-group">
        {foreach $topPortfolioCategories as $item}
            <a class="list-group-item clearfix" href="{$smarty.const.IA_URL}portfolio/{$item.title_alias}">
                {$item.title|escape:'html'}
                {if $core.config.portfolio_show_counter}
                    <span class="badge pull-right">
                        {if $core.config.portfolio_show_children_entries}
                            {$item.num_all_listings}
                        {else}
                            {$item.num_listings}
                        {/if}
                    </span>
                {/if}
            </a>
        {/foreach}
    </div>
{/if}