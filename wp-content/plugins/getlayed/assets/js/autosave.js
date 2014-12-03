var AutoSave = (function(){
 
	var timer = null;
 
	function getEditor(){
 
		var elems = document.querySelector("#layout-builder");
		if (elems == 'undefined')
			return null;
 
		return elems;
	}
 
 
	function save(){
 
		var editor = getEditor(); 
                if (editor) {
		    localStorage.setItem("AUTOSAVE_" + document.location, editor )
                }
 
	}
 
 
	function restore(){
 
		var saved = localStorage.getItem("AUTOSAVE_" + document.location)
		var editor = getEditor();
		if (saved && editor){
 
			editor = saved; 
		}
	}
 
	return { 
 
		start: function(){
 
			var editor = getEditor(); 
 
			if (editor)
				restore();
 
			if (timer != null){
				clearInterval(timer);
				timer = null;
			}
 
			timer = setInterval(save, 5000);
 
 
		},
 
		stop: function(){
 
			if (timer){ 
				clearInterval(timer);
				timer = null;
			}
 
		}
	}
 
}());