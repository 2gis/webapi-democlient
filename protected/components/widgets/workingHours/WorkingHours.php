<?php

class WorkingHours extends CWidget
{
    protected $days = array('Mon' => 'пн', 'Tue' => 'вт', 'Wed' => 'ср', 'Thu' => 'чт', 'Fri' => 'пт', 'Sat' => 'сб', 'Sun' => 'вс');
    public $params;
    
    private function isRoundTheClock($filial_schedule)
    {
        $part1 = 'working_hours-0';
        
        foreach ($this->days as $day => $dayRus) {
            if (!isset($filial_schedule->$day)) {
                return false;
            }
        }
        
        foreach($filial_schedule as $day => $whs) {
            if (!isset($this->days[$day])) {
                continue;
            }
            if (count((array)$whs) > 1) {
                return false;
            }
            if ($whs->$part1->from != '00:00' || $whs->$part1->to != '24:00') {
                return false;
            }
        }
        
        return true;
    }

    
    private function formatSchedule($filial_schedule)
    {
        $whData = array();
        $part1 = 'working_hours-0';
        $part2 = 'working_hours-1';
        foreach($filial_schedule as $day => $whs) {
            if (!isset($this->days[$day])) {
                continue;
            }
            if (count((array)$whs) == 1) {
                $whData[$day] = $whs->$part1->from . '&ndash;' . $whs->$part1->to;
            } else {
                $whData[$day] = $whs->$part1->from . '&ndash;' . $whs->$part2->to . ', обед ' . $whs->$part1->to . '&ndash;' . $whs->$part2->from;
            }
        }
        
        return $whData;
    }

    public function init()
    {
        $assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.components.widgets.workingHours.assets'));
        $cs = Yii::app()->clientScript;
        $cs->registerCssFile($assetsUrl . '/css/worktime.css');
        parent::init();
    }
    
    public function run()
    {
        $filial_schedule = $this->params['filial_schedule'];

        $currentDay = date('D');
        $roundTheClock = $this->isRoundTheClock($filial_schedule);
        $whData = array();
        $today = '';
        if (!$roundTheClock) {
            $whData = $this->formatSchedule($filial_schedule);
            $today = 'Сегодня ' . (isset($whData[$currentDay]) ? $whData[$currentDay] : 'выходной');
        }
        
        $this->render('workingHours', array(
                'today' => $today,
                'roundTheClock' => $roundTheClock,
                'comment' => isset($filial_schedule->comment) ? $filial_schedule->comment : '',
                'days' => $this->days,
                'whData' => $whData,
                'opened' => isset($whData[$currentDay])
             ));
    }
}