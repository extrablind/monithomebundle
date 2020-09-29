<?php

namespace Extrablind\MonitHomeBundle\Controller\API;

use Extrablind\MonitHomeBundle\Entity\Log;
use Extrablind\MonitHomeBundle\Entity\Node;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class NodesController extends FOSRestController
{
    public function pushAction(Request $request)
    {
        $session = $this->container->get('session');
        $from    = $session->get('from');
        $to      = $session->get('to');

        $logs = $this->get('doctrine')->getRepository(Log::class)->getLogsBetween($from, $to);

        return new JsonResponse([
            'sensors' => $sensors,
        ]);
    }

    /**
     * @Get("/nodes")
     */
    public function getNodesAction()
    {
        $nodes = $this->get('doctrine')
    ->getRepository(Node::class)
    ->createQueryBuilder('node')
    ->select(['node', 'sensor'])
    ->join('node.sensors', 'sensor')
    ->getQuery()
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;
        $view = $this->view($nodes, 200)
    ->setTemplate('ExtrablindMonitHomeBundle:API:Nodes:get.html.twig')
    ->setTemplateVar('users')
    ;

        header('Access-Control-Allow-Origin: *');

        return $this->handleView($view);
    }
}
