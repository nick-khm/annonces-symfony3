<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;

// use OC\PlatformBundle\Entity\Skill;

class AdvertController extends Controller
{
    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findAll();

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
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findAll();

        return $this->render(
            'OCPlatformBundle:Advert:index.html.twig',
            array(
                'listAdverts' => $listAdverts
            )
        );
    }

    public function viewAction($id)
    {
        /*// On récupère le repository
        $em = $this->getDoctrine()
          ->getManager();

        // On récupère l'entité correspondante à l'id $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
          ->getRepository('OCPlatformBundle:Application')
          ->findBy(array('advert' => $advert));

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
        'advert' => $advert,
        'listApplications' => $listApplications
        ));*/

        $em = $this->getDoctrine()->getManager();


        /*$cat_repo = $em->getRepository('OCPlatformBundle:Category');
        $new_advert = new Advert();
        $new_advert->setTitle('Second title');
        $new_advert->setAuthor('Second author');
        $new_advert->setContent('Second content');
        $new_advert->addCategory($cat_repo->find(3));
        $new_advert->addCategory($cat_repo->find(5));
        $em->persist($new_advert);
        $em->flush();*/


        // On récupère l'annonce $id
        $advert = $em
          ->getRepository('OCPlatformBundle:Advert')
          ->find($id)
        ;

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        

        // $advert->setNbApplications(count($listApplications));
        $application1 = new Application();
        $application1->setAuthor('Will');
        $application1->setContent("J'ai beaucoup d'expérience");
        $advert->addApplication($application1);
        $em->persist($application1);
        $em->flush();

        // On avait déjà récupéré la liste des candidatures
        $listApplications = $em
          ->getRepository('OCPlatformBundle:Application')
          ->findBy(array('advert' => $advert))
        ;

        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $em
          ->getRepository('OCPlatformBundle:AdvertSkill')
          ->findBy(array('advert' => $advert))
        ;

        $tests = $this->getDoctrine()->getManager()->getRepository('OCPlatformBundle:Advert')
            ->getAdvertWithCategories(array('Réseau'));
            // ->getAdvertWithCategories1(array('Réseau'));
            // ->getAdvertWithCategories1(array('Développeur', 'Intégrateur'));

        // print_r($test);

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
          'advert'           => $advert,
          'listApplications' => $listApplications,
          'listAdvertSkills' => $listAdvertSkills,
          'tests' => $tests
        ));
    }

    public function addAction(Request $request)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Création de l'entité Advert
        $advert = new Advert();
        $advert->setTitle('Dev NodeJS');
        $advert->setAuthor('Nick');
        $advert->setContent("Nous recherchons un développeur NodeJS");

        // On récupère toutes les compétences possibles
        $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

        // Pour chaque compétence
        foreach ($listSkills as $skill) {
            // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
            $advertSkill = new AdvertSkill();

            // On la lie à l'annonce, qui est ici toujours la même
            $advertSkill->setAdvert($advert);
            // On la lie à la compétence, qui change ici dans la boucle foreach
            $advertSkill->setSkill($skill);

            // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
            $advertSkill->setLevel('Expert');

            // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
            $em->persist($advertSkill);
        }

        // Création d'une première candidature
        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises.");

        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Nick');
        $application2->setContent("Je suis très motivé.");

        // On lie les candidatures à l'annonce
        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        $em->persist($application1);
        $em->persist($application2);

        // Doctrine ne connait pas encore l'entité $advert. Si vous n'avez pas défini la relation AdvertSkill
        // avec un cascade persist (ce qui est le cas si vous avez utilisé mon code), alors on doit persister $advert
        $em->persist($advert);

        // On déclenche l'enregistrement
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

        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        /*// La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }*/

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // Étape 2 : On déclenche l'enregistrement
        // $em->flush();

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array('advert' => $advert));
    }



    public function updateAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        $advert->setDate(new \Datetime());

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $em->flush();

        return $this->redirectToRoute('oc_platform_view', array('id' => $id));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // On déclenche la modification
        $em->flush();
        return $this->redirectToRoute('core_homepage');
        // return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function editImageAction($advertId)
    {
        $em = $this->getDoctrine()->getManager();

      // On récupère l'annonce
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($advertId);

      // On modifie l'URL de l'image par exemple
        $advert->getImage()->setUrl('test.png');

      // On n'a pas besoin de persister l'annonce ni l'image.
      // Rappelez-vous, ces entités sont automatiquement persistées car
      // on les a récupérées depuis Doctrine lui-même
      
      // On déclenche la modification
        $em->flush();

        return new Response('OK');
    }
}
