<h1>Insert or remove entries.</h1>

<? echo BASE . $form_action; ?>

<script type="text/javascript">
    function maurizio(){
	    var order = $("#sortable2").sortable("serialize");
	    $.ajax({
		type: "GET",
		dataType: "json",
		url: "<? echo BASE . $form_action; ?>",
		data: order
	    })
	};
    
    $(document).ready(function(){
	$(function() {
	    $("#sortable1, #sortable2").sortable({ 
		opacity: 0.6,
		cursor: 'move',
		connectWith: ".connectedSortable"
	    });
	});
		
    });
</script>
<h3>Current entries in the channel:</h3>

<a onclick="maurizio();">CLICK ME</a>

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

<?
$attributes = array(
    'id' => 'ordering'
);
echo form_open($form_action, $attributes);
?>

<ul id="sortable2" class="connectedSortable">
    <li class="ui-state-default">Desidered order.</li>
</ul>
<? echo form_submit('submit', 'Submit', 'class="submit"'); ?>
<? echo form_close(); ?>