<?php
class Inscription_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    function get_all_desabled()
    {
        $query = $this->db->get_where('utilisateur', array('activer' => 0));
        return $query->result();
    }

    function insert_inscription($response='')
    {
        if(!$response)
        $data = array(
            'password' => sha1($this->input->post('password')),
            'email'    => $this->input->post('email'),
            'nom'      => $this->input->post('nom'),            
            'date_inscription' => date('Y-m-d H:i:s'),
            'activer' => 0,
        );
        else
            $data = array(
            'password' => sha1($response['registration']['password']),
            'email'    => $response['registration']['email'],
            'nom'      => $response['registration']['name'],            
            'date_inscription' => date('Y-m-d H:i:s'),
            'activer' => 1,
        );

        $this->db->insert('utilisateur', $data);
    }

    function update_activer($id)
    {
        $data = array('activer' => 1);

        $this->db->update('utilisateur', $data, array('id' => $id));
    }
    
    function login(){
        $this->db->select('id, nom, email, password, activer')
                ->from('utilisateur')
                ->where('email', $this->input->post('email'))
                ->where('password', sha1($this->input->post('password')))
                ->limit(1);
        $query = $this->db->get();
        if($query->num_rows()==1)
            return $query->result();
        else
            return false;
    }

}