{ia_add_media files='css: _IA_URL_modules/portfolio/templates/front/css/style'}

{if isset($portfolio_entry)}
	<div class="portfolio-entry">

		{if $portfolio_entry.image}
			{ia_image file=$portfolio_entry.image title=$portfolio_entry.title type='large'}
		{/if}

		{$portfolio_entry.body}

		<div class="tags">
			<span class="fa fa-tags"></span>
			{if $tags}
				{lang key='tags'}:
				{foreach $tags as $tag}
					<a href="{$smarty.const.IA_URL}portfolio/tag/{$tag.alias}">{$tag.title|escape:'html'}</a>{if !$tag@last}, {/if}
				{/foreach}
			{else}
				{lang key='no_tags'}
			{/if}
		</div>

		<hr>
		<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_default_style">
			<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
			<a class="addthis_button_tweet"></a>
			<a class="addthis_button_pinterest_pinit"></a>
			<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
			<a class="addthis_counter addthis_pill_style"></a>
		</div>
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-5170da8b1f667e6d"></script>
		<!-- AddThis Button END -->
	</div>
{else}
	{if $portfolio_entries}
		<div class="portfolio-entries">
			<div class="row">
				{foreach $portfolio_entries as $pf_entry}
					<div class="col-md-3">
						<div class="ia-item ia-item--card">
							{if $pf_entry.image}
								<a href="{$smarty.const.IA_URL}portfolio/{$pf_entry.id}-{$pf_entry.alias}" class="ia-item__image">{ia_image file=$pf_entry.image title=$pf_entry.title}<span class="fa fa-eye"></span></a>
							{/if}

							<div class="ia-item__content">
								<h4 class="ia-item__title">
									<a href="{$smarty.const.IA_URL}portfolio/{$pf_entry.id}-{$pf_entry.alias}">{$pf_entry.title|escape: html}</a>
								</h4>

								<p class="text-muted">{$pf_entry.body|strip_tags|truncate:$core.config.portfolio_desc_length:'...'}</p>
								<p><span class="fa fa-tags"></span>
								{if $tags}
									{$tagsExist=0}
									{foreach $tags as $tag}
										{if $pf_entry.id == $tag.portfolio_id}
											{$tagsExist = $tagsExist + 1}
										{/if}
									{/foreach}
									{if $tagsExist != 0}
										{foreach $tags as $tag}
											{if $pf_entry.id == $tag.portfolio_id}
												<a href="{$smarty.const.IA_URL}portfolio/tag/{$tag.alias}">{$tag.title|escape: 'html'}</a>
											{/if}
										{/foreach}
									{else}
										{lang key='no_tags'}
									{/if}

								{else}
									{lang key='no_tags'}
								{/if}
								</p>
							</div>
						</div>
					</div>

					{if $pf_entry@iteration % 4 == 0 && !$pf_entry@last}
						</div>
						<div class="row">
					{/if}
				{/foreach}
			</div>
		</div>

		{navigation aTotal=$pagination.total aTemplate=$pagination.template aItemsPerPage=$core.config.portfolio_entries_per_page  aNumPageItems=5}
	{else}
		<div class="alert alert-info">{lang key='pf_no_entries'}</div>
	{/if}
{/if}