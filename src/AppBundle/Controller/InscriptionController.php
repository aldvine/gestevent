<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inscription;
use AppBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Inscription controller.
 *
 * @Route("{_locale}/inscription")
 */
class InscriptionController extends Controller
{
    /**
     * Lists all inscription entities.
     *
     * @Route("/", name="inscription_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        // liste uniquement de ses inscriptions
        $inscriptions = $em->getRepository('AppBundle:Inscription')->findBy(array('user' => $this->getUser()));
        $events = new ArrayCollection();
        foreach($inscriptions as $inscription){
            $event = $em->getRepository('AppBundle:Event')->findOneBy(array('id' => $inscription->getEvent()->getId()));
            $id = $inscription->getId();
            $events["$id"] = $event;
        }

        return $this->render('inscription/index.html.twig', array(
            'inscriptions' => $inscriptions,
            'events' => $events,
        ));
    }

    /**
     * Creates a new inscription entity.
     *
     * @Route("/new/{id}", name="inscription_new")
     * @Method({"POST"})
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->findOneBy(array('id' => $id));
        $resp = $em->getRepository('AppBundle:Inscription')->findOneBy(array('user' => $this->getUser(),'event'=>$event));
       
        if(empty($resp)){
            $inscription = new Inscription();
            $inscription->setDate(date_create());
            $inscription->setUser($this->getUser());
            
            $inscription->setEvent($event);
        
            $em->persist($inscription);
            $em->flush();
        }
        return $this->redirectToRoute('inscription_index');
    
    }

    /**
     * Finds and displays a inscription entity.
     *
     * @Route("/{id}", name="inscription_show")
     * @Method("GET")
     */
    public function showAction(Inscription $inscription)
    {
        $deleteForm = $this->createDeleteForm($inscription);

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->findOneBy(array('id' => $inscription->getEvent()->getId()));
        $inscriptions = $em->getRepository('AppBundle:Inscription')->findBy(array('event' => $event->getId()));
        $users = new ArrayCollection();
        foreach($inscriptions as $inscription){
            $user = $em->getRepository('AppBundle:User')->findBy(array('id' => $inscription->getUser()->getId()));
            $users[] = $user;
        }

        return $this->render('inscription/show.html.twig', array(
            'users' => $users,
            'event' => $event,
            'id' => $inscription->getId(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing inscription entity.
     *
     * @Route("/{id}/edit", name="inscription_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Inscription $inscription)
    {
        $deleteForm = $this->createDeleteForm($inscription);
        $editForm = $this->createForm('AppBundle\Form\InscriptionType', $inscription);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('inscription_edit', array('id' => $inscription->getId()));
        }

        return $this->render('inscription/edit.html.twig', array(
            'inscription' => $inscription,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a inscription entity.
     *
     * @Route("/remove/{id}", name="inscription_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
      
        $em = $this->getDoctrine()->getManager();
        $inscription = $em->getRepository('AppBundle:Inscription')->findOneBy(array('id' => $id));
        dump($inscription);
      
        if(!empty($inscription)){
            
            $em->remove($inscription);
            $em->flush();
        }

         return $this->redirectToRoute('inscription_index');
    }

    /**
     * Creates a form to delete a inscription entity.
     *
     * @param Inscription $inscription The inscription entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Inscription $inscription)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inscription_delete', array('id' => $inscription->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
