<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 *  Simply Order - Update / Install / Uninstall file
 *
 * @package	ExpressionEngine
 * @category	Module
 * @author	Maurizio Napoleoni
 * @link	http://www.zoomingin.net/
 * @copyright 	Copyright (c) 2011 Maurizio Napoleoni
 * @license   	http://creativecommons.org/licenses/MIT/  MIT License
 *
 */

/** Install, uninstall and update module file. * */
class Simply_order_upd {

    var $version = '0.9';

    function __construct() {
	$this->EE = & get_instance();
    }

    function install() {
	$this->EE->load->dbforge();

	/* How this module works. */
	$data = array(
	    'module_name' => 'Simply_order',
	    'module_version' => $this->version,
	    'has_cp_backend' => 'y',
	    'has_publish_fields' => 'n'
	);
	// Insert info in the ee's modules table.
	$this->EE->db->insert('modules', $data);

	/* Qui andiamo ad inserire la possibilita' per il modulo di mettere azioni sul frontend. Nel nostro caso un submit 
	 * la funzione che richiamiamo per gestire questa cosa è la funzione form_submit */
	$data = array(
	    'class' => 'Simply_order',
	    'method' => 'form_submit' //Questa funzione è quella che dovremo poi richimare al submit
	);
	// Inseriamo le informazioni nella tabella actions in modo da renderle poi disponibili
	$this->EE->db->insert('actions', $data);

	/*
	 * New database tables from version 0.9
	 * Table 'simply_order' edited with new fields from this version.
	 */
	$fields = array(
	    'id_simply_order' => array(
		'type' => 'INT',
		'constraint' => '11',
		'unsigned' => TRUE,
		'auto_increment' => TRUE,
	    ),
	    'order_tag' => array(
		'type' => 'VARCHAR',
		'constraint' => '45',
		'null' => TRUE,
	    ),
	    'site_id' => array(
		'type' => 'INT',
		'constraint' => '100',
	    ),
	);

	$this->EE->dbforge->add_field($fields);
	$this->EE->dbforge->add_key('id_simply_order', TRUE);
	$this->EE->dbforge->create_table('simply_order', TRUE);
	unset($fields);

	/*
	 * New table 'simply_order_tree' from version 0.9
	 * Added to support multiple orders
	 */
	$fields = array(
	    'id_simply_order_tree' => array(
		'type' => 'INT',
		'constraint' => '11',
		'unsigned' => TRUE,
		'auto_increment' => TRUE,
	    ),
	    'parent_id' => array(
		'type' => 'INT',
		'constraint' => '11',
		'unsigned' => TRUE,
	    ),
	    'order_by' => array(
		'type' => 'INT',
		'constraint' => '11',
		'unsigned' => TRUE,
	    ),
	    'entry_id' => array(
		'type' => 'INT',
		'constraint' => '100',
	    ),
	    'id_simply_order' => array(
		'type' => 'INT',
		'unsigned' => TRUE,
	    ),
	);

	$this->EE->dbforge->add_field($fields);
	$this->EE->dbforge->add_key('id_simply_order');
	$this->EE->dbforge->add_key('id_simply_order_tree', TRUE);
	$this->EE->dbforge->create_table('simply_order_tree', TRUE);

	return TRUE;
    }

    function uninstall() {
	$this->EE->load->dbforge();

	//Prende il modulo corrente e lo disinstalla dalla tabella modules
	$this->EE->db->select('module_id');
	$this->EE->db->where('module_name', 'Simply_order');
	$this->EE->db->delete('modules');

	//Ora lo tolgo dalla tabella actions
	$this->EE->db->where('class', 'Simply_order');
	$this->EE->db->delete('actions');

	//Cancello la tabella corrispondente simply_order dal database
	$this->EE->dbforge->drop_table('simply_order');
	$this->EE->dbforge->drop_table('simply_order_tree');

	return TRUE;
    }

    function update($current='') {

	if ($current == $this->version) {
	    return FALSE;
	}

	if ($current < 0.1) {

	    // Update code
	}

	return TRUE;
    }

}

/* END Class */

/* End of file upd.simply_order.php */
/* Location: ./system/expressionengine/third_party/modules/simply_order/upd.simply_order.php */