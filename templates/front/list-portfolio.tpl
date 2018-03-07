<div class="col-md-3">
    <div class="ia-item ia-item--card m-b">
        {if $entry.gallery}
            <a href="{$entry.link}" class="ia-item__image">
                {ia_image file=$entry.gallery|array_shift type='thumbnail' title=$entry.title|escape}
            </a>
        {/if}

        <div class="ia-item__content">
            <h4 class="ia-item__title text-center">
                <a href="{$entry.item}/{$entry.category_alias}/{$entry.id}-{$entry.title_alias}.html">{$entry.title|escape}</a>
            </h4>
        </div>
    </div>
</div>