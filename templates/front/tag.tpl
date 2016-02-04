{if isset($portfolio_entries)}
	{foreach $portfolio_entries as $portfolio_entry}
		<div class="ia-item">
			{if $portfolio_entry.image}
				<a href="{$smarty.const.IA_URL}portfolio/{$portfolio_entry.id}-{$portfolio_entry.alias}" class="ia-item__image">{printImage imgfile=$portfolio_entry.image title=$portfolio_entry.title}</a>
			{/if}
			<div class="ia-item__content">
				<h4 class="ia-item__title">
					<a href="{$smarty.const.IA_URL}portfolio/{$portfolio_entry.id}-{$portfolio_entry.alias}">{$portfolio_entry.title|escape:'html'}</a>
				</h4>
				<div class="ia-item__additional">
					<p>{lang key='posted_on'} {$portfolio_entry.date_added|date_format:$core.config.date_format}</p>
				</div>
				<div class="ia-item__body">{$portfolio_entry.body|strip_tags|truncate:$core.config.blog_max:'...'}</div>
			</div>
		</div>
	{/foreach}
	{navigation aTotal=$pagination.total aTemplate=$pagination.template aItemsPerPage=$core.config.portfolio_entries_per_page aNumPageItems=5}
{else}
	{if $tags}
		{foreach $tags as $tag}
			{if $tag != ''}
				<div class="media ia-item">
					<div class="media-body">
						<h4 class="media-heading">
							<a href="{$smarty.const.IA_URL}portfolio/tag/{$tag.alias}">#{$tag.title|escape:'html'}</a>
						</h4>
					</div>
				</div>
			{/if}
		{/foreach}

		{navigation aTotal=$pagination.total aTemplate=$pagination.template aItemsPerPage=$core.config.tag_number aNumPageItems=5}
	{else}
		<div class="alert alert-info">{lang key='no_tags'}</div>
	{/if}
{/if}