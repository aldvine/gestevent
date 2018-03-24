<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Item;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/")
 * 
 */

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->getBreadcrumbs();
    
        
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }
    
    public function getBreadcrumbs(){
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        // Simple example
        $breadcrumbs->addRouteItem($this->get('translator')->trans('home'), "homepage");
        return $breadcrumbs;
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


        $referer = $request->headers->get('referer');
        // si route event_filter ou autre 
        if( strpos($referer, "/event/")){
            return $this->redirectToRoute('event_index');
        }else{
            return $this->redirect($referer);   
        }
    }


    /**
     * @Route("/rss", name="getFluxRss")
     */
    public function rssAction(Request $request)
    {

        $domaine = 'http://gestevent.fr/';
        $em = $this->getDoctrine()->getManager();
       
        // recuperation d'uniquement ceux à venir
        $events = $this->getDoctrine()
            ->getRepository('AppBundle:Event')
            ->findAllGreaterThanDate(date('Y-m-d H:i:s'));

        $locale =  $request->getLocale();

        $feed = new Feed();

        $channel = new Channel();
        $channel
            ->title('Evenements')
            ->description('Liste des évènements à venir')
            ->url('http://gestevent.fr')
            ->feedUrl('http://gestevent.fr/rss')
            ->copyright('Copyright 2018, estevent')
            ->pubDate(strtotime(date('Y-m-d H:i:s')))
            ->lastBuildDate(strtotime(date('Y-m-d H:i:s')))
            ->appendTo($feed);

        foreach ($events as $event) {
            $autheur = $event->getUser();
            $item = new Item();
            $item
                ->title($event->getTitle())
                ->description('<div>'.$event->getDescription().'</div>')
                ->url($domaine.$locale.'/event/'.$event->getId())
                ->pubDate($event->getDate()->getTimeStamp())
                ->author($autheur->getLastname().' '.$autheur->getFirstname())
                ->appendTo($channel);
        }
        $response = new Response($feed);
        $response->headers->set('Content-Type', 'xml');
        // echo $feed; // or echo $feed->render();
        // dump($events);
        return $response;
    }
}
