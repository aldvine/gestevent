<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use AppBundle\Form\EventType;
/**
 * @Route("/{_locale}/event")
 */

class EventController extends Controller
{

    /**
     * @Route("/", name="Event_index")
     * @return \Symfony\Component\HttpFundation\Response
     * @throws \LogicException
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $events = $repository->findAll();
        dump($events);
        return $this->render('event/index.html.twig', ['events'=>$events]);
    }

    /**
     * @Route("/add/{id}", name="addEvent")
     * @return \Symfony\Component\HttpFundation\Response
     * @throws \LogicException
     */
    public function newAction(User $user, Request $request)
    {
        $event = new Event();
        echo $user->getId();
        $form = $this->createForm(EventType::class, $event, [
            'action' => $this->generateUrl('addEvent')]);
        echo "2-";
        $form->handleRequest($request);
        echo "3-";
        if (!$form->isSubmitted() || !$form->isValid()){
            return $this->render('event/new.html.twig', [
                'add_event_form' => $form->createView(),
                'user' => $user,
            ]);
        }
        $event->setEvent($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();

        $this->addFlash('notice', 'Vous avez créé un évênement !');
        return $this->redirectToRoute('Event_index');
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="deleteEvent")
     */
    public function deleteAction(Event $event,Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('Event_index');
    }

    /**
     * @Route("/edit/{id}", requirements={"id":"\d+"}, name="editEvent")
     */
    public function updateAction(Event $event, Request $request)
    {
        echo 'test';
        $form = $this->createForm(EventType::class, $event);
        echo '   1-';
        $form->handleRequest($request);
        echo '   2-';
        if (!$form->isSubmitted() || !$form->isValid()){
            echo '   3-';
            return $this->render('event/edit.html.twig', [
                'event' => $event,
                'edit_event_form' => $form->createView(),
            ]);
            echo '   4-';
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->redirectToRoute('Event_index');
    }

    /**
     * @Route("/show/{id}", requirements={"id":"\d+"}, name="showEvent")
     */
    public function showAction(Event $event,Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $event = $repository->find($event->getId());
        return $this->render('event/show.html.twig', ['event'=>$event]);
    }
}