<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Inscription;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Input\StringInput;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Event controller.
 *
 * @Route("{_locale}/event")
 */
class EventController extends Controller
{
    /**
     * Lists all event entities.
     *
     * @Route("/", name="event_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        // recuperation de tout les evenement
        // $em = $this->getDoctrine()->getManager();
        // $events = $em->getRepository('AppBundle:Event')->findAll();
        $breadcrumbs = $this->getBreadcrumbs();
        // recuperation d'uniquement ceux à venir
        $events = $this->getDoctrine()
            ->getRepository('AppBundle:Event')
            ->findAllGreaterThanDate(date('Y-m-d H:i:s'));

        // dump($events);
        return $this->render('event/index.html.twig', array(
            'events' => $events,
            // 'date_now' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Creates a new event entity.
     *
     * @Route("/new", name="event_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        
        $event = new Event();

        // création du formaulaire
        $form = $this->createForm('AppBundle\Form\EventType', $event);
        $form->handleRequest($request);

        // fil d'ariane
        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addRouteItem($this->get('translator')->trans('event.create'), "event_new");

        // validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // ----ajout de l'utilisateur actuel----
            $event->setUser($this->getUser());
            //-------

            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }
        // rendu de la vue
        return $this->render('event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     */
    public function showAction(Event $event)
    {

        $deleteForm = $this->createDeleteForm($event);
        // recherche des inscriptions lié à l'evenement
        $em = $this->getDoctrine()->getManager();
        $inscriptions = $em->getRepository('AppBundle:Inscription')->findBy(array('event' => $event->getId()));
       // compteur d'inscription qui sert au calcul du nombre de place restante dans la vue
        $nbindividu = count($inscriptions);

        $users = new ArrayCollection();
// pour toutes les inscriptions on ajoutes l'utilisateur dans le tableau
        foreach ($inscriptions as $inscription) {
            // recuperation de l'utilisateur
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $inscription->getUser()->getId()));
            $id = $inscription->getId();
            $users["$id"] = $user;
        }
        // fil d'ariane
        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addRouteItem($this->get('translator')->trans('event.show'), "event_show", [
            'id' => $event->getId(),
        ]);
            // rendu de la vue 
        return $this->render('event/show.html.twig', array(
            'users' => $users,
            'event' => $event,
            'nbindividu' => $nbindividu,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * plus utilisé
     *
     * @Route("/Byplace", name="event_showByPlace")
     * @Method("POST")
     * 
     */
    public function showByPlaceAction(Request $request)
    {
        // fonction plus utilisé remplace par filterAction
        $place = $request->request->get('place');
        $breadcrumbs = $this->getBreadcrumbs();

         // recuperation d'uniquement ceux à venir
        $events = $this->getDoctrine()
            ->getRepository('AppBundle:Event')
            ->findByPlace(date('Y-m-d H:i:s'), $place);


        return $this->render('event/index.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     * @Route("/Bydate", name="event_showByDate")
     * @Method("POST")
     */
    public function showByDateAction(Request $request)
    {
           // fonction plus utilisé remplace par filterAction
        $date = $request->request->get('date');
        $Events = $this->getDoctrine()->getRepository('AppBundle:Event')->findAllGreaterThanDate(date('Y-m-d H:i:s'));
        $events = new ArrayCollection();
        $breadcrumbs = $this->getBreadcrumbs();

        foreach ($Events as $event) {
            $dateTime = date_format($event->getDate(), "Y-m-d");
            if ($date == $dateTime) {
                $events[] = $event;
            }
        }
        return $this->render('event/index.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     * @Route("/Bytheme", name="event_showByTheme")
     * @Method("POST")
     */
    public function showByThemeAction(Request $request)
    {
           // fonction plus utilisé remplace par filterAction
        $theme = $request->request->get('theme');
        $breadcrumbs = $this->getBreadcrumbs();

        if (empty($theme)) {
            // recuperation d'uniquement ceux à venir
            $events = $this->getDoctrine()
                ->getRepository('AppBundle:Event')
                ->findAllGreaterThanDate(date('Y-m-d H:i:s'));

        } else {
         // recuperation d'uniquement ceux à venir
            $events = $this->getDoctrine()
                ->getRepository('AppBundle:Event')
                ->findByTheme(date('Y-m-d H:i:s'), $theme);
        }

        return $this->render('event/index.html.twig', array(
            'events' => $events,
        ));
    }
    
    /**
     * @Route("/Filter", name="event_filter")
     * @Method("POST")
     */
    
    public function filterAction(Request $request)
    {
        // recuperation des informations saisie
        $theme = $request->request->get('theme');
        $place = $request->request->get('place');
        $title = $request->request->get('title');
        $date = $request->request->get('date');

        // fil d'ariane
        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addRouteItem($this->get('translator')->trans('event.filter'), "event_filter");

         // recuperation d'uniquement ceux à venir avec les filtres
            $events = $this->getDoctrine()
                ->getRepository('AppBundle:Event')
                ->findByFilter(date('Y-m-d H:i:s'), $title, $place, $date, $theme);


        return $this->render('event/index.html.twig', array(
            'events' => $events,
        ));
    }
    /**
     *  affiche les evenement de l'utilisateur connecté
     * @Route("/list/MyEvent", name="event_user")
     * @Method("GET")
     */
    public function myEventsAction()
    {
        // fil d'ariane
        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addRouteItem($this->get('translator')->trans('event.myevent'), "event_user");
         // recuperation des evenement de l'utilsiateur
            $events = $this->getDoctrine()
                ->getRepository('AppBundle:Event')
                ->findByUser(date('Y-m-d H:i:s'), $this->getUser());
        // rendu vue
        return $this->render('event/index.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * affiche un formulaire pour editer un evenement
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Event $event)
    {
        // creation du formulaire de suppression (un bouton)
        $deleteForm = $this->createDeleteForm($event);
        // creation formualaire modification
        $editForm = $this->createForm('AppBundle\Form\EventType', $event);
        $editForm->handleRequest($request);
        // verification des champs et du formulaire en général
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            // redirection
            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }
        // fil d'ariane
        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addRouteItem($this->get('translator')->trans('event.edit'), "event_edit", [
            'id' => $event->getId(),
        ]);
        // rendu
        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    // retourne le fil d'ariane de ce controlleur
    public function getBreadcrumbs()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        // Simple example
        $breadcrumbs->addRouteItem($this->get('translator')->trans('event.list'), "event_index");
        return $breadcrumbs;
    }

    /**
     * Supprime un evenement
     *
     * @Route("/{id}", name="event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Event $event)
    {
        // creation de formualaire suppression
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);
        // vérification formualaire et suppression  evenement
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }
        // redirection
        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
