HybridAuth.grid.Services = function (config) {
    config = config || {};
    var baseParams = {
        action: 'mgr/service/getlist'
    };
    if (config.userId) {
        baseParams.user_id = config.userId;
    }
    Ext.applyIf(config, {
        url: HybridAuth.config.connectorUrl,
        id: 'ha-row',
        baseParams: baseParams,
        remoteSort: true,
        paging: true,
        preventRender: true,
        sm: new Ext.grid.CheckboxSelectionModel(),
        fields: this.getFields(config),
        columns: this.getColumns(config)
    });
    HybridAuth.grid.Services.superclass.constructor.call(this, config);
};
Ext.extend(HybridAuth.grid.Services, MODx.grid.Grid, {

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = HybridAuth.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    getFields: function () {
        return [
            'id',
            'hash',
            'createdon',
            'provider',
            'identifier',
            'profileurl',
            'photourl',
            'displayname',
            'email',
            'actions'
        ]
    },

    getColumns: function () {
        return [{
            header: '',
            dataIndex: 'hash',
            sortable: false,
            width: 45,
            renderer: this._renderAvatar,
            id: 'avatar'
        }, {
            header: _('ha.service_createdon'),
            dataIndex: 'createdon',
            sortable: true
        }, {
            header: _('ha.service_provider'),
            dataIndex: 'provider',
            sortable: true
        }, {
            header: _('ha.service_identifier'),
            dataIndex: 'identifier',
            sortable: true,
            renderer: this._renderProfileUrl
        }, {
            header: _('ha.service_displayname'),
            dataIndex: 'displayname',
            sortable: true
        }, {
            header: _('ha.service_email'),
            dataIndex: 'email',
            sortable: true,
            renderer: this._renderEmail
        }, {
            header: '',
            dataIndex: 'actions',
            renderer: HybridAuth.utils.renderActions,
            sortable: false,
            width: 35,
            id: 'actions'
        }];
    },

    removeItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
                ? _('ha.services_remove')
                : _('ha.service_remove'),
            text: ids.length > 1
                ? _('ha.services_remove_confirm')
                : _('ha.service_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/service/remove',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    _renderAvatar: function (v, md, record) {
        return '<img src="//gravatar.com/avatar/' + v + '?d=' +
            encodeURIComponent(record.get('photourl')) + '&s=80&d=mm"/>';
    },

    _renderProfileUrl: function (v, md, record) {
        var url = record.get('profileurl').trim();
        if (url === '') {
            return v;
        }
        return '<a href="' + url + '" target="_blank" style="color:#428bca;">' + v + '</a>';
    },

    _renderEmail: function (v) {
        return '<a href="mailto:' + v + '" style="color:#428bca;">' + v + '</a>';
    }
});
Ext.reg('hybridauth-grid-services', HybridAuth.grid.Services);
