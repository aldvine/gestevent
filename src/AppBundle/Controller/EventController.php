<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Event;

/**
 * @Route("/{_locale}/Event")
 */

class EventController extends Controller
{
    /**
     * @Route("/", name="listEvent")
     */
    public function listAction(Request $request)
    {
        //recuperer tout les evenement et les afficher 
        // replace this example code with whatever you need

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

     /**
     * @Route("/new", name="addEvent")
     */

//exemple a modifier et adapater
    public function newAction()
    {
        $event = new Event();
        $form = $this->createForm(
            eventType::class,
            $event,
            [
                'action' => $this->generateUrl('addevent', ['id' => $user->getId()])
            ]
        );
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('event/new. html. twig', [
                'add_event_form' => $form->createView(),
                'user' => $user,
            ]);
        }
        $event->setuser($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();
        $this->addFlash('notice', 'Youy create a new event !');
        return $this->redirectToRoute('showUser', ['id' => $user->getId()]);
    }
}
