<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Limatotiga_mdl extends CI_Model {

	var $table = 'users';
	var $column_order = array(
		'id','ip_address','username','password','salt','email',
		'activation_code','forgotten_password_code','forgotten_password_time',
		'remember_code','created_on','last_login','active','first_name','last_name','company','phone',
		'idnumber','address','birthdat','gender','teamname','ideatitle','ideadesc','ideafile','memberstats',
		'headuserid','ideatype','loginmethod','cmsstatus',null);
	var $column_search = array('first_name','teamname','ideatitle','ideadesc');
	var $order = array('id' => 'asc'); 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);

		$i = 0;

		foreach ($this->column_search as $item)  
		{
			if($_POST['search']['value']) 
			{
				
				if($i===0) 
				{
					$this->db->group_start(); 
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) 
					$this->db->group_end(); 
			}
			$i++;
		}

		if(isset($_POST['order'])) 
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}
	public function gettop30user() {
		$this->db->select('users.*,nilai.*');
		$this->db->from($this->table);
		$this->db->join('nilai', 'nilai.idpeserta = users.id');
		$this->db->where('tiga =', 1);
		$this->db->order_by('idpeserta','asc') ;

         //      $this->db->where('status', "xto100");
		//$this->db->order_by("totalall", "desc");
		$this->db->limit('50');
		$query = $this->db->get(); 
		
		return $query->result_array();
	}
	public function nilaibaru($idpeserta)
    {
        $this->db->select_avg('xnilai');
        $this->db->from('screenrelation');
        //$this->db->join('screentahap', 'screentahap.idpeserta = screenrelation.idpeserta');
        $this->db->where('idpeserta', $idpeserta);
        $this->db->order_by('xnilai','desc') ;
        $query = $this->db->get();
        return $query;
    }
    public function nilaianyar($idpeserta)
    {
        $this->db->select_avg('xnilai');
        $this->db->from('screentahap');
        //$this->db->join('screentahap', 'screentahap.idpeserta = screenrelation.idpeserta');
        $this->db->where('idpeserta', $idpeserta);
        $this->db->where('idscreener !=', 14 );
        $this->db->order_by('xnilai','desc') ;
        $query = $this->db->get();
        return $query;
    }

}
