<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
    {preventCsrf}

    {if iaCore::ACTION_EDIT == $pageAction}
        <input id="input-id" type="hidden" value="{$id}">
    {/if}

    {if $item.parent_id != iaCategories::ROOT_PARENT_ID}
        {capture name='general' append='fieldset_before'}
            {include 'tree.tpl'}
        {/capture}

        {capture name='title' append='field_after'}
            <div class="row">
                <label class="col col-lg-2 control-label">{lang key='title_alias'}</label>
                <div class="col col-lg-4">
                    <input type="text" name="title_alias" id="js-field-alias" value="{$item.title_alias|escape}">
                    <p class="help-block text-break-word">{lang key='page_url_will_be'}: <span class="text-danger" id="title_url">{$smarty.const.IA_URL}</span></p>
                </div>
            </div>
        {/capture}
    {/if}

    {include 'field-type-content-fieldset.tpl' isSystem=true datetime=true}
</form>
{ia_add_media files='js:_IA_URL_modules/portfolio/js/admin/categories'}