<div class="row">
    <label class="col col-lg-2 control-label">{lang key='recount_portfolio_entries'}</label>
    <div class="col col-lg-1">
        <button type="button" class="btn btn-success btn-small js-start-maintenance-cmd" data-action="recount_entries" data-module="categories">{lang key='start'}</button>
    </div>
    <div class="col col-lg-2">
        <div class="progress progress-striped hidden" id="js-recount_entries-progress">
            <div class="progress-bar progress-bar-success" style="width: 0"></div>
        </div>
    </div>
</div>
{ia_add_media files='js: _IA_URL_modules/portfolio/js/admin/consistency'}