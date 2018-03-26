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
     * Liste de toutes les inscriptions
     *
     * @Route("/", name="inscription_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        // liste uniquement de ses propres inscriptions
        $inscriptions = $em->getRepository('AppBundle:Inscription')->findBy(array('user' => $this->getUser()));
        
        $events = new ArrayCollection();
        // fil ariane
        $breadcrumbs = $this->getBreadcrumbs();
        // pour toutes les inscriptions recuperation des evenement correspondants.
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
    // fil ariane du controleur
    public function getBreadcrumbs(){
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        // Simple example
        $breadcrumbs->addRouteItem($this->get('translator')->trans('inscription.list'), "inscription_index");
        return $breadcrumbs;
    }
    /**
     * 
     * Creates a new inscription entity.
     *
     * @Route("/new/{id}", name="inscription_new")
     * @Method({"POST"})
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // recuperation de l'evenement
        $event = $em->getRepository('AppBundle:Event')->findOneBy(array('id' => $id));
        // verification d'une eventuelle isncription identique deja presente
        $resp = $em->getRepository('AppBundle:Inscription')->findOneBy(array('user' => $this->getUser(),'event'=>$event));
        // compteur du nombre d'inscriptions
        $inscriptions = $em->getRepository('AppBundle:Inscription')->findBy(array('event' => $id));
        $nbindividu = count($inscriptions);
        // veriifcation que l'inscription n'est pas déjà faite , si ok enregistrement
        if(empty($resp) && $nbindividu<$event->getNbPlace()){
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
        // creation formulaire de désinscription
        $deleteForm = $this->createDeleteForm($inscription);

        $em = $this->getDoctrine()->getManager();
        // recuperation de l'evenement de l'inscription
        $event = $em->getRepository('AppBundle:Event')->findOneBy(array('id' => $inscription->getEvent()->getId()));
        //recuperation de toutes les inscriptions
        $inscriptions = $em->getRepository('AppBundle:Inscription')->findBy(array('event' => $event->getId()));
        $users = new ArrayCollection();
        // pour toutes ces inscriptions on ajoutes les utilisateurs dans un tableau pour 
        // qui corresponds aux différents participants
        foreach($inscriptions as $inscription){
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $inscription->getUser()->getId()));
            $users[] = $user;
        }
        // fil ariane
        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addRouteItem($this->get('translator')->trans('inscription').' '.$inscription->getId(), "inscription_show",[
            'id' => $inscription->getId(),
        ]);
        // rendu vue
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
        // fonction non utilisé mais généré par la commande CRUD
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
      // désinscription
        $em = $this->getDoctrine()->getManager();
        $inscription = $em->getRepository('AppBundle:Inscription')->findOneBy(array('id' => $id));
       
        // si inscription existe on la supprime
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
