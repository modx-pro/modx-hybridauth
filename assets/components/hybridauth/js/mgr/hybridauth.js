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

Ext.ComponentMgr.onAvailable("modx-user-tabs", function() {
    this.on("beforerender", function() {
        this.add({
            title: _("ha.services"),
            border: false,
            items: [{
                layout: "anchor",
                border: false,
                items: [{
                    html: _("ha.services_tip"),
                    border: false,
                    bodyCssClass: "panel-desc"
                }, {
                    xtype: "hybridauth-grid-services",
                    anchor: "100%",
                    cls: "main-wrapper",
                    userId: HybridAuth.config.user_id
                }]
            }]
        });
    });
});
