<?php

namespace OC\PlatformBundle\Controller;

// N'oubliez pas ce use :
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdvertController extends Controller
{
  public function indexAction()
  {
    $url = $this->get('router')->generate('oc_platform_view', array('id'=>5), UrlGeneratorInterface::ABSOLUTE_URL);
    $content = $this
    ->get('templating')
    ->render('OCPlatformBundle:Advert:index.html.twig', array('nom' => 'winzou', 'url' => $url, 'advert_id' => 5));
    
    return new Response($content);
  }

  public function viewAction($id, Request $request)
  {
    print_r($request->getSession()->getFlashBag());
        // return $this->redirectToRoute('oc_platform_home');
        $tag = $request->query->get('tag');
        // // return new Response("Affichage de l'annonce d'id : ".$id." avec tag param: ".$tag);
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array('id'=>$id,'tag'=>$tag));
  }

  public function viewSlugAction($slug, $year, $_format)
    {
        return new Response(
            "On pourrait afficher l'annonce correspondant au
            slug '".$slug."', créée en ".$year." et au format ".$_format."."
        );
    }

  public function viewSessionAction($id, Request $request)
  {
    // Récupération de la session
    $session = $request->getSession();
    
    // On récupère le contenu de la variable user_id
    $userId = $session->get('user_id');

    // On définit une nouvelle valeur pour cette variable user_id
    $session->set('user_id', $id);

    // On n'oublie pas de renvoyer une réponse
    return new Response("<body>Je suis une page de test, je n'ai rien à dire</body>");
  }

  public function addAction(Request $request)
  {
    $session = $request->getSession();
    
    // Bien sûr, cette méthode devra réellement ajouter l'annonce
    
    // Mais faisons comme si c'était le cas
    $session->getFlashBag()->add('info', 'Annonce bien enregistrée');

    // Le « flashBag » est ce qui contient les messages flash dans la session
    // Il peut bien sûr contenir plusieurs messages :
    $session->getFlashBag()->add('info', 'Oui oui, elle est bien enregistrée !');

    // Puis on redirige vers la page de visualisation de cette annonce
    return $this->redirectToRoute('oc_platform_view', array('id' => 5));
  }
}