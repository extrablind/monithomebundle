<?php

namespace Extrablind\MonitHomeBundle\Command;

use Extrablind\MonitHomeBundle\Entity\Event;
use Extrablind\MonitHomeBundle\Events\NewMessage\NewMessageEvent;
use Extrablind\MonitHomeBundle\Events\ScheduleTrigger\ScheduleTriggerEvent;
use Extrablind\MonitHomeBundle\Services\MySensors\Gateways\GatewayInterface;
use Extrablind\MonitHomeBundle\Utilities\SharedMemory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DaemonStartCommand extends ContainerAwareCommand
{
    private $entityManager;
    private $container;
    private $stop = false;

    public function __construct(GatewayInterface $gateway, $pusher, $doctrine)
    {
        $this->gateway = $gateway;
        $this->pusher  = $pusher;
        $this->em      = $doctrine->getManager();
        parent::__construct();
    }

    protected function configure()
    {
        $this
    ->setName('mysensors:daemon:start')
    ->setHelp($this->getCommandHelp())
    ->setDescription('Start gateway daemon reader and parser.')
    ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->message = $this->getContainer()->get('monithome_mysensors_message');
        $this->em      = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $title      = 'Daemon Start';
        $boundaries = str_repeat('=', \strlen($title));
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
        $this->output = $output;
        pcntl_signal(SIGINT, [$this, 'doInterrupt']);
        pcntl_signal(SIGTERM, [$this, 'doTerminate']);

        $output = $this->getOutputStyles($output);

        $this->gateway->start();
        //$sharedMem = new SharedMemory();
        $lastUpdate = time();

        while (true) {
            $now      = new \DateTime();
            $format   = $now->format('Y-m-d H:i:s');
            $triggers = $this->em->getRepository(Event::class)
      ->getByDate($format)->getResult();

            if (!empty($triggers)) {
                $this->getContainer()->get('event_dispatcher')
        ->dispatch(ScheduleTriggerEvent::NAME, new ScheduleTriggerEvent($triggers));
            }

            // Push gateway is alive message every 5 seconds
            $differenceInSeconds = time() - $lastUpdate;
            if ($differenceInSeconds > 2) {
                $lastUpdate = time();
                $this->pusher->push(['msg' => ['type' => 'push', 'action' => 'updateGatewayStatus', 'data' => $lastUpdate]], 'monithome_push_topic');
            }

            pcntl_signal_dispatch();
            if ($this->stop) {
                break;
            }
            $this->gateway->read();
            if (!$this->gateway->hasMessage()) {
                continue;
            }

            $gwMessage = $this->gateway->getMessage();
            $message   = $this->message->parse($gwMessage);
            $output->writeln($gwMessage);

            // Parsed gateway message is invalid
            if (!$message) {
                continue;
            }
            // Events here
            $event = new NewMessageEvent($message, $output);
            $this->getContainer()->get('event_dispatcher')->dispatch(NewMessageEvent::NAME, $event);

            // Be light, we are in infinite loop
            unset($gwMessage, $message, $event);
        }
        $this->gateway->stop();
    }

    /**
     * Ctrl-C.
     */
    private function doInterrupt()
    {
        $this->output->writeln('<error>Interruption (Ctrl+c) received, closing gateway</error>');
        $this->stop = true;
        $this->gateway->stop();
        exit;
    }

    /**
     * kill PID.
     */
    private function doTerminate()
    {
        $this->output->writeln('<error>Termination signal received, closing gateway</error>');
        $this->stop = true;
        $this->gateway->stop();
        exit;
    }

    private function getCommandHelp()
    {
        return 'This command run forever and treate entries from controller to save new node, save sensor values, log values.';
    }
}
