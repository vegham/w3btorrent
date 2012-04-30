<form id="mysql" method="post" action="?p=s">
	<div class="topic">MySQL - store everything in database (optional)<hr /></div>
	<div class="belowSpace">
		<div class="col1"><img src="<?php echo $CONFIG['icon']['mysql']['enabled']; ?>" alt="" /> Enabled</div>
		<div><input type="checkbox" name="enabled" onclick="$('#enabled').html('Dataloss may occur when changing config.').fadeOut(3000);" <?php echo (isset($cfg->mysql->enabled)&&(string)$cfg->mysql->enabled==1?'checked':''); ?> /><span id="enabled" class="status"></span></div>
	</div>
	<div class="belowSpace">
		<div class="col1"><img src="<?php echo $CONFIG['icon']['mysql']['hostname']; ?>" alt="" /> Hostname <img src="<?php echo $CONFIG['icon']['mysql']['tip']; ?>" alt="" title="Suggestion" onclick="$('#mysqlHostname').val('localhost');" class="hand" /></div>
		<div><input type="text" name="hostname" id="mysqlHostname" value="<?php echo (isset($cfg->mysql->hostname)?(string)$cfg->mysql->hostname:''); ?>" /></div>
	</div>
	<div class="belowSpace">
		<div class="col1"><img src="<?php echo $CONFIG['icon']['mysql']['port']; ?>" alt="" /> Port <img src="<?php echo $CONFIG['icon']['mysql']['tip']; ?>" alt="" title="Suggestion" onclick="$('#mysqlPort').val('3306');" class="hand" /></div>
		<div><input type="port" name="port" id="mysqlPort" value="<?php echo (isset($cfg->mysql->port)?(string)$cfg->mysql->port:''); ?>" /></div>
	</div>
	<div class="belowSpace">
		<div class="col1"><img src="<?php echo $CONFIG['icon']['mysql']['username']; ?>" alt="" /> Username</div>
		<div><input type="text" name="username" value="<?php echo (isset($cfg->mysql->username)?(string)$cfg->mysql->username:''); ?>" /></div>
	</div>
	<div class="belowSpace">
		<div class="col1"><img src="<?php echo $CONFIG['icon']['mysql']['password']; ?>" alt="" /> Password</div>
		<div><input type="password" name="password" value="<?php echo (isset($cfg->mysql->password)?(string)$cfg->mysql->password:''); ?>" /></div>
	</div>
	<div class="belowSpace">
		<div class="col1"><img src="<?php echo $CONFIG['icon']['mysql']['db']; ?>" alt="" /> Database</div>
		<div><input type="text" name="db" value="<?php echo (isset($cfg->mysql->db)?(string)$cfg->mysql->db:''); ?>" /></div>
	</div>
	<span class="textLeft"><input id="submitMysql" type="submit" value="Apply" onclick="return submitForm(['submitMysql'],'mysql','mysqlError');" /></span>
	<span id="mysqlError" class="textLeft error"><?php if (isset($status['mysql'])) { echo $status['mysql']; } ?></span>
</form>
