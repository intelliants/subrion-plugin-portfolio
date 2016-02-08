{if $block_portfolio_tags}
	<div class="list-group">
		{foreach $block_portfolio_tags as $tag}
			{if $tag != ''}
				<a class="list-group-item" href="{$smarty.const.IA_URL}portfolio/tag/{$tag.alias}">#{$tag.title|escape:'html'}</a>
			{/if}
		{/foreach}
	</div>
{/if}