{if !empty($entry)}
    <div class="portfolio-entry">

        <p class="text-fade-50">{lang key='posted_on'} {$entry.date_added|date_format:$core.config.date_format} {lang key='by'} {$entry.fullname}</p>

        {if !empty($entry.gallery)}
            <div class="news-item__section m-b">
                {ia_add_media files='fotorama'}
                <div class="news-item__gallery">
                    <div class="fotorama"
                         data-nav="thumbs"
                         data-width="100%"
                         data-ratio="800/400"
                         data-allowfullscreen="true"
                         data-fit="cover">
                        {foreach $entry.gallery as $item}
                            <a class="news-item__gallery__item" href="{ia_image file=$item type='large' url=true}">{ia_image file=$item type='large'}</a>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}

        <div class="m-b">
            {$entry.body}
        </div>

        <p><span class="fa fa-folder-open"></span> <a href="{$entry.link}">{$category.title|escape}</a></p>

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
{/if}