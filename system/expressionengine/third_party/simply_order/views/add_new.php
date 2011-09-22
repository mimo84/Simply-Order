<h1>Add New Entries Order</h1>

<? if (! isset($query)) { ?>
<div id="add_new_order_form">
    <div class="simply_order_form_row">
	<? echo form_open($form_action); ?>
    </div>
    <div class="simply_order_form_row">
	<? echo form_input('order_tag', 'Order Tag'); ?>
    </div>
    <div class="simply_order_form_row">
	<? echo form_input('site_id', 'Site ID'); ?>
    </div>
    <div class="simply_order_form_row">
	<? echo form_submit('submit', 'Submit', 'class="submit"'); ?>
    </div>
    <div class="simply_order_form_row">
	<? echo form_close(); ?>
    </div>
</div>
<? } else { ?>
<h2>New ordering added.</h2>

<? } ?>
