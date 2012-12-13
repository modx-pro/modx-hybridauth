var HybridAuth = function(config) {
	config = config || {};
	HybridAuth.superclass.constructor.call(this,config);
};
Ext.extend(HybridAuth,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('hybridauth',HybridAuth);

HybridAuth = new HybridAuth();