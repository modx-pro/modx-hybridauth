[[+photo:notempty=`<img src="[[+photo]]" align="left" alt="username"/>`]]
Приветствую, <b>[[+username]]</b> ([[+fullname]])!
<br/>
<a href="[[+logout_url]]">Выйти</a>
[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]

<br><br>
Доступны сервисы:
[[+yandex.provider:is=``:then=`<a href="[[+login_url]]&provider=Yandex">Яндекс</a>`:else=`Яндекс`]],
[[+google.provider:is=``:then=`<a href="[[+login_url]]&provider=Google">Google</a>`:else=`Google`]] и
[[+twitter.provider:is=``:then=`<a href="[[+login_url]]&provider=Twitter">Twitter</a>`:else=`Twitter`]]
