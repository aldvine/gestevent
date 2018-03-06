<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/**
 * @Route("/{_locale}/Inscription")
 */
class InscriptionController extends Controller
{
    /**
     * @Route("/", name="listInscription")
     */
    public function listAction(Request $request)
    {
        //recuperer toutes les inscriptions et les afficher 
        // replace this example code with whatever you need

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
