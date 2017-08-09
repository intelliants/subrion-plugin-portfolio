Ext.onReady(function () {
    if (Ext.get('js-grid-placeholder')) {
        var urlParam = intelli.urlVal('status');

        intelli.portfolio =
        {
            columns: [
                'selection',
                {name: 'gallery', title: _t('field_portfolio_gallery'), icon: 'image', click: function(record, field)
                {
                    $.fancybox.open(record.get(field));
                }},
                {name: 'title', title: _t('title'), width: 1, editor: 'text'},
                {name: 'title_alias', title: _t('path'), width: 1},
                {name: 'category', title: _t('category'), width: 1},
                {name: 'date_added', title: _t('date_added'), width: 170, editor: 'date'},
                {name: 'date_modified', title: _t('date_modified'), width: 170, hidden: true},
                {name: 'order', title: _t('order'), width: 50, editor: 'text'},
                'status',
                'update',
                'delete'
            ],
            storeParams: urlParam ? {status: urlParam} : null,
            texts: {
                delete_multiple: _t('are_you_sure_to_delete_selected_entries'),
                delete_single: _t('are_you_sure_to_delete_entry')
            }
        };

        intelli.portfolio = new IntelliGrid(intelli.portfolio, false);
        intelli.portfolio.toolbar = Ext.create('Ext.Toolbar', {
            items: [
                {
                    emptyText: _t('text'),
                    name: 'text',
                    listeners: intelli.gridHelper.listener.specialKey,
                    xtype: 'textfield'
                }, {
                    displayField: 'title',
                    editable: false,
                    emptyText: _t('status'),
                    id: 'fltStatus',
                    name: 'status',
                    store: intelli.portfolio.stores.statuses,
                    typeAhead: true,
                    valueField: 'value',
                    xtype: 'combo'
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.portfolio);
                    },
                    id: 'fltBtn',
                    text: '<i class="i-search"></i> ' + _t('search')
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.portfolio, true);
                    },
                    text: '<i class="i-close"></i> ' + _t('reset')
                }
            ]
        });

        if (urlParam) {
            Ext.getCmp('fltStatus').setValue(urlParam);
        }

        intelli.portfolio.init();
    }
});

intelli.fillUrlBox = function () {
    var slug = $('input[name="title_alias"]').val();
    var title = ('' == slug ? $('#field_portfolio_title').val() : slug);
    var id = $('#entry-id').val();
    var category = $('#input-tree').val();
    var params = {title: title, category: category, id: id};

    if ('' != slug) params.slug = 1;

    $.get(intelli.config.admin_url + '/portfolio/slug.json', params, function (data) {
        if ('' != data.data) {
            $('#title_url').html('<a href="' + data.data + '" target="_blank">' + data.data + '</a>');
            $('#title_box').fadeIn();
        }
    });
};

$(function () {
    $('#field_portfolio_title').keyup(function () {
        $('#tr_title_alias').show();
    });

    $('#field_portfolio_title, input[name="title_alias"]').blur(intelli.fillUrlBox).blur();
});