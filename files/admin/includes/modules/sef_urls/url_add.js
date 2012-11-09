var urlData = new Array();
var internalLineNr = 0;

var pathToImages = 'includes/modules/sef_urls/images';

function loadTable()
{
	//alert(urlData.length);
	for(var i = 0; i < urlData.length; i++)
	{
		addLangBreak(urlData[i], i);
		for(var y = 0; y < urlData[i]['data'].length; y++)
		{
			addLine(i, y);
		}
		addLine(i, false);
	}
}


function addLangBreak(data, langIndex)
{
	var addString = '';

	if(data != false)
	{
		addString += '<b><font style="line-height:1.9;">'+ data["name"] + '</font></b><br />';
		addString += langTextNormalUrl+' '+data["input"];
		
		addString += '<table border="0" width="100%" cellspacing="0" cellpadding="2" id="listing_table_'+langIndex+'">';
		
		addString += '<tr class="dataTableHeadingRow" id="line_row_'+internalLineNr+'" >';
			addString += '<td class="dataTableHeadingContent" width="60%">';
			addString += langTextAlias;
			addString += '</td>';
			addString += '<td class="dataTableHeadingContent" width="20%">';
			addString +=  langTextDefault;
			addString += '</td>';
			addString += '<td class="dataTableHeadingContent" width="20%">';
			addString +=  langTextAction;
			addString += '</td>';
		addString += '</tr>';
		addString += '</table><br />';
		internalLineNr++;
	}
	$('listing').insert(addString); 
}

function addLine(langIndex, urlIndex)
{
	var addString = '';
	addString += '<tr class="dataTableRow" id="line_row_'+internalLineNr+'">';
	addString += '<td>&nbsp;</td>';
	addString += '</tr>';
	$('listing_table_'+langIndex).insert(addString); 
	
	if(urlIndex !== false)
	{
		showLine(internalLineNr, langIndex, urlIndex);
		internalLineNr++;
	}
	else
	{
		urlData[langIndex]['data'][nextUrlId] = new Object();
		urlData[langIndex]['data'][nextUrlId]["url_id"] = nextUrlId;
		urlData[langIndex]['data'][nextUrlId]["url_alias"] = '';
		urlData[langIndex]['data'][nextUrlId]["languages_id"] = urlData[langIndex]["languages_id"];
		urlData[langIndex]['data'][nextUrlId]["url_default"] = '0';
		urlData[langIndex]['data'][nextUrlId]["new_added"] = true;
		showLine(internalLineNr, langIndex, nextUrlId);
		$('item_to_add').insert('<input type="hidden" name="url_add['+nextUrlId+']" value="'+nextUrlId+'" />');

		nextUrlId++;
		internalLineNr++;
	}	
}

function editLine(lineNr, langIndex, urlIndex)
{
	var replaceString = '';
	
	replaceString += '<td class="dataTableContent line">';
	replaceString += 	'<input id="input_field_alias_'+lineNr+'" name="url['+urlData[langIndex]['data'][urlIndex]["url_id"]+'][url_alias]" type="text" value="'+urlData[langIndex]['data'][urlIndex]["url_alias"]+'" size="50" />';
	replaceString += 	'<input id="input_field_lang_'+lineNr+'" name="url['+urlData[langIndex]['data'][urlIndex]["url_id"]+'][languages_id]" type="hidden" value="'+urlData[langIndex]['data'][urlIndex]["languages_id"]+'" size="50" />';
	replaceString += '</td>';
	
	replaceString += '<td class="dataTableContent line">';
	replaceString += 	'<input id="input_field_default_'+lineNr+'" name="url['+urlData[langIndex]['data'][urlIndex]["url_id"]+'][url_default]" type="checkbox" value="1" size="50"';
	if(urlData[langIndex]['data'][urlIndex]["url_default"] == 1)
		replaceString += ' checked="checked" ';
		
	replaceString +=	'/>';
	replaceString += '</td>';
	
	replaceString += '<td class="dataTableContent line">';
	replaceString += 	'<a href="" onclick="return saveLine(\''+lineNr+'\', \''+langIndex+'\', \''+urlIndex+'\');"><img src="'+pathToImages+'/save.png" style="border:none;" title="Save"/></a>&nbsp;&nbsp;';
	replaceString += 	'<a href="" onclick="return deleteLine(\''+lineNr+'\', \''+langIndex+'\', \''+urlIndex+'\');"><img src="'+pathToImages+'/trash.png" style="border:none;" title="Delete"/></a>&nbsp;&nbsp;';
	replaceString += 	'<a href="" onclick="return abordLine(\''+lineNr+'\', \''+langIndex+'\', \''+urlIndex+'\');"><img src="'+pathToImages+'/abord.png" style="border:none;" title="Cancel"/></a>';
	replaceString += '</td>';
	
	
	$('line_row_'+lineNr).update(replaceString)
	$('input_field_alias_'+lineNr).focus();
	return false;
}

