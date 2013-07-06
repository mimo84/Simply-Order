<h1>Select the ordering you would like to edit.</h1>

<?php 
$this->table->set_heading('Name', 'Click below to edit!','','');
foreach ($list as $single) {

    $this->table->add_row(
	    form_open($form_action . "&amp;id_simply_order=" . $single['id'] . "&amp;site_id=" . $single['site_id'] . "&amp;channel_id=" . $single['channel_id']) . $single['order_tag'], 
	    form_submit('submit', 'Edit it!', 'class="submit"'),form_close(), 
	    anchor()
	    );
}

echo $this->table->generate();
?>