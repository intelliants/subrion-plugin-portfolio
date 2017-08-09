Ext.onReady(function () {
    if (Ext.get('js-grid-placeholder')) {
        intelli.categ = new IntelliGrid
        (
            {
                columns: [
                    'selection',
                    {name: 'title', title: _t('title'), width: 2, editor: 'text'},
                    {name: 'title_alias', title: _t('path'), width: 1},
                    {name: 'num_listings', title: _t('entries'), width: 70},
                    {name: 'num_all_listings', title: _t('total_entries'), width: 80},
                    {name: 'order', title: _t('order'), width: 60, editor: 'number'},
                    'status',
                    'update',
                    'delete'
                ],
                texts: {
                    delete_multiple: _t('are_you_sure_to_delete_selected_categs'),
                    delete_single: _t('are_you_sure_to_delete_this_categ')
                }
            }, false
        );

        intelli.categ.toolbar = new Ext.Toolbar({
            items: [
                {
                    emptyText: _t('title'),
                    listeners: intelli.gridHelper.listener.specialKey,
                    name: 'title',
                    width: 250,
                    xtype: 'textfield'
                }, {
                    displayField: 'title',
                    editable: false,
                    emptyText: _t('status'),
                    name: 'status',
                    store: intelli.categ.stores.statuses,
                    typeAhead: true,
                    valueField: 'value',
                    width: 100,
                    xtype: 'combo'
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.categ)
                    },
                    id: 'fltBtn',
                    text: '<i class="i-search"></i> ' + _t('search')
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.categ, true)
                    },
                    text: '<i class="i-close"></i> ' + _t('reset')
                }
            ]
        });

        intelli.categ.init();
    }
});

intelli.titleCache = '';
intelli.fillUrlBox = function () {
    var id = $('#input-id').val();
    var title_alias = $('input[name="title_alias"]').val();
    var title = ('' == title_alias ? $('#field_portfolio_categs_title').val() : title_alias);
    var category = $('#input-tree').val();
    var cache = title + '%%' + category;

    if ('' != title && intelli.titleCache != cache) {
        var params = {action: 'alias', title: title, category: category, id: id};

        if ('' != title_alias) params.alias = 1;

        $.getJSON(intelli.config.admin_url + '/portfolio/categories/alias.json', params, function (response) {
            if ('' != response.data) {
                var $existsNotificationBox = $('#js-exists-warning');

                $('#title_url').html(response.data);
                $('#title_box').fadeIn();

                if (typeof response.exists != 'undefined') {
                    if (!$existsNotificationBox.length) {
                        $('<span>')
                            .attr({id: 'js-exists-warning', 'class': 'alert alert-info'})
                            .css({display: 'block', marginTop: '8px'})
                            .text(response.exists)
                            .appendTo($('#js-field-alias').parent());
                    }
                }
                else {
                    $existsNotificationBox.remove();
                }
            }
        });
    }

    intelli.titleCache = cache;
};

$(function () {
    $('#field_portfolio_categs_title').keyup(function () {
        $('#tr_title_alias').show();
    });

    $('#field_portfolio_categs_title, input[name="title_alias"]').on('blur', intelli.fillUrlBox).trigger('blur');
});