<?php

namespace Extrablind\MonitHomeBundle\Command;

use Extrablind\MonitHomeBundle\Entity\Node;
use Extrablind\MonitHomeBundle\Entity\Sensor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class UpdateTopologyCommand extends ContainerAwareCommand
{
    private $entityManager;
    private $container;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
    ->setName('mysensors:update')
    ->setHelp($this->getCommandHelp())
    ->setDescription('Send a raw command to MySensors gateway')
    ->addArgument('path', InputArgument::REQUIRED, '')
    ->addOption('truncate', 't', InputOption::VALUE_OPTIONAL, 'Remove previously added nodes, sensors and logs', 0);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->em      = $this->getContainer()->get('doctrine')->getManager();
        $this->message = $this->getContainer()->get('monithome_mysensors_message');
        $this->em      = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $title      = 'Update node and sensors topology network based on yml definition file';
        $boundaries = str_repeat('=', \strlen($title));
        $output->writeln($boundaries);
        $output->writeln($title);
        $output->writeln($boundaries);
    }

    public function getOutputStyles($output)
    {
        $outputStyle = new OutputFormatterStyle('green', 'black', ['bold']);
        $output->getFormatter()->setStyle('title', $outputStyle);

        return $output;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file     = realpath($input->getArgument('path'));
        $truncate = $input->getOption('truncate');
        if (!$file) {
            throw new \Exception('File not found !');
        }

        if (null === $truncate) {
            $output->writeln('<info>Truncate</info>');
            $output->writeln('> Truncate log table');
            $RAW_QUERY = 'DELETE FROM monithome_logs;';
            $statement = $this->em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();

            $output->writeln('> Truncate node table');
            $RAW_QUERY = 'DELETE FROM monithome_nodes;';
            $statement = $this->em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();

            $output->writeln('> Truncate sensor table');
            $RAW_QUERY = 'DELETE FROM monithome_sensors;';
            $statement = $this->em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
            $this->em->flush();
        }

        $nodes = Yaml::parse(file_get_contents($file))['nodes'];
        // Read yaml
        foreach ($nodes as $k => $node) {
            $n = $this->em->getRepository(Node::class)->findOneBy(['nodeId' => $node['id']]);

            // Update or create
            $saveNode       = (!$n) ? new Node() : $n;
            $createOrUpdate = (!$n) ? 'Create' : 'Update';
            $output->writeln("<info>$createOrUpdate node : {$node['name']} - {$node['place']}</info>");

            $saveNode
      ->setNodeType('S_ARDUINO_NODE')
      ->setNodeSketchVersion('1.0')
      ->setNodeName($node['name'])
      ->setPlace($node['place'])
      ->setCreated(new \DateTime())
      ->setDescription($node['description'])
      ->setNodeId($node['id'])
      ;
            $this->em->persist($saveNode);
            $this->em->flush();

            foreach ($node['sensors'] as $k => $sensor) {
                // Find sensor with
                $identifier = "{$node['id']}-{$sensor['id']}";
                $s          = $this->em->getRepository(Sensor::class)->findOneBy(['sensorUniqueIdentifier' => $identifier]);

                // Update or create
                $saveSensor     = (!$s) ? new Sensor() : $s;
                $createOrUpdate = (!$s) ? 'Create' : 'Update';
                $output->writeln("$createOrUpdate sensor : {$sensor['title']}");

                $saveSensor
        ->setNode($saveNode)
        ->setTitle($sensor['title'])
        ->setDescription($sensor['description'])
        ->setValue(null)
        ->setUnit($sensor['configuration']['unit'])
        ->setUpdated(new \DateTime())
        ->setCreated(new \DateTime())
        ->setConfiguration($sensor['configuration'])
        ->setSensorId($sensor['id'])
        ->setSensorUniqueIdentifier("{$saveNode->getNodeId()}-{$saveSensor->getSensorId()}")
        ->setSensorType($sensor['type'])
        ->setSensorValueType($sensor['valueType'])
        ;
                $this->em->persist($saveSensor);
                $this->em->flush();
            }
        }
        $output->writeln('DONE');
        exit();
    }

    private function getCommandHelp()
    {
        return 'Will update nodes and sensors in db from specified yml file description';
    }
}
