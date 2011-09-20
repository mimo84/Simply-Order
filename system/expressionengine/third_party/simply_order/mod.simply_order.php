<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Simply Order - Module file
 *
 * @package	ExpressionEngine
 * @category	Module
 * @author	Maurizio Napoleoni
 * @link	http://www.zoomingin.net/
 * @copyright 	Copyright (c) 2011 Maurizio Napoleoni
 * @license   	http://creativecommons.org/licenses/MIT/  MIT License
 *
 */
class Simply_order {

    function get() {
        $output = "";
        $this->EE = & get_instance();

        $query = $this->EE->db->get('simply_order');

        foreach ($query->result() as $row) {
            $output = $row->entry_id;
        }

        return $output;
    }

}

/* End of file mod.simply_order.php */
/* Location: ./system/expressionengine/third_party/simply_order/mod.simply_order.php */