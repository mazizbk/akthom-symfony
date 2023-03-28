<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguageController extends AbstractController
{
    /**
     * @Route("/language/{lang}", name="language_switcher")
     */
    public function switchLanguage($lang, Request $request)
    {

        // Store the selected language in the session
        $request->getSession()->set('_locale', $lang);

        $refer  = $request->headers->get('referer');
        if (\str_contains($refer, 'search')) {
            $refer .= '?search_string=';
        }
        // Redirect the user back to the page they were on before
        return $this->redirect($refer);
    }
}
