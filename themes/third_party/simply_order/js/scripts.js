$(document).ready(function(){
    $(function() {
	$("#sortable1, #sortable2").sortable({ 
	    opacity: 0.6,
	    cursor: 'move',
	    connectWith: ".connectedSortable",
		
	    update: function() {
		var order = $("#sortable2").sortable("serialize");
		alert(order); 
	    }
	});
    });
	
});