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

class FullcalendarFormatter
{
    private function getRandomColor()
    {
        $r = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $g = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $b = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);

        return '#'.$r.$g.$b;
    }

    public function transform($logs)
    {
        $datas = [];
        $colors = [];
        //  { id: '1', resourceId: 'id', start: '2018-02-07T02:00:00', end: '2018-02-07T07:00:00', title: 'event 1' },
        foreach ($logs as $k => $log) {
            // Assign color
            if (!isset($colors[$log['id']])) {
                $colors[$log['id']] = $this->getRandomColor();
            }
            // Values
            $datas[$k]['id'] = uniqid();
            $datas[$k]['resourceId'] = $log['id'];
            $datas[$k]['start'] = $log['created']->format('Y-m-d H:i:s');
            // Search next different value to get duration of this task
            $l = \array_slice($logs, $k, \count($logs) - 1);
            foreach ($l as $next) {
                if ($next['value'] === $log['value']) {
                    unset($logs[$k]);
                    continue;
                }
                $datas[$k]['end'] = $next['created']->format('Y-m-d H:i:s');
                break;
            }
        }

        return array_values($datas);
    }
}
