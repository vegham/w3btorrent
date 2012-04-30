<div>
<img src="<?php echo $CONFIG['icon']['files']['delete']; ?>" alt="DELETE" class="disabled" title="Delete" id="delete" onclick="if ($(this).hasClass('hand')) { fileAction('delete'); }" />
<br /><br />
</div>
<div id="tree"></div>
<table><thead><tr><td>Name</td><td>Size</td><td>Date</td></tr></thead><tbody id="filesTableBody"></tbody></table>
<script type="text/javascript">
function updateFilesPage(data)
{
	var json = jQuery.parseJSON(data);
	
	// show tree
	var html = '';
	$.each(json[0],function(num,branch)
	{
		html += '<a href="'+branch['href']+'" onclick="return pf(this.href);">'+branch['name']+'</a> / ';
	});
	$('#tree').html(html);
	
	var html = '';
	$.each(json[1],function(num,dir)
	{
		html += '<tr id="'+dir['path']+'">';
		html += '<td><img src="'+dir['icon']+'" alt="" />';
		if (dir['href'] != '')
		{
			html += '<a href="'+dir['href']+'" onclick="return pf(this.href);">'+dir['name']+'</a>';
		}
		else
		{
			html += '<a href="javascript:void(0);" onclick="alert(\'Permission denied.\');">'+dir['name']+'</a>';
		}
		html += '</td><td>--</td><td></td>';
		html += '</tr>';
	});
	$.each(json[2],function(num,file)
	{
		html += '<tr id="'+file['path']+'">';
		html += '<td><img src="'+file['icon']+'" alt="" title="'+file['mime']+'" />';
		if (file['href'] != '')
		{
			html += '<a href="'+file['href']+'">'+file['name']+'</a>';
		}
		else
		{
			html += file['name'];
		}
		html += '</td><td>'+file['size']+'</td>';
		html += '<td>'+file['date']+'</td>';
		html += '</tr>';
	});
	$('#filesTableBody').html(html);
}

function pf(href)
{
	$('body').css('cursor','progress');
	$.ajax({
		url:href,
		type: 'POST',
		data: {ajax:1, browse:1},
		timeout: 5000,
		error: function(request,error)
		{
			$('body').css('cursor','auto');	
		},
		success: function(json)
		{
			$('body').css('cursor','auto');
			if (json != "")
			{
				$('#delete').removeClass("hand");
				$('#delete').addClass("disabled");
				updateFilesPage(json);
				makeFilesTableClickable();
			}
		}
	});
	return false;
}

updateFilesPage('<?php echo json_encode($list); ?>');

function makeFilesTableClickable()
{
	$('#filesTableBody tr').click(function(event)
	{
		if ($('body').css('cursor') == 'progress')	// don't want to do anything if some loading is going on
		{
			return;
		}
		$(this).toggleClass("selected");

		if ($('.selected').length == 0)
		{
			$('#delete').removeClass("hand");
			$('#delete').addClass("disabled");
		}
		else
		{
			$('#delete').addClass("hand");
			$('#delete').removeClass("disabled");
		}
	});
}

$(document).ready(makeFilesTableClickable());


function fileAction(type)
{
	$('body').css('cursor','progress');
	var selected = serializeSelected(type);

	$('#delete').removeClass("hand");
	$('#delete').addClass("disabled");
	if (type == 'delete')
	{
		$('.selected').fadeOut(500);
	}
	$.ajax({
		url:'?p=f&'+selected,
		type: 'POST',
		data: {ajax:1, refresh:1, action:type},
		timeout: 5000,
		error: function(request,error)
		{
			alert(error);
			$('body').css('cursor','auto');
			$('#delete').addClass("hand");
			$('#delete').removeClass("disabled");
		},
		success: function(json)
		{
			$('body').css('cursor','auto');
			$('#delete').addClass("hand");
			$('#delete').removeClass("disabled");
			//updateFilesPage(json);
			makeFilesTableClickable();
		}
	});
}
</script>