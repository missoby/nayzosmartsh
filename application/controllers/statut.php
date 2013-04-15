<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statut extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('statut_model', 'statutManager');
    }
        
    public function index()
    {
        $data['res'] = $this->statutManager->getAllNonAttachStatus(getsessionhelper()['id']);
        foreach($data['res'] as $value)
        {
            if($value->partage == 0)
                $value->etat = 'Statut non partagé';
            else
                $value->etat = 'Statut partagé';
        }
        $this->twig->render('statut_view', $data);
    }
    
    public function nouveau()
    {
        //$this->form_validation->set_error_delimiters('<p class="form_erreur">', '</p>');
        
        $this->form_validation->set_rules('localisation', '\'Localisation\'', 'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('statut', '\'Statut\'', 'trim|required|xss_clean');
        
        if($this->form_validation->run())
        {
            $this->statutManager->addStatut();
            redirect('/statut');
        }
        else
        {
            $this->twig->render('nouveau_statut_view');
        }
    }

    public function modifier($id)
    {
        if(empty($id))
            exit('Erreur ID Statut');
        
        $data = array();
        $data['s'] = $this->statutManager->getStatut($id)->statut;
        $data['l'] = $this->statutManager->getStatut($id)->localisation;
        
        //$this->form_validation->set_error_delimiters('<p class="form_erreur">', '</p>');
        
        $this->form_validation->set_rules('localisation', '\'Localisation\'', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('statut', '\'Statut\'', 'trim|required|xss_clean');
        
        if($this->form_validation->run())
        {
            $this->statutManager->updateStatut($id);
            redirect('/statut');
        }
        else
        {
            $this->twig->render('modifier_statut_view', $data);
        }
    }


    public function supprimer($id)
    {
        if(empty($id))
            exit('Erreur ID Statut');
        
        $this->statutManager->deleteStatut($id);
        redirect('/statut');
    }
    
}

?>


