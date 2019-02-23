<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Command;

use Extrablind\MonitHomeBundle\Services\MySensors\Gateways\GatewayInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends ContainerAwareCommand
{
    private $entityManager;
    private $container;

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
        parent::__construct();
    }

    protected function configure()
    {
        $this
    ->setName('mysensors:send')
    ->setHelp($this->getCommandHelp())
    ->setDescription('Send a raw command to MySensors gateway')
    ->addArgument('c', InputArgument::OPTIONAL, "1;1;1;0;0;0;\n")
    ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->message = $this->getContainer()->get('monithome_mysensors_message');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $title = 'Send Command';
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
        $output = $this->getOutputStyles($output);

        $m = (string) $input->getArgument('c')."\n";
        $message = $this->message->parse($m);

        $this->gateway->start();
        $this->gateway->send($message);
        $output->writeln('Sended : '.$m);
        $this->gateway->stop();
    }

    private function getCommandHelp()
    {
        return 'This command run forever and treate entries from controller';
    }
}
