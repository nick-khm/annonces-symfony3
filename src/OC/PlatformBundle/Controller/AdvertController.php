<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;

class AdvertController extends Controller
{
    public function menuAction($limit)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
    // on la récupérera depuis la BDD !
        $listAdverts = array(
          array('id' => 2, 'title' => 'Recherche développeur Symfony'),
          array('id' => 5, 'title' => 'Mission de webmaster'),
          array('id' => 9, 'title' => 'Offre de stage webdesigner')
          );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
          // Tout l'intérêt est ici : le contrôleur passe
          // les variables nécessaires au template !
          'listAdverts' => $listAdverts
          ));
    }

    /**
     * Main page
     * @return [type] [description]
     */
    public function indexAction()
    {
        $listAdverts = array(
            array(
        'title' => 'Recherche développpeur Symfony',
        'id' => 1,
        'author' => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date' => new \Datetime(),),
            array(
        'title' => 'Mission de webmaster',
        'id' => 2,
        'author' => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date' => new \Datetime(), ),
        array(
        'title' => 'Offre de stage webdesigner',
        'id' => 3,
        'author' => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date' => new \Datetime(), ),
        );

        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
        'listAdverts' => $listAdverts));
    }

    public function viewAction($id)
    {
        // On récupère le repository
        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('OCPlatformBundle:Advert')
        ;

        // On récupère l'entité correspondante à l'id $id
        $advert = $repository->find($id);

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
        'advert' => $advert
        ));
    }

    public function addAction(Request $request)
    {
        // Création de l'entité
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
        // On peut ne pas définir ni la date ni la publication,
        // car ces attributs sont définis automatiquement dans le constructeur

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Étape 1 : On « persiste » l'entité
        $em->persist($advert);

        // Étape 2 : On « flush » tout ce qui a été persisté avant
        $em->flush();

        // Reste de la méthode qu'on avait déjà écrit
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        // $antispam = $this->container->get('oc_platform.antispam');
        // $text = 'ggg';
        // if ($antispam->isSpam($text)) {
        //     throw new \Exception('Votre message a été détecté comme spam !');
        // }

        return $this->render('OCPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        $advert = array(
          'title' => 'Recherche développpeur Symfony2',
          'id' => $id,
          'author' => 'Alexandre',
          'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
          'date' => new \Datetime(),
          );

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array('advert' => $advert));
    }

    public function deleteAction($id)
    {
        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }
}
