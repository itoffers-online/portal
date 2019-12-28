<?php


namespace App\Offers\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    public function userBlockedAction(Request $request) : Response
    {
        return $this->render('@offers/security/user_blocked.html.twig', []);
    }
}