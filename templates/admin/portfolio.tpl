<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="wrap-group-heading">
				<h4>{lang key='general'}</h4>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-title">{lang key='title'} {lang key='field_required'}</label>
				<div class="col col-lg-4">
					<input type="text" name="title" value="{$item.title|escape:'html'}" id="input-title">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-alias">{lang key='title_alias'}</label>
				<div class="col col-lg-4">
					<input type="text" name="alias" id="input-alias" value="{if isset($item.alias)}{$item.alias}{/if}">
					<p class="help-block text-break-word" id="title_box" style="display: none;">{lang key='page_url_will_be'}: <span id="title_url" class="text-danger">{$smarty.const.IA_URL}</span></p>
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="body">{lang key='pf_body'} {lang key='field_required'}</label>
				<div class="col col-lg-8">
					{ia_wysiwyg name='body' value=$item.body}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="tags">{lang key='pf_tags'}</label>
				<div class="col col-lg-4">
					<input type="text" name="tags" value="{$item.tags|escape:'html'}" id="input-tag">
					<p class="help-block">{lang key='pf_tags_help'}</p>
				</div>
			</div>
		</div>

		<div class="wrap-group" id="image-container">
			<div class="wrap-group-heading">
				<h4>{lang key='pf_image'}</h4>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-image">{lang key='image'} {lang key='field_required'}</label>
				<div class="col col-lg-4">
					{if !$core.config.portfolio_use_crop && isset($item.image) && $item.image}
						<div class="input-group thumbnail thumbnail-single with-actions">
							<a href="{printImage imgfile=$item.image fullimage=true url=true}" rel="ia_lightbox">
								{printImage imgfile=$item.image}
							</a>

							<div class="caption">
								<a class="btn btn-small btn-danger" href="javascript:void(0);" title="{lang key='delete'}" onclick="return intelli.admin.removeFile('{$item.image}',this,'portfolio_entries','image','{$id}')"><i class="i-remove-sign"></i></a>
							</div>
						</div>
					{/if}

					<div class="image-upload">
						{ia_html_file name='image' id='image-input'}
						<input class="image-src" name="image-src" type="hidden">
						<input class="image-data" name="image-data" type="hidden">
					</div>
				</div>
			</div>

			{if $core.config.portfolio_use_crop}
				<div class="row">
					<label for="" class="col col-lg-2"></label>
					<div class="col col-lg-8">
						<div class="row">
							<div class="col-lg-5">
								<div class="image-preview image-preview--md" style="width:{$core.config.portfolio_thumbnail_width}px;height:{$core.config.portfolio_thumbnail_height}px;overflow:hidden;">
									{if isset($item.image) && $item.image}
										<div class="input-group thumbnail thumbnail-single with-actions">
											<a href="{printImage imgfile=$item.image fullimage=true url=true}" rel="ia_lightbox">
												{printImage imgfile=$item.image}
											</a>

											<div class="caption">
												<a class="btn btn-small btn-danger" href="javascript:void(0);" title="{lang key='delete'}" onclick="return intelli.admin.removeFile('{$item.image}',this,'portfolio_entries','image','{$id}')"><i class="i-remove-sign"></i></a>
											</div>
										</div>
									{/if}
								</div>
							</div>
							<div class="col-lg-7">
								<div class="image-wrapper"></div>
							</div>
						</div>
					</div>
				</div>
			{/if}
		</div>

		{capture name='systems' append='fieldset_before'}
			<div class="row">
				<label class="col col-lg-2 control-label" for="input-language">{lang key='language'}</label>
				<div class="col col-lg-4">
					<select name="lang" id="input-language"{if count($core.languages) == 1} disabled{/if}>
						{foreach $core.languages as $code => $language}
							<option value="{$code}"{if $item.lang == $code} selected{/if}>{$language.title}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/capture}

		{include file='fields-system.tpl'}
	</div>
</form>

{ia_add_media files='tagsinput, css:_IA_URL_modules/portfolio/templates/admin/css/style, js:_IA_URL_modules/portfolio/js/cropper/cropper.min, js:_IA_URL_modules/portfolio/js/admin/crop, css:_IA_URL_modules/portfolio/js/cropper/cropper, js:_IA_URL_modules/portfolio/js/admin/portfolio'}