<form action="[[~[[*id]]]]" method="post" class="form-horizontal">
	<div class="control-group[[+error.username:notempty=` error`]]">
		<label class="control-label">Имя</label>
		<div class="controls">
			<input type="text" name="username" value="[[+username]]" />
			<span class="help-inline">[[+error.username]]</span>
		</div>
	</div>
	
	<div class="control-group[[+error.fullname:notempty=` error`]]">
		<label class="control-label">Полное имя</label>
		<div class="controls">
			<input type="text" name="fullname" value="[[+fullname]]" />
			<span class="help-inline">[[+error.fullname]]</span>
		</div>
	</div>
	
	<div class="control-group[[+error.email:notempty=` error`]]">
		<label class="control-label">Email</label>
		<div class="controls">
			<input type="text" name="email" value="[[+email]]" />
			<span class="help-inline">[[+error.email]]</span>
		</div>
	</div>
	<input type="hidden" name="action" value="updateProfile" />
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Обновить</button>
	</div>
</form>
[[+success:is=`1`:then=`<div class="alert alert-block">Профиль успешно обновлен</div>`]]
[[+success:is=`0`:then=`<div class="alert alert-block alert-error">Ошибка при обновлении профиля</div>`]]