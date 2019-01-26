<?php

namespace App\Controller;

use App\Provider\DomainProvider;
use App\Provider\UsernameProvider;
use App\Tokenizer\EmailTokenizer;
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
     * @param EmailTokenizer $emailTokenizer
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/mail/config-v1.1.xml", name="mozilla")
     */
    public function mozilla(Request $request, DomainProvider $domainProvider,
                            UsernameProvider $usernameProvider, EmailTokenizer $emailTokenizer)
    {
        $email = $request->query->get('emailaddress');
        $domain = $emailTokenizer->getDomainPart($email);
        $username = null;

        if ((null !== $domain)&&( $domainProvider->verifyDomain($domain))) {
            $username = $usernameProvider->getUsername($email);
        }

        return $this->render('template.html.twig', [
            'email' => $email,
            'username' => $username
        ]);
    }
}
