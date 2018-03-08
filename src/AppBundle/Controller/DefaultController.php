<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/**
 * @Route("/")
 */

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
       
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * Change the locale for the current user
     *
     * @Route("/locale/{locale}", name="setlocale")
     * 
     */
    public function setLocaleAction(Request $request, $locale = "null")
    {
        if ($locale != null) {
           // On enregistre la langue en session
            $request->getSession()->set('_locale', $locale);
        }

        return $this->redirectToRoute('homepage');
    }
    /**
     * Change the locale for the current user
     *
     * @Route("/theme/{style}", name="setTheme")
     * 
     */
    public function setThemeAction(Request $request, $style = "null")
    {
       
        if ($style != null) {
           // On enregistre la langue en session

           
            $request->getSession()->set('style', 'css/' . $style . '.css');
            // dump($request->getSession());
        }

        return $this->redirectToRoute('homepage');
       
    }

}
