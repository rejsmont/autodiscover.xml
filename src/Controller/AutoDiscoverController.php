<?php

namespace AutodiscoverXml\Controller;

use AutodiscoverXml\Email\Email;
use AutodiscoverXml\Provider\DomainProvider;
use AutodiscoverXml\Provider\ServiceProvider;
use AutodiscoverXml\Email\EmailFactory;
use AutodiscoverXml\User\UserFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param Request $request
     * @return Response
     *
     * @Route("/mail/config-v1.1.xml", name="mozilla", methods={"GET"})
     */
    public function mozilla(Request $request)
    {
        $email = $this->emailFactory->fromString($request->query->get('emailaddress'));

        $response = $this->render('mozilla.xml.twig', $this->fetchData($email));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

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
        $data = $request->getContent();
        $this->logger->info('Got post data:');
        $this->logger->info($data);
        $crawler = new Crawler($data);

        // Find out if this is an Outlook or an ActiveSync request
        try {
            $schema = $crawler->filter('Request > AcceptableResponseSchema')->text();
        } catch (\InvalidArgumentException $e) {
            $schema = $crawler->children()->filter('default|Request > default|AcceptableResponseSchema')->text();
        }

        // Get the requested email address
        try {
            $string = $crawler->filter('Request > EMailAddress')->text();
        } catch (\InvalidArgumentException $e) {
            $string = $crawler->children()->filter('default|Request > default|EMailAddress')->text();
        }
        $email = $this->emailFactory->fromString($string);
        $data = $this->fetchData($email);

        switch($schema) {
            case 'http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a':
                $response = $this->render('microsoft.xml.twig', $data);
                $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
                break;
            case 'http://schemas.microsoft.com/exchange/autodiscover/mobilesync/responseschema/2006':
                if ($email == $data['user']->getUserName()) {
                    $response = $this->render('activesync.xml.twig', $data);
                    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
                } else {
                    $response = $this->render('activesync-redirect.xml.twig', $data);
                    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
                }
                break;
        }

        $this->logger->info('Response data:');
        $this->logger->info($response->getContent());

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
        $response->headers->set('Content-Type', 'application/x-apple-aspen-config; charset=utf-8');
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
