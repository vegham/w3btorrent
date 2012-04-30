<form id="account" method="post" action="?p=s">
	<div class="topic">Accounts (required)<hr /></div>
	<div class="belowSpace">
		<div class="col1"> &nbsp; &nbsp; <img src="<?php echo $CONFIG['icon']['account']['user']; ?>" alt="" /> Select user</div>
		<div><select name="id" id="accountList" onchange="var option=this.options[this.selectedIndex];$('#username').val(option.label);document.getElementById('type').selectedIndex = option.innerHTML;$('#password').val('');"><option></option>
<?php

foreach (account::userList() as $user)
{
	echo '<option value="'.$user[0].'" label="'.$user[1].'" '.($_SESSION[$_SERVER['REMOTE_ADDR']]['account']['username']==$user[1]?"disabled":"").'>'.$user[2].'</option>';
}

?>
</select></div></div>
<div>
	<div class="col1"> &nbsp; &nbsp; <img src="<?php echo $CONFIG['icon']['account']['type']; ?>" alt="" /> Account type</div>
	<div>
	<select name="type" id="type">
		<option value="0">Admin</option>
		<option value="1">User</option>
	</select>
	</div>
	<div class="belowSpace">
		<div class="col1"> &nbsp; &nbsp; <img src="<?php echo $CONFIG['icon']['account']['username']; ?>" alt="" /> Username</div>
		<div><input type="text" name="username" id="username" value="<?php echo (isset($_POST['username'])?$_POST['username']:''); ?>" /></div>
	</div>
	<div class="belowSpace">
		<div class="col1"> &nbsp; &nbsp; <img src="<?php echo $CONFIG['icon']['account']['password']; ?>" alt="" /> Password</div>
		<div><input type="password" id="password" name="password" /></div>
	</div>
	<div>
		<div class="col1"><img src="<?php echo $CONFIG['icon']['account']['password']; ?>" alt="" /><img src="<?php echo $CONFIG['icon']['account']['password']; ?>" alt="" /> Password again</div>
		<div><input type="password" id="passwordAgain" name="passwordAgain" /></div>
	</div>
	
	</div>
	<br />
	
	<span><input name="add" id="submitUserAdd" type="submit" value="Add new user" onclick="return submitForm(['submitUserAdd','submitUserUpdate','submitUserDelete'],'account','accountError','&add=1');" /><input id="submitUserUpdate" type="submit" value="Update user" name="update" onclick="return submitForm(['submitUserAdd','submitUserUpdate','submitUserDelete'],'account','accountError','&update=1');" /><input id="submitUserDelete" type="button" value="Delete user" name="delete" onclick="submitForm(['submitUserAdd','submitUserUpdate','submitUserDelete'],'account','accountError','&delete=1');$('#accountList option:selected').remove();"  /></span>
	<span class="textLeft error" id="accountError"><?php if (isset($status['account'])) { echo $status['account']; } ?></span>
	<br /><br />
</form>



</fieldset>
