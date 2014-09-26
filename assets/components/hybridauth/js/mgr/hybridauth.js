var HybridAuth = function (config) {
	config = config || {};
	HybridAuth.superclass.constructor.call(this, config);
};

Ext.extend(HybridAuth, Ext.Component, {
	grid: {},
	combo: {},
	window: {},
	config: {}
});
Ext.reg('HybridAuth', HybridAuth);

HybridAuth = new HybridAuth();
