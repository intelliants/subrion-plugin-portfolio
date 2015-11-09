{if isset($portfolio_entry)}
	<div class="media ia-item portfolio-entry">
		<p class="ia-item-date">{lang key='posted_on'} {$portfolio_entry.date_added|date_format:$core.config.date_format}</p>

		{if $portfolio_entry.image}
			<div class="ia-item-image">{printImage imgfile=$portfolio_entry.image fullimage=true title=$portfolio_entry.title}</div>
		{/if}

		<div class="ia-item-body">{$portfolio_entry.body}</div>

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
		<div class="pf">
			<div class="row-fluid">
				{foreach $portfolio_entries as $pf_entry}
					<div class="span3">
						<div class="pf__item">
							{if $pf_entry.image}
								<a href="{$smarty.const.IA_URL}portfolio/{$pf_entry.id}-{$pf_entry.alias}" class="pf__item__image">{printImage imgfile=$pf_entry.image title=$pf_entry.title}</a>
							{/if}
						</div>
					</div>

					{if $pf_entry@iteration % 4 == 0}
						</div>
						<div class="row-fluid">
					{/if}
				{/foreach}
			</div>
		</div>

		{navigation aTotal=$pagination.total aTemplate=$pagination.template aItemsPerPage=$core.config.portfolio_entries_per_page  aNumPageItems=5}
	{else}
		<div class="alert alert-info">{lang key='pf_no_entries'}</div>
	{/if}
{/if}