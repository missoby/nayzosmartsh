<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Photo extends CI_Controller
{
    private $fb;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('photo_model', 'photoManager');
        $this->load->model('statut_model', 'statutManager');
        
        $this->twig->addFunction('getsessionhelper');
        
        //Facebook Connect
            require_once 'assets/facebook_sdk_src/facebook.php';
            $param = array();
            $param['appId'] = '444753728948897';
            $param['secret'] = '5ab7c77a75fd646619cc98bde08e37e3';
            $param['fileUpload'] = true; // pour envoyer des photos
            $param['cookie'] = false;
            $this->fb = new Facebook($param);
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
    
    public function publierfb($photo, $msg)
    {
        $uid = $this->fb->getUser();
        //$img_url = base_url() . 'uploads/'.$photo;
        $img_url = 'C:/wamp/www/SmartShare/uploads/photo3.png';
        
        
        if (empty($uid)) //User non connecté sur facebook
        {
            $ppp = array();
            $ppp['redirect_uri'] = 'http://localhost:8094/photo/publierfb/' . $photo . '/' . $msg;
            $ppp['display'] = 'popup';
            redirect($this->fb->getLoginUrl($ppp));
        }
        else //User connecté sur facebook
        {
            try
            {
                $ret_obj = $this->fb->api('/me/photos', 'POST', array('source' => '@'.$img_url, 'message' => $msg));
                
                /*$ret_obj = $this->fb->api('/me/feed', 'POST',
                                    array(
                                      'link' => 'www.lol.com',
                                      'message' => 'Test'
                                 ));*/
                
                echo '<pre>';
                print_r($ret_obj);
                echo '</pre>';
            }
            catch(FacebookApiException $e)
            {
                echo 'Exception: ' . $e->getMessage();
            }   
        }
    }
    
}

?>


