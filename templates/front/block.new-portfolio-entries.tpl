{if !empty($block_portfolio_entries)}
	<div class="ia-items portfolio-entries m-b">
		<div class="row">
			{foreach $block_portfolio_entries as $pf_entry}
				<div class="col-md-3">
					<div class="ia-item ia-item--card">
						{if $pf_entry.image}
							<a href="{$smarty.const.IA_URL}portfolio/{$pf_entry.id}-{$pf_entry.alias}" class="ia-item__image">{ia_image file=$pf_entry.image title=$pf_entry.title}<span class="fa fa-eye"></span></a>
						{/if}

						<div class="ia-item__content">
							<h4 class="ia-item__title">
								<a href="{$smarty.const.IA_URL}portfolio/{$pf_entry.id}-{$pf_entry.alias}">{$pf_entry.title|escape: html}</a>
							</h4>
						</div>

						<p class="ia-item__tags">
							<span class="fa fa-tags"></span>
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

				{if $pf_entry@iteration == $core.config.portfolio_block_count}
					{break}
				{/if}
			{/foreach}
		</div>

		<p class="m-t-md text-center">
			<a class="btn btn-primary-outline m-r" href="{$smarty.const.IA_URL}portfolio/">{lang key='pf_view_all'}</a>
		</p>
	</div>
{else}
	<div class="alert alert-info">{lang key='pf_no_entries'}</div>
{/if}

{ia_add_media files='css: _IA_URL_modules/portfolio/templates/front/css/style'}