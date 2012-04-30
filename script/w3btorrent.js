function pt(obj) // Page Tabbing
{
	$('body').css('cursor','progress');
	$.ajax({
		url:obj,
		type: 'POST',
		data: {ajax:1},
		timeout: 5000,
		error: function(request,error)
		{
			p(obj);		
		},
		success: function(xml)
		{
			$('body').css('cursor','auto');
			$('#content').html(xml);
		}
	});
	$(ACTIVE_TAB).removeClass("active");
	ACTIVE_TAB = obj;
	$(obj).addClass("active");
	return false;
}


function p(href) // Pager
{
	$('body').css('cursor','progress');
	$.ajax({
		url:href,
		type: 'POST',
		data: {ajax:1},
		timeout: 5000,
		error: function(request,error)
		{
			p(href);		
		},
		success: function(xml)
		{
			$('body').css('cursor','auto');
			$('#content').html(xml);
		}
	});
	return false;
}

function submitForm(disableArray,argId,argErrorId,argGet)
{
	for (i in disableArray)
	{
		$('#'+disableArray[i]).attr('disabled',true);
	}
	$('#'+argErrorId).html('');
	$('body').css('cursor','progress');
	$.ajax({
		url:$('#'+argId).attr('action')+'&'+$('#'+argId).serialize()+'&'+argId+'=1&'+(argGet?argGet:''),
		type:$('#'+argId).attr('method'),
		data:$('#'+argId).serialize()+'&ajax=1&submit=1&'+argId+'=1'+argGet,
		timeout: 5000,
		error: function(request,error)
		{
			for (i in disableArray)
			{
				$('#'+disableArray[i]).attr('disabled',false);
			}
			$('body').css('cursor','auto');
			$('#'+argErrorId).html('Unknown error occured. Try again');
			//submitForm(argId,argErrorId,argGet);
			//alert(error);		
		},
		success: function(xml)
		{
			for (i in disableArray)
			{
				$('#'+disableArray[i]).attr('disabled',false);
			}
			$('body').css('cursor','auto');
			$('#'+argErrorId).html(xml);			
		}
	});
	return false;
}

//
// DOWNLOADS
//
function updateDownloadsContent(data)
{
	var json = jQuery.parseJSON(data);
	$.each(json,function(num,torrent)
	{
		var html = '';
		if ($('#'+torrent['hash']).html() == null)	// this row does not exist, let's add it
		{
			html += '<tr id="'+torrent['hash']+'">';
		}
		html += '<td>'+torrent['name']+'</td>';
		html += '<td>'+torrent['size_bytes']+'</td>';
		html += '<td>'+torrent['percent']+'%</td>';
		html += '<td>'+torrent['status']+'</td>';
		html += '<td>'+torrent['down_total']+'</td>';
		html += '<td>'+torrent['up_total']+'</td>';
		html += '<td>'+torrent['down_rate']+'</td>';
		html += '<td>'+torrent['up_rate']+'</td>';
		html += '<td>'+torrent['peers_complete']+'</td>';
		html += '<td>'+torrent['peers_connected']+'</td>';
		html += '<td>'+torrent['ratio']+'</td>';
		html += '<td>'+torrent['eta']+'</td>';
		
		if ($('#'+torrent['hash']).html() == null)	// this row does not exist, let's add it
		{
			html += '</tr>';
			$('#downloadsTableBody').append(html);
		}
		else
		{
			$('#'+torrent['hash']).html(html);
		}
	});
}

function updateDownloads()
{
	$.ajax({
		url:'?p=d',
		type: 'POST',
		data: {ajax:1, refresh:1},
		timeout: 5000,
		error: function(request,error)
		{
			setTimeout("updateDownloads()",5000);	// re-run in case of timeout or whatever
		},
		success: function(xml)
		{
			if ($('#downloadsTableBody').html() != null)
			{
				updateDownloadsContent(xml);
				setTimeout("updateDownloads()",5000);
			} // user changed page
		}
	});
}
function serializeSelected(argKey)
{
	var data = '';
	$('.selected').each(function(key,val)
	{
		if (key != 0)
		{
			data += '&';
		}
		data += argKey+'[]='+$(val).attr('id');
	});
	return data;
}
function unselectAll()
{
	$('.selected').each(function(key,val)
	{
		$(this).removeClass("selected");
	});
}
function torrentAction(type)
{
	var selected = serializeSelected('hash');
	if (type == 'delete')
	{
		$('.selected').fadeOut(500);
	}
	disableDownloads();
	$.ajax({
		url:'?p=d&'+selected,
		type: 'POST',
		data: {ajax:1, refresh:1, action:type},
		timeout: 5000,
		error: function(request,error)
		{
			enableDownloads();
		},
		success: function(json)
		{
			updateDownloadsContent(json);
			enableDownloads();
		}
	});
}
function enableDownloads()
{
	$('body').css('cursor','auto');
	unselectAll();
	var button = ["stopAll","startAll"];
	for (i in button)
	{
		$('#'+button[i]).addClass("hand");
		$('#'+button[i]).removeClass("disabled");
	}
}
function disableDownloads()
{
	$('body').css('cursor','progress');
	var button = ["stop","start","stopAll","startAll","delete"];
	for (i in button)
	{
		$('#'+button[i]).removeClass("hand");
		$('#'+button[i]).addClass("disabled");
	}	
}