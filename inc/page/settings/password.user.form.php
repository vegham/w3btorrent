<form id="passwordForm" method="post" action="?p=s">
	<div class="topic">Change password<hr /></div>
	<div class="belowSpace">
		<div class="col1"> &nbsp; &nbsp; <img src="pix/key.png" alt="" /> Old password</div>
		<div><input type="password" name="passwordOld" /></div>
	</div>
	<div class="belowSpace">
		<div class="col1"> &nbsp; &nbsp; <img src="pix/key.png" alt="" /> Password</div>
		<div><input type="password" name="passwordNew" /></div>
	</div>
	<div>
		<div class="col1"><img src="pix/key.png" alt="" /><img src="pix/key.png" alt="" /> Password again</div>
		<div><input type="password" name="passwordNewAgain" /></div>
	</div>
	<br />
	<span class="textRight"><input id="passwordSubmit" type="submit" value="Update" onclick="return submitForm(['passwordSubmit'],'passwordForm','passwordStatus');" /></span>
	<span class="textLeft status" id="passwordStatus"><?php if (isset($status['password'])) { echo $status['password']; } ?></span>
</form>
