<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Simply Order - Control Panel File
 *
 * @package	ExpressionEngine
 * @category	Module
 * @author	Maurizio Napoleoni
 * @link	http://www.zoomingin.net/
 * @copyright 	Copyright (c) 2011 Maurizio Napoleoni
 * @license   	http://creativecommons.org/licenses/MIT/  MIT License
 *
 */
class Simply_order_mcp {

    private $_base_url;   // the base url for this module
    private $_form_base;
    private $module_name = "simply_order";

    public function __construct() {
	$this->EE = & get_instance();
	//prende la url di base del pannello di controllo
	$this->_base_url = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp';
	$this->_base_url .= AMP . 'module=' . $this->module_name;
	$this->_form_base = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->module_name;
	$this->theme_base = $this->EE->config->item('theme_folder_url') . 'third_party/simply_order/';

	// Top right menu navigation.
	$this->EE->cp->set_right_nav(array(
	    'docs' => $this->EE->cp->masked_url('http://www.zoomingin.net/'),
	    'new_order' => $this->_base_url . AMP . 'method=add_new'
	));

	// Libraries and helpers
	$this->EE->load->library('javascript');
	$this->EE->load->library('table');
	$this->EE->load->library('form_validation');
	$this->EE->load->helper('form');
    }

    /**
     * Module Home
     */
    public function index() {
	$vars = array();
	/*
	 * In questa funzione abbiamo due possibili comportamenti:
	 * 1. Nessun ordering è stato ancora caricato -> visualizziamo il form "Add new Ordering"
	 * 2. Sono stati inseriti degli ordering. Ne visualizziamo la lista, e con un click diamo
	 *    la possibilità all'utente di modificarlo, oppure di cancellare i record con un secondo
	 *    link.
	 */

	// Andiamo a vedere se sono presenti dei record salvati nel sistema. Utilizzo in questo caso una
	// funzione privata creata ad hoc per questo scopo: _get_simply_orders();
	$data['query'] = $this->_get_simply_orders();

	if ($data['query']) {
	    foreach ($data['query']->result_array() as $row) {
		$vars['list'][$row['id_simply_order']]['id'] = $row['id_simply_order'];
		$vars['list'][$row['id_simply_order']]['site_id'] = $row['site_id'];
		$vars['list'][$row['id_simply_order']]['order_tag'] = $row['order_tag'];
		$vars['list'][$row['id_simply_order']]['channel_id'] = $row['channel_id'];
	    }


	    $vars['cp_page_title'] = $this->EE->lang->line('Edit orders');
	    $vars['form_action'] = $this->_form_base . AMP . 'method=update_record';

	    return $this->content_wrapper('edit', 'edit', $vars);
	} else {
	    // Se non è presente nessun nuovo ordering andiamo a visualizzare 
	    // automaticamente il form per inserire il primo.
	    $this->add_new();
	}
    }

    function get_records() {
	$query = $this->EE->db->get('simply_order');
	return $query;
    }

    /*
     * Add new adds a new ordering, with a tag and site_id.
     */

    function add_new() {

	$this->EE->form_validation->set_rules('order_tag', 'required');
	$this->EE->form_validation->set_rules('site_id', 'required');
	$this->EE->form_validation->set_rules('channel_id', 'required');

	if ($this->EE->form_validation->run() == FALSE) {
	    // Definisco la variabile da passare alla vista
	    $vars = array(
		'cp_page_title' => $this->EE->lang->line('new_order'),
		'form_action' => $this->_form_base . AMP . 'method=add_new',
	    );
	    // Faccio caricare la vista per aggiungere un nuovo ordering
	    return $this->content_wrapper('add_new', 'welcome', $vars);
	} else {

	    $data['channel_id'] = $this->EE->input->post('channel_id');
	    $data['order_tag'] = $this->EE->input->post('order_tag');
	    $data['site_id'] = $this->EE->input->post('site_id');
	    $vars['query'] = $this->EE->db->insert('simply_order', $data);
	    $vars['cp_page_title'] = $this->EE->lang->line('new_order');
	    $vars['form_action'] = $this->_form_base . AMP . 'method=add_new';

	    return $this->content_wrapper('add_new', 'welcome', $vars);
	}
    }

    /*
     * 
     * Update record shows the interface where peoples can drag and drop elements.
     * The method calls the "edit_single" method to save data.
     * 
     */

    function update_record() {

	$vars['site_id'] = $this->EE->input->get('site_id');
	$vars['channel_id'] = $this->EE->input->get('channel_id');
	$vars['id_simply'] = $this->EE->input->get('id_simply_order');

	$vars['cp_page_title'] = $this->EE->lang->line('edit_single');
	$vars['form_action'] = $this->_form_base . AMP . 'method=edit_single';

	// This section can be edited: peoples can 
	$data['site_id'] = $vars['site_id'];
	$data['channel_id'] = $vars['channel_id'];
	$data['id_simply'] = $vars['id_simply'];

	
	$old_entries = $this->_get_old_order($data);
	if($old_entries->num_rows > 0){
	    $vars['old_entries'] = $old_entries;
	    $vars['entries'] = $this->_get_channel_entries($data);
	} else {
	    $vars['entries'] = $this->_get_channel_entries($data);
	}
	
	if ($vars['entries']) {
	    
	    return $this->content_wrapper('edit_single', 'edit_single', $vars);
	} else {
	    $this->index();
	}
    }
	
