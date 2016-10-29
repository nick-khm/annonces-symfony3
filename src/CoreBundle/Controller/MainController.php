<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Main Controller
 */
class MainController extends Controller
{
    /**
     * Main page
     * @return [type] [description]
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $flash_msgs = $session->get('flash_msgs');
        $session->remove('flash_msgs');
        return $this->render('CoreBundle:Main:index.html.twig', array('flash_msgs' => $flash_msgs));
    }

    /**
     * Render contact page
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function contactAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('flash_msgs', array('contact'=>"La page de contact n'est pas encore disponible, merci de revenir plus tard."));
        return $this->redirectToRoute('core_homepage');
    }
}
