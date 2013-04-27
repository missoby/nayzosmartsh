<?php
class Photo_model extends CI_Model
{
    private $table = 'photo';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function getPhoto($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }
    
    public function getAllPhotos($id)
    {
        return $this->db->where('user', $id)
                 ->order_by('date_envoi', 'desc')
                 ->get($this->table)
                 ->result();
    }
    
    public function getPhotosByCategorie($id, $cat)
    {
        $query = $this->db->get_where($this->table, array('user' => $id, 'categorie' => $cat));
        return $query->result();
    }
    
    public function updatePhoto($id)
    {
        if(empty($id))
            return false;
        
        // Insertion du nv commentaire
        $ddd = date('Y-m-d H:i:s');
        $ds = array('statut' => $this->input->post('statut'),
                    'date_envoi' => $ddd,
                    'localisation' => $this->input->post('localisation'),
                    'partage' => 0,
                    'attache' => 1,
                    'user' => getsessionhelper()['id']);
        
        $this->db->insert('statut', $ds);
        
        // Get hid ID
        $sid = $this->db->where('date_envoi', $ddd)->get('statut')->row()->id;
        
        $data = array('localisation' => $this->input->post('localisation'),
                      'statut'       => $sid,
                      'categorie'    => $this->input->post('cat'),
                      'date_envoi'   => $ddd);
        
        $this->db->update($this->table, $data, array('id' => $id));
        return true;
    }
    
    public function deletePhoto($id)
    {
        $this->db->delete($this->table, array('id' => $id));
    }

}

?>


