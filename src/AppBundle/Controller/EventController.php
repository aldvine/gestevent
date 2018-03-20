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
        $form = $this->createForm('AppBundle\Form\EventType', $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // ----ajout de l'utilisateur actuel----
            $event->setUser($this->getUser());
            //-------

            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

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

        $em = $this->getDoctrine()->getManager();
        $inscriptions = $em->getRepository('AppBundle:Inscription')->findBy(array('event' => $event->getId()));
        $nbindividu = count($inscriptions);
        $users = new ArrayCollection();
        foreach ($inscriptions as $inscription) {

            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $inscription->getUser()->getId()));
            $id = $inscription->getId();
            $users["$id"] = $user;
        }

        return $this->render('event/show.html.twig', array(
            'users' => $users,
            'event' => $event,
            'nbindividu' => $nbindividu,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     * @Route("/Byplace", name="event_showByPlace")
     * @Method("POST")
     * 
     */
    public function showByPlaceAction(Request $request)
    {

    
          $place = $request->request->get('place');
    
        
 // $em = $this->getDoctrine()->getManager();
        // $events = $em->getRepository('AppBundle:Event')->findBy(array('place' => $place));

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
        $date = $request->request->get('date');
        $Events = $this->getDoctrine()->getRepository('AppBundle:Event')->findAllGreaterThanDate(date('Y-m-d H:i:s'));
        $events = new ArrayCollection();
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
        $theme = $request->request->get('theme');

        if (empty($theme)) {
            // recuperation d'uniquement ceux à venir
            $events = $this->getDoctrine()
                ->getRepository('AppBundle:Event')
                ->findAllGreaterThanDate(date('Y-m-d H:i:s'));

        } else {
                   // $em = $this->getDoctrine()->getManager();
        // $events = $em->getRepository('AppBundle:Event')->findBy(array('theme' => $theme));

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
     * Displays a form to edit an existing event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('AppBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }
        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     *
     * @Route("/{id}", name="event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Event $event)
    {
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

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
