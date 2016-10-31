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
        return $this->render('CoreBundle:Main:index.html.twig');
    }

    /**
     * Render contact page
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function contactAction(Request $request)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('flash_msgs', "La page de contact n'est pas encore disponible, merci de revenir plus tard.");
        return $this->redirectToRoute('core_homepage');
    }
}
