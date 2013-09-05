<img src="[[+gravatar]]?s=50" align="left" alt="username"/>
[[%ha.greeting]] <b>[[+username]]</b> ([[+fullname]])!
<br/>
<a href="[[+logout_url]]">[[%ha.logout]]</a>
[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]

<br><br>
[[%ha.providers_available]]
[[+yandex.provider:is=``:then=`<a href="[[+login_url]]&provider=Yandex">Yandex</a>`:else=`Yandex`]],
[[+google.provider:is=``:then=`<a href="[[+login_url]]&provider=Google">Google</a>`:else=`Google`]],
[[+twitter.provider:is=``:then=`<a href="[[+login_url]]&provider=Twitter">Twitter</a>`:else=`Twitter`]]
