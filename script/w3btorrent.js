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
	$.each(json['torrents'],function(num,torrent)
	{
		var html = '';
		if ($('#'+torrent['hash']).html() == null)	// this row does not exist, let's add it
		{
			html += '<tr id="'+torrent['hash']+'">';
		}
		html += '<td>'+torrent['shortName']+'</td>';
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
	
	// totals
	if (!$('#set_download_rate').is(':focus') && !$('#set_upload_rate').is(':focus') && !$('#set_max_peers_seed').is(':focus') && !$('#set_max_peers').is(':focus'))
	{
		var html = '';
		// admin stuff
		if (json['total']['down']['setRate'])
		{
			html += '<td><strong>Total and settings</strong></td>';
		}
		else
		{
			html += '<td><strong>Total</strong></td>';
		}
		html += '<td>'+json['total']['size']+'/'+json['total']['freeSpace']+'</td>';	
		html += '<td>'+json['total']['percent']+'%</td>';	
		html += '<td></td>';
		html += '<td>'+json['total']['down']['loaded']+'</td>';	
		html += '<td>'+json['total']['up']['loaded']+'</td>';
		
		// admin stuff
		if (json['total']['down']['setRate'])
		{
			html += '<td>'+json['total']['down']['rate']+'/<input title="Max. download speed" id="set_download_rate" style="background-color:#faffbd;border:0px;padding:0;margin:0;min-width:2em;width:'+(json['total']['down']['setRate'].length*0.6)+'em;" type="text" value="'+json['total']['down']['setRate']+'" onkeydown="updateSettingHandler(this,event,\''+json['total']['down']['setRate']+'\');" />'+(json['total']['down']['setRate'] != "off"?"/s":"")+'</td>';
			html += '<td>'+json['total']['up']['rate']+'/<input title="Max. upload speed" id="set_upload_rate" style="background-color:#faffbd;border:0px;padding:0;margin:0;min-width:2em;width:'+(json['total']['up']['setRate'].length*0.6)+'em;" type="text" value="'+json['total']['up']['setRate']+'" onkeydown="updateSettingHandler(this,event,\''+json['total']['up']['setRate']+'\');" />'+(json['total']['up']['setRate'] != "off"?"/s":"")+'</td>';
			html += '<td>'+json['total']['seeds']+'/<input title="Max. numer of seeders" id="set_max_peers_seed" style="background-color:#faffbd;border:0px;padding:0;margin:0;min-width:2em;width:'+(json['total']['maxSeeds'].length*0.6)+'em;" type="text" value="'+json['total']['maxSeeds']+'" onkeydown="updateSettingHandler(this,event,\''+json['total']['maxSeeds']+'\');" /></td>';
			html += '<td>'+json['total']['leech']+'/<input title="Max. numer of leechers" id="set_max_peers" style="background-color:#faffbd;border:0px;padding:0;margin:0;min-width:2em;width:'+(json['total']['maxLeech'].length*0.6)+'em;" type="text" value="'+json['total']['maxLeech']+'" onkeydown="updateSettingHandler(this,event,\''+json['total']['maxLeech']+'\');" /></td>';
		}
		else
		{
			html += '<td>'+json['total']['down']['rate']+'/s</td>';
			html += '<td>'+json['total']['up']['rate']+'/s</td>';
			html += '<td>'+json['total']['seeds']+'</td>';
			html += '<td>'+json['total']['leech']+'</td>';
		}
		$('#downloadsTableFooter').html(html);
	}
}

function updateSettingHandler(obj,e,oldVal)
{
	if (e != '' && e.keyCode == 27)	// ESC is pressed
	{
		$(obj).val(oldVal);
	}
	else if (e == '' || e.keyCode == 13)	// enter is pressed
	{
		$(obj).blur();
		$(obj).attr("disabled",true);
		$.ajax({
			url:'?p=d',
			type: 'POST',
			data: {ajax:1, refresh:1, action:$(obj).attr('id'), value:$(obj).val()},
			timeout: 5000,
			success: function(json)
			{
				updateDownloadsContent(json);
			}
		});
	}
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
			setTimeout("updateDownloads()",10000);	// re-run in case of timeout or whatever
		},
		success: function(xml)
		{
			if ($('#downloadsTableBody').html() != null)
			{
				updateDownloadsContent(xml);
				setTimeout("updateDownloads()",10000);
			} // user changed page
		}
	});
}

//
// FILES
//
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
