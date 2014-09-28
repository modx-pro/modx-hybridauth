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
		baseParams: baseParams,
		remoteSort: true,
		paging: true,
		autoExpandColumn: 'name',
		preventRender: true,
		fields: [
			'id',
			'hash',
			'createdon',
			'provider',
			'identifier',
			'profileurl',
			'photourl',
			'displayname',
			'email'
		],
		columns: [{
			header: _('ha.service_avatar'),
			dataIndex: 'hash',
			sortable: false,
			width: 45,
			renderer: this.renderAvatar
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
			renderer: this.renderProfileUrl
		}, {
			header: _('ha.service_displayname'),
			dataIndex: 'displayname',
			sortable: true
		}, {
			header: _('ha.service_email'),
			dataIndex: 'email',
			sortable: true,
			renderer: this.renderEmail
		}]
	});
	HybridAuth.grid.Services.superclass.constructor.call(this, config);
};
Ext.extend(HybridAuth.grid.Services, MODx.grid.Grid, {
	renderAvatar: function (v, md, record) {
		return '<img src="' + document.location.protocol + '//gravatar.com/avatar/' + v + '?d=' + encodeURIComponent(record.get('photourl')) + '&s=30" height="30" style="display: block; margin: auto"/>';
	},
	renderProfileUrl: function (v, md, record) {
		var url = record.get('profileurl').trim();
		if (url === '') {
			return v;
		}
		return '<a href="' + url + '" target="_blank" style="color:#428bca;">' + v + '</a>';
	},
	renderEmail: function (v) {
		return '<a href="mailto:' + v + '" style="color:#428bca;">' + v + '</a>';
	}
});
Ext.reg('hybridauth-grid-services', HybridAuth.grid.Services);
