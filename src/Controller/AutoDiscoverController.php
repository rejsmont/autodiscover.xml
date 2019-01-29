<?php
/*
 * This file is part of the Autodiscover.xml
 *
 * Copyright (c) 2019 Radosław Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AutodiscoverXml\Controller;

use AutodiscoverXml\Email\Email;
use AutodiscoverXml\Provider\DomainProvider;
use AutodiscoverXml\Provider\ServiceProvider;
use AutodiscoverXml\Email\EmailFactory;
use AutodiscoverXml\User\UserFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
    private $logger;


    /**
     * AutoDiscoverController constructor.
     *
     * @param DomainProvider $domainProvider
     * @param UserFactory $userFactory
     * @param EmailFactory $emailFactory
     * @param ServiceProvider $serviceProvider
     * @param LoggerInterface $logger
     */
    public function __construct(DomainProvider $domainProvider, UserFactory $userFactory,
                                EmailFactory $emailFactory, ServiceProvider $serviceProvider,
                                LoggerInterface $logger)
    {
        $this->domainProvider = $domainProvider;
        $this->userFactory = $userFactory;
        $this->emailFactory = $emailFactory;
        $this->serviceProvider = $serviceProvider;
        $this->logger = $logger;
    }

    /**
     * This route handles Mozilla autoconfig requests
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/mail/config-v1.1.xml", name="mozilla", methods={"GET"})
     */
    public function mozilla(Request $request)
    {
        $email = $this->emailFactory->fromString($request->query->get('emailaddress'));
        $this->logger->info("Got a Mozilla request for email: " . $email);

        $response = $this->render('mozilla.xml.twig', $this->fetchData($email));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    /**
     * This route handles Microsoft Autodiscover protocol for Outlook and ActiveSync
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/autodiscover/autodiscover.xml", name="microsoft", methods={"POST"})
     */
    public function microsoft(Request $request)
    {
        $data = $request->getContent();
        $httpUser = $request->getUser();
        $crawler = new Crawler($data);

        // Find out if this is an Outlook or an ActiveSync request
        try {
            $acceptableResponse = $crawler->filter('Request > AcceptableResponseSchema')->text();
        } catch (\InvalidArgumentException $e) {
            $acceptableResponse = $crawler->children()->filter('default|Request > default|AcceptableResponseSchema')->text();
        }
        if (strpos(strtolower($acceptableResponse), 'outlook') !== false) {
            $schema = 'Outlook';
        } elseif (strpos(strtolower($acceptableResponse), 'mobilesync') !== false) {
            $schema = 'ActiveSync';
        } else {
            // Neither Outlook or ActiveSync - we do not support that
            throw new BadRequestHttpException();
        }

        // Get the requested email address
        try {
            $string = $crawler->filter('Request > EMailAddress')->text();
        } catch (\InvalidArgumentException $e) {
            $string = $crawler->children()->filter('default|Request > default|EMailAddress')->text();
        }

        // Perform user data lookup
        $email = $this->emailFactory->fromString($string);
        $this->logger->info("Got a Microsoft " . $schema . " request for email: " . $email);
        $data = $this->fetchData($email);
        $user = $data['user']->getUserName();

        // Which response to provide?
        switch($schema) {
            // Outlook autodiscovery
            case 'Outlook':
                $response = $this->render('microsoft.xml.twig', $data);
                $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
                break;
            // ActiveSync autodiscovery
            case 'ActiveSync':
                // If ActiveSync is not configured, return 404
                if (null === $data['activesync']) {
                    throw new NotFoundHttpException();
                }
                // If client passed authentication information, but it does not match username, return 401
                if ((null != $httpUser)&&($httpUser != $user)) {
                    throw new UnauthorizedHttpException('ActiveSync');
                }
                // Return ActiveSync response
                if (($email == $user) || ($httpUser == $user)) {
                    $response = $this->render('activesync.xml.twig', $data);
                    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
                } else {
                    $response = $this->render('activesync-redirect.xml.twig', $data);
                    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
                }
                break;
            default:
                // Something weird happened, return 400
                throw new BadRequestHttpException();
        }

        return $response;
    }

    /**
     * This route handles Apple Mobile Config Profile requests
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/email.mobileconfig", name="apple", methods={"GET"})
     */
    public function apple(Request $request)
    {
        $email = $this->emailFactory->fromString($request->query->get('email'));
        $this->logger->info("Got a Apple request for email: " . $email);

        $response = $this->render('apple.xml.twig', $this->fetchData($email));
        $response->headers->set('Content-Type', 'application/x-apple-aspen-config; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="${filename}"');

        return $response;
    }

    /**
     * Fetch data from ServiceProvider
     *
     * @param Email $email
     * @return array
     */
    private function fetchData($email)
    {
        // Verify that user domain is served by the mail server and populate user object
        if ((null !== $email)&&($this->domainProvider->verifyDomain($email->getDomainPart()))) {
            $user = $this->userFactory->fromString($email);
        } else {
            throw new NotFoundHttpException();
        }

        // Fetch the service data
        $provider = $this->serviceProvider->getProvider();
        $imaps = $this->serviceProvider->getImap();
        $pop3s = $this->serviceProvider->getPop3();
        $smtps = $this->serviceProvider->getSmtp();
        $activesync = $this->serviceProvider->getActiveSync();

        return [
            'user' => $user,
            'provider' => $provider,
            'imaps' => $imaps,
            'smtps' => $smtps,
            'pop3s' => $pop3s,
            'activesync' => $activesync
        ];
    }
}
