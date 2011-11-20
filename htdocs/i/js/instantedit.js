function makeEditable(id){
	Event.observe(id, 'click', function(){edit($(id))}, false);
	Event.observe(id, 'mouseover', function(){showAsEditable($(id))}, false);
	Event.observe(id, 'mouseout', function(){showAsEditable($(id), true)}, false);
}

function showAsEditable(obj, clear){
	if (!clear){
		Element.addClassName(obj, 'editable');
	} else {
		Element.removeClassName(obj, 'editable');
	}
}

function edit(obj){
	Element.hide(obj);

	var textarea = '<div id="'+obj.id+'_editor"><input id="'+obj.id+'_edit" name="'+obj.id+'" value="'+obj.innerHTML+'"/>';
	var button	 = '<div><input id="'+obj.id+'_save" type="button" value="Zapisz" /> &nbsp; <input id="'+obj.id+'_cancel" type="button" value="Anuluj" /></div></div>';
	
	new Insertion.After(obj, textarea+button);	
		
	Event.observe(obj.id+'_save', 'click', function(){saveChanges(obj)}, false);
	Event.observe(obj.id+'_cancel', 'click', function(){cleanUp(obj)}, false);
}

function cleanUp(obj, keepEditable){
	Element.remove(obj.id+'_editor');
	Element.show(obj);
	if (!keepEditable) showAsEditable(obj, true);
}

function editComplete(t, obj){
	var res = document.getElementById(obj.id+'_result');
	res.innerHTML = '';
	obj.innerHTML = t.responseText;
	showAsEditable(obj, true);
}

function editFailed(t, obj){
	var res = document.getElementById(obj.id+'_result');
	res.innerHTML = '<strong class="msgErr"> Nie udało się zapisać zmian.</strong>';
	cleanUp(obj);
}