function abordLine(lineNr, langIndex, urlIndex)
{
	showLine(lineNr, langIndex, urlIndex);
	return false;
}

function showLine(lineNr, langIndex, urlIndex)
{
	var replaceString = '';
	replaceString += '<td class="dataTableContent line" onclick="editLine(\''+lineNr+'\', \''+langIndex+'\', \''+urlIndex+'\');">';
	replaceString += 	urlData[langIndex]['data'][urlIndex]["url_alias"];
	replaceString += '</td>';
	replaceString += '<td class="dataTableContent line" onclick="editLine(\''+lineNr+'\', \''+langIndex+'\', \''+urlIndex+'\');">';
	replaceString += 	urlData[langIndex]['data'][urlIndex]["url_default"] == 1 ? 'true' : 'false';
	replaceString += '</td>';
	replaceString += '<td class="dataTableContent line">';
	replaceString += 	'<a href="" onclick="return editLine(\''+lineNr+'\', \''+langIndex+'\', \''+urlIndex+'\');"><img src="'+pathToImages+'/add.png" style="border:none;"/></a>&nbsp;&nbsp;';
	replaceString += '</td>';
	$('line_row_'+lineNr).update(replaceString)
	return false;
}

function saveLine(lineNr, langIndex, urlIndex)
{
	if($('input_field_alias_'+lineNr).value == '')
	{
		abordLine(lineNr, langIndex, urlIndex);
		return false;
	}
	urlData[langIndex]['data'][urlIndex]["url_alias"] = $('input_field_alias_'+lineNr).value;
	$('input_field_default_'+lineNr).checked == true ? urlData[langIndex]['data'][urlIndex]["url_default"] = 1 : urlData[langIndex]['data'][urlIndex]["url_default"] = '0';
	
	showLine(lineNr, langIndex, urlIndex);
	var newString = '';
	newString += '<input name="url['+urlData[langIndex]['data'][urlIndex]["url_id"]+'][url_alias]" type="hidden" value="'+urlData[langIndex]['data'][urlIndex]["url_alias"]+'" size="50" />';
	newString += '<input name="url['+urlData[langIndex]['data'][urlIndex]["url_id"]+'][url_default]" type="hidden" value="'+urlData[langIndex]['data'][urlIndex]["url_default"]+'" size="50" />';
	newString += '<input name="url['+urlData[langIndex]['data'][urlIndex]["url_id"]+'][languages_id]" type="hidden" value="'+urlData[langIndex]['data'][urlIndex]["languages_id"]+'" size="50" />';
	$('line_row_'+lineNr).update($('line_row_'+lineNr).innerHTML+newString);
	if(urlData[langIndex]['data'][urlIndex]["new_added"])
	{
		addLine(langIndex, false);
		urlData[langIndex]['data'][urlIndex]["new_added"] = false;
	}
	return false;
}

function deleteLine(lineNr, langIndex, urlIndex)
{
	if(!urlData[langIndex]['data'][urlIndex]["new_added"])
	{
		$('line_row_'+lineNr).replace('');
		$('item_to_delete').insert('<input type="hidden" name="url_delete['+urlData[langIndex]['data'][urlIndex]["url_id"]+']" value="'+urlData[langIndex]['data'][urlIndex]["url_id"]+'" />');
	}
	return false;
}



