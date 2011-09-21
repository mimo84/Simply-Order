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
		$vars['list'][$row['id_simply_order']]['order_tag'] = $row['order_tag'];
		$vars['list'][$row['id_simply_order']]['site_id'] = $row['site_id'];
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
     * This function is needed to add a new entries ordering.
     * 
     */

    function add_new() {

	$this->EE->form_validation->set_rules('order_tag', 'required');
	$this->EE->form_validation->set_rules('site_id', 'required');

	if ($this->EE->form_validation->run() == FALSE) {
	    // Definisco la variabile da passare alla vista
	    $vars = array(
		'cp_page_title' => $this->EE->lang->line('new_order'),
		'form_action' => $this->_form_base . AMP . 'method=add_new',
	    );
	    // Faccio caricare la vista per aggiungere un nuovo ordering
	    return $this->content_wrapper('add_new', 'welcome', $vars);
	} else {

	    $data['order_tag'] = $this->EE->input->post('order_tag');
	    $data['site_id'] = $this->EE->input->post('site_id');
	    $vars['query'] = $this->EE->db->insert('simply_order', $data);
	    $vars['cp_page_title'] = $this->EE->lang->line('new_order');
	    $vars['form_action'] = $this->_form_base . AMP . 'method=add_new';

	    return $this->content_wrapper('add_new', 'welcome', $vars);
	}
    }

    function update_record() {

	$vars['site_id'] = $this->EE->input->get('site_id');
	$vars['id_simply'] = $this->EE->input->get('id_simply_order');

	$vars['cp_page_title'] = $this->EE->lang->line('edit_single');
	$vars['form_action'] = $this->_form_base . AMP . 'method=edit_single';

	$data['site_id'] = '1';
	$data['channel_id'] = '2';

	$entries = $this->_get_channel_entries($data);
	if ($entries) {
	    $vars['entries'] = $entries;
	    return $this->content_wrapper('edit_single', 'edit_single', $vars);
	} else {
	    $this->index();
	}
    }

    /*     * ******************************************
     * FUNCTIONS TO GET HELP IN OTHER FUNCTIONS
     * ****************************************** */

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
	$this->EE->cp->set_variable('cp_page_title', lang($lang_key) . $title_extra);

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

	$this->EE->db->where('channel_id', $data['channel_id']);
	$this->EE->db->where('site_id', $data['site_id']);
	$query = $this->EE->db->get('channel_titles');

	if ($query->num_rows() == 0) {
	    return FALSE;
	} else {
	    return $query;
	}
    }

    /*
     * This function add js scripts and 
     */

    private function _add_assets() {
	$this->EE->cp->add_to_head('<link type="text/css" href="' . $this->theme_base . 'css/simply_order.css" rel="stylesheet" />');

	$js_script = "<script type='text/javascript'>";
	$js_script .= "$(document).ready(function(){ 					   
			$(function() {
			    $('#availables .element').droppable({ 
					opacity: 0.6, 
					cursor: 'move'			    
				});
			    });
			});";
	$js_script .= "</script>";
	
	// $this->EE->cp->add_to_head($js_script);
    }
    

}

// END CLASS

/* End of file mcp.simply_order.php */
/* Location: ./system/expressionengine/third_party/modules/simply_order/mcp.simply_order.php */