<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Services\MySensors;

class LogTransformer
{
    private function getRandomColor()
    {
        $r = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $g = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $b = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);

        return '#'.$r.$g.$b;
    }

    protected $step = false;
    protected $fill = false;

    public function setStep($value)
    {
        $this->step = $value;

        return $this;
    }

    public function setFill($value)
    {
        $this->fill = $value;

        return $this;
    }

    public function transform($logs)
    {
        $datasets = [];
        $colors = [];
        foreach ($logs as $k => $log) {
            // Assign color
            if (!isset($colors[$log['id']])) {
                $colors[$log['id']] = $this->getRandomColor();
            }
            // Init
            $sensorId = $log['id'];
            $name = $log['title'].' - '.$log['place'].' - '.$log['sensorValueType'];
            $date = $log['created']->format('Y-m-d H:i:s');
            $uniqId = uniqid();
            // Values
            $datasets[$sensorId]['data'][$uniqId]['y'] = $log['value'];
            $datasets[$sensorId]['data'][$uniqId]['x'] = $date;
            $datasets[$sensorId]['data'] = array_values($datasets[$sensorId]['data']);
            // Infos on distribution
            $datasets[$sensorId]['label'] = $name;
            $datasets[$sensorId]['id'] = $sensorId;
            // Graphic UI
            $datasets[$sensorId]['backgroundColor'] = $colors[$sensorId];
            $datasets[$sensorId]['fill'] = $this->fill;
            $datasets[$sensorId]['spanGaps'] = true;
            $datasets[$sensorId]['borderColor'] = $colors[$sensorId];

            if ($this->step) {
                $datasets[$sensorId]['steppedLine'] = true;
            }
        }
        $datasets = array_values($datasets);
        $return = ['datasets' => $datasets];

        return $return;
    }

    /*
    public function chartify($logs)
    {

      foreach ($logs as $k => $log) {
        $colors[$log['id']] = $this->getRandomColor();
      }
      $datasets = [];
      foreach ($logs as $k => $log) {
        $id = $log['id'];
        $name = $log['title'].' - '.$log['place'].' - '. $log['sensorValueType'];
        $date = $log['created']->format('Y-m-d H:i:s');
        $uniqId = uniqid();
        // Values
        $datasets[$id][0]['data'][$uniqId]['y'] = $log['value'];
        $datasets[$id][0]['data'][$uniqId]['x'] = $date;
        $datasets[$id][0]['data'] = array_values($datasets[$id][0]['data']);
        // Infos
        $datasets[$id][0]['label'] = $name;
        $datasets[$id][0]['id'] = $id;
        // UI
        $datasets[$id][0]['backgroundColor'] = $colors[$id];
        $datasets[$id][0]['fill'] = true;
        $datasets[$id][0]['spanGaps'] = true;
        $datasets[$id]['borderColor'] = $colors[$id];

        if ('V_STATUS' === $log['sensorValueType']) {
          $datasets[$id][0]['steppedLine'] = true;
        }
      }
      $sensors = $datasets;
      $datasets = [];
      foreach ($logs as $k => $log) {
        $id = $log['id'];
        $name = $log['title'].' - '.$log['place'].' - '.$log['sensorValueType'];
        $date = $log['created']->format('Y-m-d H:i:s');
        // One dataset by sensor
        $datasets[$id]['label'] = $name;
        $datasets[$id]['id'] = $id;
        $datasets[$id]['backgroundColor'] = $colors[$id];
        $uniqId = uniqid();
        $datasets[$id]['data'][$uniqId]['y'] = $log['value'];
        $datasets[$id]['data'][$uniqId]['x'] = $date;
        $datasets[$id]['fill'] = false;
        $datasets[$id]['borderColor'] = $colors[$id];
        $datasets[$id]['spanGaps'] = true;

        $datasets[$id]['data'] = array_values($datasets[$id]['data']);
        if ('V_STATUS' === $log['sensorValueType']) {
          $datasets[$id]['steppedLine'] = true;
        }
      }
      $datasets = array_values($datasets);
      //  $labels   =     array_values($labels);
      $full = [
        'datasets' => $datasets,
      ];

      return [
        'full' => $full,
        'sensors' => $sensors,
      ];
    }
    */
}
