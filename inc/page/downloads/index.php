<div>
<img src="<?php echo $CONFIG['icon']['downloads']['stop']; ?>" alt="STOP" class="disabled" title="Stop" id="stop" onclick="if ($(this).hasClass('hand')) { torrentAction('stop'); }" />
<img src="<?php echo $CONFIG['icon']['downloads']['start']; ?>" alt="START" class="disabled" title="Start" id="start" onclick="if ($(this).hasClass('hand')) { torrentAction('start'); }" />
<img src="<?php echo $CONFIG['icon']['downloads']['stopAll']; ?>" alt="STOP ALL" class="hand" title="Stop all" id="stopAll" onclick="if ($(this).hasClass('hand')) { torrentAction('stopAll'); }" />
<img src="<?php echo $CONFIG['icon']['downloads']['startAll']; ?>" alt="START ALL" class="hand" title="Start all" id="startAll" onclick="if ($(this).hasClass('hand')) { torrentAction('startAll'); }" />
<img src="<?php echo $CONFIG['icon']['downloads']['delete']; ?>" alt="DELETE" class="disabled" title="Delete" id="delete" onclick="if ($(this).hasClass('hand')) { torrentAction('delete'); }" />
</div>
<script type="text/javascript">

$('#content').append('<table><thead><tr><td>Name</td><td>Size</td><td>Done</td><td>Status</td><td>Downloaded</td><td>Uploaded</td><td>Down</td><td>Up</td><td>Seeds</td><td>Leech</td><td>Ratio</td><td>ETA</td></tr></thead><tbody id="downloadsTableBody"></tbody><tfoot><tr id="downloadsTableFooter"></tr></tfoot></table>');
updateDownloadsContent('<?php echo json_encode($status); ?>');

setTimeout("updateDownloads()",5000);

$(document).ready(function() {
	$('#downloadsTableBody tr').click(function(event)
	{
		if ($('body').css('cursor') == 'progress')	// don't want to do anything if some loading is going on
		{
			return;
		}
		$(this).toggleClass("selected");
		var button = ["stop","start","delete"];
		if ($('.selected').length == 0)
		{
			for (i in button)
			{
				$('#'+button[i]).removeClass("hand");
				$('#'+button[i]).addClass("disabled");
			}
		}
		else
		{
			for (i in button)
			{
				$('#'+button[i]).addClass("hand");
				$('#'+button[i]).removeClass("disabled");
			}
		}
	});
	
	// right click is not yet implemented
	/*$('#downloadsTableBody').mousedown(function(event) 
	{
		if(event.which == 3)
    		{
			$('.selected').each(function(key,val)
		{
			alert($(val).attr('id'));
		});
		}
	});*/
});
</script>

