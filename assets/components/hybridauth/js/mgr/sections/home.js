Ext.onReady(function() {
	MODx.load({ xtype: 'hybridauth-page-home'});
});

HybridAuth.page.Home = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'hybridauth-panel-home'
			,renderTo: 'hybridauth-panel-home-div'
		}]
	}); 
	HybridAuth.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(HybridAuth.page.Home,MODx.Component);
Ext.reg('hybridauth-page-home',HybridAuth.page.Home);