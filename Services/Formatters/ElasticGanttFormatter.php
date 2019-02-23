<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Extrablind\MonitHomeBundle\Services\Formatters;

class ElasticGanttFormatter
{
    private function getRandomColor()
    {
        $r = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $g = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $b = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);

        return '#'.$r.$g.$b;
    }

    public function transform($logs, $id)
    {
        $datas = [];
        $colors = [];
        foreach ($logs as $k => $log) {
            // Assign color
            if (!isset($colors[$log['id']])) {
                $colors[$log['id']] = $this->getRandomColor();
            }
            // Values
            $datas[$k]['id'] = $id;
            $datas[$k]['start'] = $log['created']->getTimestamp();
            $datas[$k]['value'] = $log['value'];
            $datas[$k]['progress'] = 100;
            $datas[$k]['type'] = 'task';
            // Search next different value to get duration of this task
            $l = \array_slice($logs, $k, \count($logs) - 1);
            foreach ($l as $next) {
                if ($next['value'] === $log['value']) {
                    unset($logs[$k]);
                    continue;
                }
                $diffSecs = $next['created']->getTimestamp() - $log['created']->getTimestamp();
                $datas[$k]['duration'] = $diffSecs;
                break;
            }
        }

        return array_values($datas);
    }
}
