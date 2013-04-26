<?php
class Inscription_model extends CI_Model
{
    private $table = 'utilisateur';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_all_desabled()
    {
        $query = $this->db->get_where($this->table, array('activer' => 0));
        return $query->result();
    }
    
    public function getUserData($fid) //Identique à login() en desous
    {
        $query = $this->db->where('facebook_id', $fid)
                          ->get($this->table);
        
        if($query->num_rows() >= 1) //Normalement == 1 car l'id est unisue donc il existe un seul row mais on est en mode test donc on peut avoir plusieurs ligne avec le meme ID facebook
            return $query->row();
        else
            return false;
    }
    
    public function update_facebookID($id, $fid)
    {
        $data = array('facebook_id' => $fid);
        $this->db->update($this->table, $data, array('id' => $id));
    }

    public function insert_inscription($response='')
    {
        if(!$response)
        $data = array(
            'password' => sha1($this->input->post('password')),
            'email'    => $this->input->post('email'),
            'nom'      => $this->input->post('nom'),            
            'date_inscription' => date('Y-m-d H:i:s'),
            'activer' => 0,
            'facebook_id' => 0
            );
        else
            $data = array(
            'password' => sha1($response['registration']['password']),
            'email'    => $response['registration']['email'],
            'nom'      => $response['registration']['name'],            
            'date_inscription' => date('Y-m-d H:i:s'),
            'activer' => 1,
            'facebook_id' => $response['user_id']
            );

        $this->db->insert($this->table, $data);
    }
    
    public function checkMail($mail, $fid)
    {
        $query = $this->db->where('email', $mail)->get($this->table);
        
        if($query->num_rows() == 1)
        {
            $res = $query->row();
            $data = array('facebook_id' => $fid);
            $this->db->update($this->table, $data, array('id' => $res->id));
            return true;
        }
        else
            return false;
    }

    public function update_activer($id)
    {
        $data = array('activer' => 1);

        $this->db->update($this->table, $data, array('id' => $id));
    }
    
    public function login()
    {
        $query = $this->db->where('email', $this->input->post('email'))
                          ->where('password', sha1($this->input->post('password')))
                          ->get($this->table);
        
        if($query->num_rows() == 1)
            return $query->row();
        else
            return false;
    }

    public function updateProfil($id)
    {
        if(empty($id))
            return false;
        
        $data = array('nom' => $this->input->post('nom'), 'email' => $this->input->post('email'));
        $this->db->update($this->table, $data, array('id' => $id));
        return true;
    }
    
    public function getPassword($id)
    {
        if(empty($id))
            return false;
        
        return $this->db->select('password')->from($this->table)->where('id', $id)->get()->row()->password;
    }

    public function setPassword($id)
    {
        if(empty($id))
            return false;
        
        $data = array('password' => sha1($this->input->post('new')));
        $this->db->update($this->table, $data, array('id' => $id));
        return true;
    }

}

?>


