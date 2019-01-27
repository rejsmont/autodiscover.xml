<?php

namespace App\Controller;

use App\Provider\DomainProvider;
use App\Provider\UsernameProvider;
use App\Email\EmailFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AutoDiscoverController
 * @package App\Controller
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AutoDiscoverController extends AbstractController
{
    /**
     * @param Request $request
     * @param DomainProvider $domainProvider
     * @param UsernameProvider $usernameProvider
     * @param EmailFactory $emailFactory
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/mail/config-v1.1.xml", name="mozilla")
     */
    public function mozilla(Request $request, DomainProvider $domainProvider,
                            UsernameProvider $usernameProvider, EmailFactory $emailFactory)
    {
        $email = $emailFactory->fromString($request->query->get('emailaddress'));
        $username = null;

        if ((null !== $email)&&($domainProvider->verifyDomain($email->getDomainPart()))) {
            $username = $usernameProvider->getUsername($email);
        }

        return $this->render('template.html.twig', [
            'email' => $email,
            'username' => $username
        ]);
    }
}
