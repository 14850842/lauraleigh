jQuery('.gallerybtn').on('click',function(e){
	e.preventDefault();
	jQuery('.thumbnailMenu').toggleClass('open');
})

jQuery(document).on({
    mouseenter: function () {
      	var options = tmpl("tmpl-img-options",{});
		jQuery(this).append(options);
    },
    mouseleave: function () {
        jQuery(this).find('#img-options').remove();
    }
},'.col');

jQuery(document).on({
    mouseenter: function () {
    	var data = {
			row : jQuery(this).attr('id')
		};
		console.log(data);
      	var options = tmpl("tmpl-row-options",data);
		jQuery(this).append(options);
    },
    mouseleave: function () {
       jQuery(this).find('#row-options').remove();
    }
},'.row-item');


jQuery(document).on({
    click: function (e) {
    	e.preventDefault();
    	var id = jQuery(this).data('row');
    	var row = jQuery('#'+id);

    	var images = row[0].querySelectorAll('img');



		[].forEach.call(
	  		images, 
	  			function(el){
					document.querySelector('#image-'+el.dataset.id).className = 'img-responsive';
	  			}
		);

		row[0].parentNode.removeChild(row[0]);
    	
    },
},'.row-item .trash-row');

jQuery(document).on({
    click: function (e) {
    	e.preventDefault();
    	var id = jQuery(this).data('row');
    	var row = jQuery('#'+id);
    	console.log(row[0].previousElementSibling);
    	row[0].parentNode.insertBefore(row[0],row[0].previousElementSibling);
    	
    },
},'.row-item .swap-row-up');

jQuery(document).on({
    click: function (e) {
    	e.preventDefault();
    	var id = jQuery(this).data('row');
    	var row = jQuery('#'+id);
    	console.log(jQuery(row[0]));
    	insertAfter(row[0],row[0].nextElementSibling);
    	
    },
},'.row-item .swap-row-down');

function insertAfter(newElement,targetElement) {
    //target is what you want it to go after. Look for this elements parent.
    var parent = targetElement.parentNode;

    //if the parents lastchild is the targetElement...
    if(parent.lastchild == targetElement) {
        //add the newElement after the target element.
        parent.appendChild(newElement);
        } else {
        // else the target has siblings, insert the new element between the target and it's next sibling.
        parent.insertBefore(newElement, targetElement.nextSibling);
        }
}

jQuery(document).on({
    click: function (e) {
    	e.preventDefault();
    	var row = jQuery(this).closest('.row-holder');
    	var col = jQuery(this).closest('.col');
    	
    	document.querySelector('#image-'+col[0].querySelector('img').dataset.id).className = 'img-responsive';
    	col.remove();

    	var cols = row[0].childNodes.length;
		row[0].dataset.colus = cols;
	  	row[0].className = 'row-holder col-'+ cols;
    	
    },
},'.col #img-options .trash-img');


jQuery(document).ready(function() {
 
  var owl = jQuery("#owl-demo");
 
  owl.owlCarousel({
  		mouseDrag : false,
  });
 
  // Custom Navigation Events
  jQuery(".next").click(function(){
    owl.trigger('owl.next');
  })
  jQuery(".prev").click(function(){
    owl.trigger('owl.prev');
  })
 
});

var editorBtn = document.querySelector('.editorBtn');
var itBtn = document.querySelector('.itbtn');
//var recipeBtn = document.querySelector('.recipeBtn');

var elements = document.querySelectorAll('.editor');
	
var article2Medium = new MediumEditor(elements);

var row = 0;

function reinit() {
	
	}


itBtn.onmousedown = function(e) {
	var data = {
		count : row++
	};
	e.preventDefault();
	var node = document.getSelection().anchorNode,startNode = (node && node.nodeType === 3 ? node.parentNode : node);



	var func = tmpl("tmpl-demo",data);

	if (node && node.innerText === '\n' || node.innerText === '') {
		jQuery(startNode).before(func);
	} else {
		jQuery(startNode).after(func).after('<p><br></p>');
	}


	//document.execCommand('insertHtml', false, func);
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


