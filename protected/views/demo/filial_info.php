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
                    <div class="dg-api-firm-branches-link">
                        <?php echo CHtml::link(
                        '<span id="selection_index67" class="selection_index"></span>'.
                            '<span class="dg-api-with-count-title">Посмотреть все филиалы</span><span class="dg-api-with-count-number">(' .
                            $filial->firm_group->count . ')</span>',
                        Yii::app()->controller->createUrl('demo/filials', array('firm_id' => $filial->firm_group->id)),
                        array('class'=>'dg-api-with-count')
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
                    $this->widget('application.components.widgets.workingHours.WorkingHours', array('params' => array('filial_schedule' => $filial->schedule)));
                endif;?>
            <?php if (isset($filial->additional_info) || isset($filial->payoptions)): ?>
            <ul class="dg-api-firm-attrs">
                <?php if ( !empty($filial->additional_info->wifi) ||
                !empty($filial->additional_info->avg_price) ||
                !empty($filial->additional_info->business_lunch) ) :?>

                <li class="dg-api-firm-attrs-row">
                    <?php if (!empty($filial->additional_info->wifi)) :?>
                    <span>WiFi</span>
                    <?php endif; ?>
                    <?php if (!empty($filial->additional_info->business_lunch)) :?>
                    <span>Бизнес-ланч</span>
                    <?php endif; ?>
                    <?php if (!empty($filial->additional_info->avg_price)) :?>
                    <span><span>Средний чек <?=$filial->additional_info->avg_price?> </span><span class="dg-currency dg-currency-ru" classname="dg-currency dg-currency-ru">Р</span></span>
                    <?php endif; ?>
                </li>
                <?php endif;
                if (isset($filial->payoptions)): ?>
                    <li class="dg-api-firm-attrs-row">

                        <?php foreach ($filial->payoptions as $payoption): ?>
                        <span><?=Helper::getPayoptionName($payoption)?></span>
                        <?php endforeach; ?>
                    </li>
                    <?php endif;?>
            </ul>
            <?php endif; ?>
            </div>
            <?php if((isset($filial->comment) && strlen($filial->comment) > 0) || (isset($filial->article) && strlen($filial->article) > 0)) :?>
                <div class="description" style="position: relative;">
                    <?php if(isset($filial->comment) && strlen($filial->comment) > 0) :?>
                        <p><?php echo strip_tags($filial->comment)?></p>
                    <?php endif; ?>
                    <?php if(isset($filial->article) && strlen($filial->article) > 0) :?>
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
