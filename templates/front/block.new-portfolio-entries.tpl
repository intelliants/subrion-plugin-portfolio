{if isset($block_portfolio_entries) && $block_portfolio_entries}
	<div class="pf">
		<div class="row-fluid">
			{foreach $block_portfolio_entries as $pf_entry}
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
{else}
	<div class="alert alert-info">{lang key='pf_no_entries'}</div>
{/if}