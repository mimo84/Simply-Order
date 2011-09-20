<h1>Insert or remove entries.</h1>

<h3>Current entries in the channel:</h3>
<div id="availables">
<? foreach ($entries->result_array() as $single_one) { ?>

    <div class="element">
	<? 
	    echo form_hidden('entry_id', $single_one['entry_id'] ); 
	    echo form_input('title', $single_one['title'], 'readonly');
	?>

    </div>


<? } ?>
</div>