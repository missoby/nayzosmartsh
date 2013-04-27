<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Photo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('photo_model', 'photoManager');
        $this->load->model('statut_model', 'statutManager');
        
        $this->twig->addFunction('getsessionhelper');
    }
        
    public function index($categorie = "")
    {
        $data = array();
        
        if(empty($categorie))
        {
            $data['res'] = $this->photoManager->getAllPhotos(getsessionhelper()['id']);
            foreach($data['res'] as $value)
            {
                $value->statut = $this->statutManager->getStatut($value->statut)->statut;
                
                if($value->partage == 0)
                    $value->etat = 'Image non partagé';
                else
                    $value->etat = 'Image partagé';
            }
            
            $this->twig->render('photo_view', $data);
        }
        else
        {
            $data['res'] = $this->photoManager->getPhotosByCategorie(getsessionhelper()['id'], $categorie);
            foreach($data['res'] as $value)
            {
                $value->statut = $this->statutManager->getStatut($value->statut)->statut;
                
                if($value->partage == 0)
                    $value->etat = 'Image non partagé';
                else
                    $value->etat = 'Image partagé';
            }
            $this->twig->render('photo_view', $data);
        }
        
    }
    
    public function categorie()
    {
        $this->twig->render('categorie_view');
    }
    
    public function modifier($id)
    {
        if(empty($id))
            exit('Erreur ID Photo');
        
        $data = array();
        $data['s'] = $this->statutManager->getStatut($this->photoManager->getPhoto($id)->statut)->statut;
        $data['l'] = $this->photoManager->getPhoto($id)->localisation;
        
        //$this->form_validation->set_error_delimiters('<p class="form_erreur">', '</p>');
        
        $this->form_validation->set_rules('localisation', '\'Localisation\'', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('statut', '\'Statut\'', 'trim|required|xss_clean');
        
        if($this->form_validation->run())
        {
            $this->photoManager->updatephoto($id);
            redirect('/photo');
        }
        else
        {
            $this->twig->render('modifier_photo_view', $data);
        }
    }

    public function supprimer($id)
    {
        if(empty($id))
            exit('Erreur ID image');
        
        $this->photoManager->deletePhoto($id);
        redirect('/photo');
    }
    
}

?>


