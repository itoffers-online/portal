<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    public function homeAction(Request $request) : Response
    {
        return $this->render('/home/index.html.twig', [
            'specializations' => $this->get(System::class)->query(SpecializationQuery::class)->all(),
        ]);
    }

    public function faqAction(Request $request) : Response
    {
        return $this->render('/home/faq.html.twig', []);
    }
}
