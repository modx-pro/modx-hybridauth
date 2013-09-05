<div class="hybridauth">
	<img src="[[+gravatar]]?s=75" alt="[[+username]]" title="[[+fullname]]"  class="ha-avatar" />

	<span class="ha-info">
		[[%ha.greeting]] <b>[[+username]]</b> ([[+fullname]])! <a href="[[+logout_url]]">[[%ha.logout]]</a>
		<br/><br/>
		<small>[[%ha.providers_available]]</small><br/>
		[[+providers]]
	</span>

</div>

[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]
