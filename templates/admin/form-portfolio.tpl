<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
    {preventCsrf}

    {if $id}
        <input type="hidden" id="entry-id" value="{$id}">
    {/if}

    {capture name='title' append='field_after'}
        <div id="title_alias" class="row">
            <label class="col col-lg-2 control-label">{lang key='title_alias'}</label>
            <div class="col col-lg-4">
                <input type="text" name="title_alias" id="field_title_alias"{if isset($item.title_alias)} value="{$item.title_alias}"{/if}>
                <p class="help-block text-break-word">{lang key='page_url_will_be'}: <span class="text-danger" id="title_url"></span></p>
            </div>
        </div>
    {/capture}

    {capture name='general' append='fieldset_before'}
        {include 'tree.tpl' tree=$categoryTree}
    {/capture}

    {include 'field-type-content-fieldset.tpl' isSystem=true datetime=true}
</form>

{ia_add_media files='js:_IA_URL_modules/portfolio/js/admin/portfolio'}