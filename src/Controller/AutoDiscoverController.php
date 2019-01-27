<?php

namespace AutodiscoverXml\Controller;

use AutodiscoverXml\Email\Email;
use AutodiscoverXml\Provider\DomainProvider;
use AutodiscoverXml\Provider\ServiceProvider;
use AutodiscoverXml\Email\EmailFactory;
use AutodiscoverXml\User\UserFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AutoDiscoverController
 * @package AutodiscoverXml\Controller
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AutoDiscoverController extends AbstractController
{
    private $domainProvider;
    private $userFactory;
    private $emailFactory;
    private $serviceProvider;


    /**
     * AutoDiscoverController constructor.
     *
     * @param DomainProvider $domainProvider
     * @param UserFactory $userFactory
     * @param EmailFactory $emailFactory
     * @param ServiceProvider $serviceProvider
     */
    public function __construct(DomainProvider $domainProvider, UserFactory $userFactory,
                                EmailFactory $emailFactory, ServiceProvider $serviceProvider)
    {
        $this->domainProvider = $domainProvider;
        $this->userFactory = $userFactory;
        $this->emailFactory = $emailFactory;
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/mail/config-v1.1.xml", name="mozilla", methods={"GET"})
     */
    public function mozilla(Request $request)
    {
        $email = $this->emailFactory->fromString($request->query->get('emailaddress'));

        $response = $this->render('mozilla.xml.twig', $this->fetchData($email));
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/autodiscover/autodiscover.xml", name="microsoft", methods={"POST"})
     */
    public function microsoft(Request $request)
    {
        $crawler = new Crawler($request->getContent());
        $string = $crawler->filterXPath('//Autodiscover/Request/EMailAddress')->text();
        $email = $this->emailFactory->fromString($string);

        $response = $this->render('microsoft.xml.twig', $this->fetchData($email));
        $response->headers->set('Content-Type', 'xml; chatset=utf-8');

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/email.mobileconfig", name="apple", methods={"GET"})
     */
    public function apple(Request $request)
    {
        $email = $this->emailFactory->fromString($request->query->get('email'));

        $response = $this->render('apple.xml.twig', $this->fetchData($email));
        $response->headers->set('Content-Type', 'application/x-apple-aspen-config; chatset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="${filename}"');

        return $response;
    }

    /**
     * @param Email $email
     * @return array
     */
    private function fetchData($email)
    {
        if ((null !== $email)&&($this->domainProvider->verifyDomain($email->getDomainPart()))) {
            $user = $this->userFactory->fromString($email);
        } else {
            throw new NotFoundHttpException();
        }

        $provider = $this->serviceProvider->getProvider();
        $imaps = $this->serviceProvider->getImap();
        $pop3s = $this->serviceProvider->getPop3();
        $smtps = $this->serviceProvider->getSmtp();

        return [
            'user' => $user,
            'provider' => $provider,
            'imaps' => $imaps,
            'smtps' => $smtps,
            'pop3s' => $pop3s
        ];
    }
}
