<h1>Add New Entries Order</h1>

<?php if (! isset($query)) { ?>
<div id="add_new_order_form">
    <div class="simply_order_form_row">
	<?php echo form_open($form_action); ?>
    </div>
    <div class="simply_order_form_row">
	<?php echo form_input('order_tag', 'Order Tag'); ?>
    </div>
    <div class="simply_order_form_row">
	<?php echo form_input('channel_id', 'Channel ID'); ?>
    </div>
    <div class="simply_order_form_row">
	<?php echo form_input('site_id', 'Site ID'); ?>
    </div>
    <div class="simply_order_form_row">
	<?php echo form_submit('submit', 'Submit', 'class="submit"'); ?>
    </div>
    <div class="simply_order_form_row">
	<?php echo form_close(); ?>
    </div>
</div>
<?php } else { ?>
<h2>New ordering created.</h2>

<?php } ?>
