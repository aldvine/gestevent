<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Event;
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
        $form = $this->createForm(EventType::class, $event, [
            'action' => $this->generateUrl('addEvent',['id'=>$user->getId()])]);
        $form->handleRequest($request);
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
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()){
            return $this->render('event/edit.html.twig', [
                'event' => $event,
                'edit_event_form' => $form->createView(),
            ]);
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
        dump($event);
        return $this->render('event/show.html.twig', ['events'=>$event]);
    }
}
b