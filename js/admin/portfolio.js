Ext.onReady(function()
{
	var pageUrl = intelli.config.admin_url + '/portfolio/';

	if (Ext.get('js-grid-placeholder'))
	{
		var urlParam = intelli.urlVal('status');

		intelli.portfolio =
		{
			columns: [
				'selection',
				{name: 'title', title: _t('title'), width: 2, editor: 'text'},
				{name: 'alias', title: _t('title_alias'), width: 220},
				'status',
				{name: 'date_added', title: _t('date'), width: 120, editor: 'date'},
				'update',
				'delete'
			],
			sorters: [{property: 'date_added', direction: 'DESC'}],
			storeParams: urlParam ? {status: urlParam} : null,
			url: pageUrl
		};
		intelli.portfolio = new IntelliGrid(intelli.portfolio, false);
		intelli.portfolio.toolbar = Ext.create('Ext.Toolbar', {items:[
		{
			emptyText: _t('text'),
			name: 'text',
			listeners: intelli.gridHelper.listener.specialKey,
			width: 275,
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
			handler: function(){intelli.gridHelper.search(intelli.portfolio);},
			id: 'fltBtn',
			text: '<i class="i-search"></i> ' + _t('search')
		}, {
			handler: function(){intelli.gridHelper.search(intelli.portfolio, true);},
			text: '<i class="i-close"></i> ' + _t('reset')
		}]});

		if (urlParam)
		{
			Ext.getCmp('fltStatus').setValue(urlParam);
		}

		intelli.portfolio.init();
	}
	else
	{
		$('#input-title, #input-alias').on('blur', function()
		{
			var alias = $('#input-alias').val();
			var title = alias != '' ? alias : $('#input-title').val();

			if ('' != title)
			{
				$.get(pageUrl + 'read.json', {get: 'alias', title: title}, function(data)
				{
					if ('' != data.url)
					{
						$('#title_url').text(data.url);
						$('#title_box').fadeIn();
					}
					else
					{
						$('#title_box').hide();
					}
				});
			}
			else
			{
				$('#title_box').hide();
			}
		});

		$('#tags').tagsInput({width: '100%', height: 'auto'});
	}
});