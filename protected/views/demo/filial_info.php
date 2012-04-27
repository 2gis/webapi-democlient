<?php 
    $days = array('Mon' => 'Пн', 'Tue' => 'Вт', 'Wed' => 'Ср', 'Thu' => 'Чт', 'Fri' => 'Пт', 'Sat' => 'Сб', 'Sun' => 'Вс');
?>

<?php if (isset($filial->response_code) && $filial->response_code == 200):?>
    <script type="text/javascript" src="<?php echo Yii::app()->apiClient->apiUrl; ?>/assets/apitracker.js"></script>
    <script type="text/javascript">
        DG.apitracker.regBC('<?=$filial->register_bc_url?>');
    </script>

    <div class="firm-head">
        <h1><?php echo $filial->name?></h1>
        <?php if (isset($filial->rubrics)) : ?>
            <div class="rubric">
                <div style="position: relative; display: inline; zoom: 1;">
                    <?php
                        $cnt = count($filial->rubrics);
                        foreach ($filial->rubrics as $i => $rubric) {
                            echo (($i+1) != $cnt) ? $rubric.'; ' : $rubric;
                        }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div><!-- /firm-head -->
    
    <div class="firm-card clearfix">

        <div class="firm-content">
            <?php if(isset($filial->micro_comment) && strlen($filial->micro_comment) > 0) :?>
                <div class="keywords" style="position: relative;">
                    <?php echo strip_tags($filial->micro_comment)?>
                </div>
            <?php endif;?>
            <div class="advanced-info">
                <?php if (isset($filial->firm_group) && $filial->firm_group->count > 1) :?>
                    <div style="padding-bottom: 16px;">
                        <?php echo CHtml::link(
                            'Посмотреть все филиалы  [' . $filial->firm_group->count . ']',
                            Yii::app()->controller->createUrl('demo/filials', array('firm_id' => $filial->firm_group->id))
                        );?>
                    </div>
                <?php endif; ?>
                <?php if ($fullAddress = Helper::getFullAddress($filial)) :?>
                    <ul class="address-info">
                        <li><span class="address"><?php echo $fullAddress; ?></span></li>
                        <?php if(isset($filial->lon) && isset($filial->lat)) : ?>
                        <li>(Координаты: <?php echo sprintf('%.6f', $filial->lat) ?> <?php echo sprintf('%.6f', $filial->lon); ?>)</li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
                <?php if(isset($filial->link) && isset($filial->link->link)) :?>
                    <ul class="advert-info">
                        <li style="position: relative;">
                            <div class="advert-icon"></div> <?php echo CHtml::link(($filial->link->text ? strip_tags($filial->link->text) : $filial->link->link), $filial->link->link, array('target' => '_blank'))?>
                        </li>
                    </ul>
                <?php endif; ?>
                <?php if(isset($filial->contacts)):
                    $contactsGroups = array();
                    foreach($filial->contacts as $group):
                        $groupName = $group->name ? $group->name : 'default';
                        foreach ($group->contacts as $contact):
                            switch ($contact->type) {
                                case 'website':
                                    $contactsGroups[$groupName]['web'][] = array(
                                        'type'  => $contact->type,
                                        'value' => $contact->value,
                                        'alias' => isset($contact->alias) ? $contact->alias : '',
                                        'comment' => isset($contact->comment) ? $contact->comment : ''
                                    );
                                break;
                            
                                case 'phone':
                                case 'fax':
                                    $contactsGroups[$groupName]['phones'][] = array(
                                        'type'  => $contact->type,
                                        'value' => $contact->value,
                                        'comment' => isset($contact->comment) ? $contact->comment : ''
                                    );
                                break;
                            
                                default:
                                   $contactsGroups[$groupName]['web'][] = array(
                                        'type'  => $contact->type,
                                        'value' => $contact->value,
                                       'comment' => isset($contact->comment) ? $contact->comment : ''
                                    );
                            }
                        endforeach;
                    endforeach;
                    
                    foreach($contactsGroups as $name => $group):
                        if ($name != 'default'):
                            echo "<span style='margin-left:19px;'>$name</span>";
                        endif;
                        if (isset($group['phones'])):?>
                            <ul class="phone-info">
                                <?php foreach($group['phones'] as $phone):?>
                                    <li>
                                        <?php echo $phone['value'];?> <div class="<?php echo $phone['type']?>-icon"></div> 
                                        <?php if($phone['comment']) echo ' - ' . $phone['comment'] ?>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        <?php endif;
                        if (isset($group['web'])):?>
                            <ul class="web-info">
                                <?php foreach($group['web'] as $web_data):?>
                                        <li>
                                    <?php if($web_data['type'] == 'website'):?>
                                        <a href="<?php echo $web_data['value']?>" target="_blank"><?php echo ($web_data['alias'] ? $web_data['alias'] : $web_data['value'])?></a>
                                        <div class="site-icon"></div>
                                    <?php elseif($web_data['type'] == 'email'):?>
                                        <a href="mailto:<?php echo $web_data['value']?>"><?php echo $web_data['value']?></a>
                                        <div class="mail-icon"></div>
                                    <?php else:?>
                                        <?php echo $web_data['value']?>
                                        <div class="<?php echo $web_data['type']?>-icon"></div>
                                    <?php endif;?>
                                    <?php if($web_data['comment']) echo ' - ' . $web_data['comment'] ?>
                                         </li>
                                <?php endforeach;?>
                            </ul>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <?php if(isset($filial->schedule)):
                $schedule = Helper::getFullWorkingHours($filial->schedule, $days);
                $whData = array();
                foreach($schedule['from'] as $i=>$v) {
                    if(!isset($whData[$schedule['days'][$i]])) {
                        $whData[$schedule['days'][$i]] = '';
                    } else {
                        $whData[$schedule['days'][$i]] .= '&nbsp;';
                    }
                    $whData[$schedule['days'][$i]] .= preg_replace('|(\d{2}):(\d{2})|', '$1<sup>$2</sup>', $v) . '&ndash;';
                    $whData[$schedule['days'][$i]] .= preg_replace('|(\d{2}):(\d{2})|', '$1<sup>$2</sup>', $schedule['to'][$i]);
                }
            ?>
                <ul class="wh-info">
                    <li class="wh-today">
                        <div style="display: inline; position: relative; zoom: 1;">
                            Сегодня <?php echo (isset($whData[$days[date('D')]]) ? $whData[$days[date('D')]] : 'не работает') ?>
                            <a href="javascript:void(0)" class="show-weekly-wh pseudo">другие дни</a>
                        </div>
                        <div class="wh-icon"></div>
                    </li>
                    <li class="wh-week">
                        <div style="display: inline; position: relative; zoom: 1;">
                        <?php $i = 0; $cnt = count($whData); foreach($days as $dayId => $dayName):
                            if(isset($whData[$dayName])):
                                $i++;
                                echo $dayName . ' ' . $whData[$dayName] . ($i < $cnt ? '; ' : ' ');
                            endif;
                        endforeach;
                        if (isset($filial->schedule->comment)):
                            echo ' (' . $filial->schedule->comment . ')';
                        endif;?>
                        <a href="javascript:void(0)" class="show-today-wh pseudo">сегодня</a>
                        </div>
                        <div class="wh-icon"></div>
                    </li>
                </ul>
            <?php endif;?>

            <?php if(isset($filial->payoptions)) :?>
                <ul class="payment-methods">
                <?php $i = 0; $cnt = count($filial->payoptions); foreach ($filial->payoptions as $payoption): $i++;?>
                    <?php if ($cnt == $i):?>
                        <li style="position: relative;">
                            <div class="<?php echo strtolower($payoption);?>-icon" title="<?php echo Helper::getPayoptionName($payoption)?>"></div>
                        </li>
                    <?php else:?>
                        <li><div class="<?php echo strtolower($payoption);?>-icon" title="<?php echo Helper::getPayoptionName($payoption)?>"></div></li>
                    <?php endif;?>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            </div>
            <?php if((isset($filial->comment) && strlen($filial->comment) > 0) || (isset($filial->article) && strlen($filial->article) > 0)) :?>
                <div class="description" style="position: relative;">
                    <?php if(isset($filial->comment) && strlen($filial->comment) > 0) :?>
                        <p>Комментарий:</p>
                        <p><?php echo strip_tags($filial->comment)?></p>
                    <?php endif; ?>
                    <?php if(isset($filial->article) && strlen($filial->article) > 0) :?>
                        <p>Статья:</p>
                        <p><?php echo strip_tags($filial->article)?><p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if(isset($filial->fas_warning) && strlen($filial->fas_warning) > 0) :?>
                <div class="fas-msg"><?php echo strip_tags($filial->fas_warning)?></div>
            <?php endif; ?>
        </div>

        <?php
        if (isset($filial->response_code) && $filial->response_code == 200 && isset($filial->lat) && isset($filial->lon)) {
            $markers = array();
            $markers[] = array(
                'point' => array(
                            'lat' => $filial->lat,
                            'lon' => $filial->lon,
                    ),
                'text' => '<a href="" class="fname-s">' . addcslashes($filial->name, "\"") . '</a><br><span class="s-text">'.Helper::getFullAddress($filial).'</span>',
            );
            $centroid = array('lon' => $filial->lon, 'lat' => $filial->lat);

            $this->widget('application.components.widgets.dgMap.DGMap', array('markers' => $markers, 'centroid' => $centroid, 'mapsApiUrl' => Yii::app()->params['mapsApiUrl']));
        }
        ?>

    </div><!-- /firm-card -->

<?php else :?>
    <p>Фирма не найдена.</p>
<?php endif;?>