HybridAuth.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>'+_('hybridauth')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,activeItem: 0
			,hideMode: 'offsets'
			,items: [{
				title: _('hybridauth.items')
				,items: [{
					html: _('hybridauth.intro_msg')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'hybridauth-grid-items'
					,preventRender: true
				}]
			}]
		}]
	});
	HybridAuth.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(HybridAuth.panel.Home,MODx.Panel);
Ext.reg('hybridauth-panel-home',HybridAuth.panel.Home);
