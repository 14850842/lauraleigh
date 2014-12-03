jQuery(document).ready(function(){
	AutoSave.start();
})

jQuery(window).unload(function(){
	AutoSave.stop();
});

jQuery('.gallerybtn').on('click',function(e){
	e.preventDefault();
	jQuery('.thumbnailMenu').toggleClass('open');
})


var editorBtn = document.querySelector('.editorBtn');
var itBtn = document.querySelector('.itbtn');
//var recipeBtn = document.querySelector('.recipeBtn');

var elements = document.querySelectorAll('.editor');
	
var article2Medium = new MediumEditor(elements);

var row = 0;

function reinit() {

	var elements = document.querySelectorAll('.editor');
	
	var article2Medium = new MediumEditor(elements);

	var dropZones = document.querySelectorAll('.init');
	var dropCanvases = document.querySelectorAll('.col');

	[].forEach.call(
	  dropZones, 
	  function(el){
		el.addEventListener('dragover', function(e) {
		  if (e.preventDefault) {
			e.preventDefault();
		  }

		  e.dataTransfer.dropEffect = 'move';

		  return false;
		},false);
	  }
	);

	[].forEach.call(
	  dropZones, 
	  function(el) {
			var count = 1;
				el.classList.remove('init');
			  	el.addEventListener('drop', function(e) {
					console.log(e);
					if (e.preventDefault) {
					  	e.preventDefault();
					}

					e.stopPropagation();

				  	data = el.dataset; 

					canvas = document.querySelector('#'+data.canvas);
					
					el.classList.remove('open');

				  	if (count == 1) { canvas.innerHTML +=  '<div data-colus="1" class="row-holder col-1"></div>'; count++; } 
					else if (count <= 4) {
						canvas.querySelector('.row-holder').dataset.colus = count;
					  	canvas.querySelector('.row-holder').className = 'row-holder col-'+count++;
					}
					else {
						cancel(e);
					  	return false;
					}

					
					canvas.querySelector('.row-holder').innerHTML += '<div class="col"><img class="unprocessed" data-id="'+e.dataTransfer.getData('id')+'" class="img-responsive" src="' + e.dataTransfer.getData('Text') + '"/></div>';

					var list = canvas.querySelector(".row-holder");
					new Sortable(list,{
					   onAdd: function (evt){
						  var itemEl = evt.item;

					  },
					  onUpdate: function (evt){
						  var itemEl = evt.item; // the current dragged HTMLElement
						  console.log(evt.item);
					  },
					  onRemove: function (evt){
						  var itemEl = evt.item;
					  }
					});
				},false);
			
	  	});
	}


itBtn.onmousedown = function(e) {
	var data = {
		count : row++
	};
	e.preventDefault();
	var func = tmpl("tmpl-demo",data);
	document.execCommand('insertHtml', false, func);
	reinit();
}

var saveBtn = document.querySelector('.saveBtn');
saveBtn.onmousedown = function(e) {
	e.preventDefault();
	console.log(image_array);
	var content = article2Medium.serialize();
	console.log(content);
	var formURL = myAjax.ajaxurl;
	content.action = 'ppm_save_post';
	content.id = myAjax.post_id;
	console.log(content);
	jQuery.ajax(
	{
	url : formURL,
	type: "POST",
	data : content,
	success:function(data, textStatus, jqXHR) 
	{   
	  alert('Yes');
	  
	},
	error: function(jqXHR, textStatus, errorThrown) 
	{
	  //if fails      
	}
	});
}

// Get the div element that will serve as the drop target.

var rows = document.querySelector("#layout-builder > .row");

// Get the draggable elements.
var dragElements = document.querySelectorAll('[draggable="true"]');

// Track the element that is being dragged.
var elementDragged = null;

var timeoutId = null;


[].forEach.call(
  dragElements, 
  function(el){
	el.addEventListener('dragstart', function(e) {

		toggleClass(document.getElementsByTagName('body')[0],'dragstart');
		console.log(e);
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text', this.src);
		e.dataTransfer.setData('id', el.dataset.id);
		elementDragged = this;

	});

	el.addEventListener('dragend', function(e) {
		elementDragged = null;
		toggleClass(document.getElementsByTagName('body')[0],'dragstart');
		window.clearTimeout(timeoutId);
	});
  }
);



function swap(elem,direction)
{
	var num = 0;

	data = elem.dataset;

	if (direction == 'up') num = parseInt(data.current) - 1;
	if (direction == 'down') num = parseInt(data.current) + 1;
	console.log(data.current);
	console.log(num);

	var current = document.querySelector('#canvas-'+data.current);
	var second = document.querySelector('#canvas-'+num);
	var temp;

	temp = current.innerHTML;
	current.innerHTML = second.innerHTML;
	second.innerHTML = temp;

	console.log(obj[1]);
	var temp_2 = new Object();
	temp_2 = obj[data.current];

	obj[data.current] = obj[num];
	obj[num] = temp_2;
	
	console.log(obj[1]);

	var list = document.querySelector(".canvas .row-holder");

	  new Sortable(list,{
		 onAdd: function (evt){
			var itemEl = evt.item;

		},
		onUpdate: function (evt){
			var itemEl = evt.item; // the current dragged HTMLElement
			console.log(evt.item);
		},
		onRemove: function (evt){
			var itemEl = evt.item;
		}
	  });



}

function toggleClass(elem, className) {
	var newClass = ' ' + elem.className.replace( /[\t\r\n]/g, ' ' ) + ' ';
	if (hasClass(elem, className)) {
		while (newClass.indexOf(' ' + className + ' ') >= 0 ) {
			newClass = newClass.replace( ' ' + className + ' ' , ' ' );
		}
		elem.className = newClass.replace(/^\s+|\s+$/g, '');
	} else {
		elem.className += ' ' + className;
	}
}

function hasClass(elem, className) {
	return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
}
