<br /><div id="logo"></div>
<br /><br />
<fieldset id="login" style="width:250px;">
	<legend>Login</legend>
<form class="textCenter" method="post" action="">
	<input type="hidden" name="url" value="<?php if (!isset($_GET['p']) || (isset($_GET['p']) && $_GET['p'] != 'a')) { echo misc::url(); } ?>" />
	User<br />
	<img src="<?php echo $CONFIG['icon']['login']['user']; ?>" alt="" /> <input type="text" id="user" name="user" value="<?php if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['virgin'])) { echo $_SESSION[$_SERVER['REMOTE_ADDR']]['virgin']; } ?>" />
	<br />Password<br />
	<img src="<?php echo $CONFIG['icon']['login']['password']; ?>" alt="" /> <input type="password" name="password" value="<?php if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['virgin'])) { echo '1234'; } ?>" />
	<br /><br />
	<input class="button" type="submit" value="Login" />
<?php

if (isset($status))
{
	echo '<br /><br /><div class="error">'.$status.'</div>';
}

?>
</form>
<script type="text/javascript">$('#user').focus();</script>
</fieldset>
