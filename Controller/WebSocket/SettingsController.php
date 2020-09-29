<?php

namespace Extrablind\MonitHomeBundle\Controller\WebSocket;

use Extrablind\MonitHomeBundle\Entity\Setting;

class SettingsController
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function saveSettings($inputSetting)
    {
        $this->em = $this->container->get('doctrine')->getManager();
        $setting  = $this->em->getRepository(Setting::class)
    ->get()->getResult()[0];
        $setting
    ->setMetric($inputSetting['metric'])
    ->setAutoMode($inputSetting['autoMode'])
    ->setTimezone($inputSetting['timezone'])
    ;
        $this->em->persist($setting);
        $this->em->flush();

        return true;
    }

    public function getSettings()
    {
        $this->em = $this->container->get('doctrine')->getManager();
        $setting  = $this->em->getRepository(Setting::class)->get()
    ->getArrayResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
    ;
        // No settings found : insert with default values
        if (!$setting[0]) {
            $setting = new Setting();
            $this->em->persist($setting);
            $this->em->flush();

            return $setting;
        }

        return $setting[0];
    }
}
