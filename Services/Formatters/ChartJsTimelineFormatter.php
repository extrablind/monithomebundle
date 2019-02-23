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

class ChartJsTimelineFormatter
{
    private function getRandomColor()
    {
        $r = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $g = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $b = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);

        return '#'.$r.$g.$b;
    }

    public function format($logs)
    {
        $datas = [];
        $colors = [];
        //  { id: '1', resourceId: 'id', start: '2018-02-07T02:00:00', end: '2018-02-07T07:00:00', title: 'event 1' },
        foreach ($logs as $k => $log) {
            // Assign color
            if (!isset($colors[$log['id']])) {
                $colors[$log['id']] = $this->getRandomColor();
            }
            // Search next different value to get duration of this task
            $next = next($logs);
            // End of array
            $status = (1 === $log['value']) ? 'On' : 'Off';

            // End save last value as ended now
            if (!$next) {
                $now = new \DateTime();
                $datas[$k]['start'] = $log['created']->format('Y-m-d H:i:s');
                $datas[$k]['color'] = ('On' === $status) ? 'green' : 'red';
                $datas[$k]['title'] = "{$log['title']} ($status)";
                $datas[$k]['end'] = $now->format('Y-m-d H:i:s');
                break;
            }
            // Duplicate value, consider only one, ignore others
            if ($next['value'] === $logs[$k]['value'] && !$next) {
                continue;
            }
            $datas[$k]['start'] = $log['created']->format('Y-m-d H:i:s');
            $datas[$k]['color'] = ('On' === $status) ? 'green' : 'red';
            $datas[$k]['title'] = "{$log['title']} ($status)";
            $datas[$k]['end'] = $next['created']->format('Y-m-d H:i:s');
        }

        return array_values($datas);
    }
}
