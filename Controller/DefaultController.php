<?php

namespace Extrablind\MonitHomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $env   = $this->container->getParameter('kernel.environment');
        $wsUrl = $this->container->getParameter('monithome.ws.url');

        return $this->render('@ExtrablindMonitHome/Default/index.html.twig',
    [
        'wsUrl' => $wsUrl,
    ]
  );
    }
}
