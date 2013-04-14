<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modifier_profil extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('inscription_model', 'inscriManager');
        $this->twig->addFunction('getsessionhelper');
    }
        
    public function index()
    {
        //$this->form_validation->set_error_delimiters('<p class="form_erreur">', '</p>');
        
        $this->form_validation->set_rules('nom', '\'Nom\'', 'trim|required|max_length[30]|alpha_dash|encode_php_tags|xss_clean');
        $this->form_validation->set_rules('email', '\'Email\'', 'trim|required|valid_email|xss_clean');
        
        if($this->form_validation->run())
        {
            if(!$this->inscriManager->updateProfil(getsessionhelper()['id']))
                echo 'Probleme ID. Fonction: models/inscription_model/updateProfil';
            else
                //unset session data et set it again!!
                redirect('/profil');
        }
        else
        {
            $this->twig->render('modifier_profil_view');
        }
    }
    
}

?>


