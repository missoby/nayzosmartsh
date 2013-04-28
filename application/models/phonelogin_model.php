<?php
class Phonelogin_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    function login()
    {
        $this->db->select('id, nom, email, password, activer')
                ->from('utilisateur')
                ->where('email', $this->input->post('email') )
                ->where('password', sha1($this->input->post('password')))
                ->limit(1);
        $query = $this->db->get();
        if($query->num_rows()==1)
            return $query->result();
        else
            return false;
    }
    
    function setStatut()
    {
        $data = array(
            'statut'     => $this->input->post('statut'),
            'user'       => $this->input->post('id'),
            'date_envoi' => date('Y-m-d H:i:s')
        );

        $this->db->insert('statut', $data);
    }
    
    function saveuploadimage($photo)
    {
        $statut = null;
        if($this->input->post('statut'))
        {
            $this->setStatut();
            $statut = $this->db->insert_id();
        }
        
        $data = array(
            'photo'     => $photo['file_name'],
            'statut'     => $statut,
            'user'       => $this->input->post('id'),
            'date_envoi' => date('Y-m-d H:i:s'),
            'categorie' => $this->input->post('categorie')
        );

        $this->db->insert('photo', $data);
    }

}