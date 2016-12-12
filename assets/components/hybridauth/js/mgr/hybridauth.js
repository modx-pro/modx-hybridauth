var HybridAuth = function (config) {
    config = config || {};
    HybridAuth.superclass.constructor.call(this, config);
};

Ext.extend(HybridAuth, Ext.Component, {
    grid: {},
    combo: {},
    window: {},
    config: {},
    utils: {}
});
Ext.reg('HybridAuth', HybridAuth);

HybridAuth = new HybridAuth();
