<h1>Insert or remove entries.</h1>
<script type="text/javascript">
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
</script>

<h3>Current entries in the channel:</h3>
<ul id="sortable1" class="connectedSortable">
    <li class="ui-state-default">Entries you have:</li>
    <? foreach ($entries->result_array() as $single_one) { ?>
        <li id="entry_id_<? echo $single_one['entry_id']; ?>" class="ui-state-default">
	    <?
	    echo form_hidden('entry_id', $single_one['entry_id']);
	    echo form_input('title', $single_one['title'], 'readonly');
	    ?>
        </li>
    <? } ?>
</ul>
<? echo form_open(); ?>
<ul id="sortable2" class="connectedSortable">
    <li class="ui-state-default">Desidered order.</li>
</ul>
<? echo form_submit('submit', 'Submit', 'class="submit"'); ?>
<? echo form_close(); ?>
<style>
    #sortable1, #sortable2 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; }
    #sortable1 li, #sortable2 li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; width: 120px; }
</style>