    /*
     * edit_single method is called by update_record. It saves entries order in the
     * "simply_order_tree" table.
     */

    function edit_single() {
	/*
	 *  I have to save: 
	 *        *  parent_id (actually setted to zero).
	 *        *  order_by (given by a variable increasing).
	 *        *  entry_id (the entry_id I'm moving from the default ordering).
	 *        *  id_simply_order (the relationshipt between each row and the simply_order table).
	 */
	// I take the entry_ids from the view, serialized with jQuery.
	$entry_ids = $this->EE->input->post('entry_order');

	// I take the id_simply_order from an hidden field.
	$vars['id_simply_order'] = $this->EE->input->post('id_simply');
	$vars['parent_id'] = 0;

	// I have to delete all entries from the db with the same id_simply_order (if any).
	$this->EE->db->where('id_simply_order', $vars['id_simply_order']);
	$query = $this->EE->db->get('simply_order_tree');
	if ($query->num_rows() > 0) {
	    $this->EE->db->where('id_simply_order', $vars['id_simply_order']);
	    $this->EE->db->delete('simply_order_tree');
	}


	// Section to explode entry_ids and insert each in the db
	$coppie = explode("&", $entry_ids);
	$i = 0;
	foreach ($coppie as $coppia) {
	    $coppia_valori = explode("=", $coppia);
	    $vars['entry_id'] = urldecode($coppia_valori[1]);
	    $vars['order_by'] = $i;
	    $i++;
	    $this->EE->db->insert('simply_order_tree', $vars);
	}
    }

    /***********************************************
     * FUNCTIONS TO GET HELP IN OTHER FUNCTIONS	   *
     ***********************************************/

    /*
     * New content wrapper to call the view file.
     * From version 0.7
     */

    function content_wrapper($content_view, $lang_key, $vars = array()) {

	// Load the correct view on the _wrapper 
	$vars['content_view'] = $content_view;
	$vars['_base'] = $this->_base_url;
	$vars['_form_base'] = $this->_form_base;

	// Add assets to the head:
	$this->_add_assets();

	// If you provide a title extra in the calling function you get .. an extra title
	$title_extra = (isset($vars['title_extra'])) ? ': ' . $vars['title_extra'] : '';

	// Lang key is required to know which key take from the language file.
	// $this->EE->cp->set_variable('cp_page_title', lang($lang_key) . $title_extra);
	ee()->view->cp_page_title = $this->EE->lang->line('cp_page_title');

	// To make the breadcrumb management easier, I define it one time here.
	$this->EE->cp->set_breadcrumb($this->_base_url, lang('simply_order_module_name'));

	// I would like to add the drag and drop functionality. Uncomment below to enable js.

	$this->EE->cp->add_js_script(
		array('ui' => array('core', 'sortable', 'draggable'),
		)
	);

	// Now we can load the view.
	return $this->EE->load->view('_wrapper', $vars, TRUE);
    }

    /**
     * Questa funzione ci restituisce le liste di ordinamento se presenti,
     * altrimenti ci restituisce falso.
     */
    private function _get_simply_orders() {

	$query = $this->EE->db->get('simply_order');
	if ($query->num_rows() == 0) {
	    return FALSE;
	} else {
	    return($query);
	}
    }

    /*
     * This function give us the list of sites installed.
     * 
     * From version 0.9
     */

    private function _get_site_list() {

	$this->EE->db->select('site_id,site_label,site_name');
	$query = $this->EE->db->get('sites');
	if ($query->num_rows() > 0) {
	    return $query;
	} else {
	    return FALSE;
	}
    }

    /*
     * This function return a list of entries from a given site and channel.
     */

     private function _get_channel_entries($data) {
	$this->EE->db->select('channel_data.entry_id,
						   channel_data.field_id_18,
						   channel_titles.title');
	$this->EE->db->from('channel_titles');
	$this->EE->db->join('channel_data','channel_data.entry_id = channel_titles.entry_id');
	$this->EE->db->where('channel_titles.channel_id',$data['channel_id']);
	$query = $this->EE->db->get();
	
	if ($query->num_rows() == 0) {
	    return FALSE;
	} else {
	    return $query;
	}
	
    }
    /*
     * Function to get entries previously submitted on the admin area.
     */
    private function _get_old_order($data) {
	
	$this->EE->db->select('simply_order_tree.entry_id, channel_titles.title, channel_data.field_id_18');
	$this->EE->db->from('simply_order_tree');
	$this->EE->db->join('channel_titles','channel_titles.entry_id = simply_order_tree.entry_id');
	$this->EE->db->where('simply_order_tree.id_simply_order',$data['id_simply']);
	$this->EE->db->join('channel_data','channel_data.entry_id = simply_order_tree.entry_id');
	$this->EE->db->order_by('order_by', 'asc');
	$query = $this->EE->db->get();
	return $query;
	
    }
    
    /*
     * Function to get entries not ordered
     */
    private function _get_exor_entries($data) {
	
	
    }

    /*
     * This function add js scripts and 
     */

    private function _add_assets() {
	$this->EE->cp->add_to_head('<link type="text/css" href="' . $this->theme_base . 'css/simply_order.css" rel="stylesheet" />');
    }

}

// END CLASS

/* End of file mcp.simply_order.php */
/* Location: ./system/expressionengine/third_party/modules/simply_order/mcp.simply_order.php */