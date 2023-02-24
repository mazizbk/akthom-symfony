<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/auth/logout", name="logout")
     */
    public function logout(): RedirectResponse
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function adminLogout(LogoutUrlGenerator $logoutUrlGenerator): RedirectResponse
    {
        return new RedirectResponse($logoutUrlGenerator->getLogoutUrl('admin_area'));
    }
}
