<?php if ($isSearchResult) : ?>
    <?php $this->beginWidget('CHtmlPurifier');?>
        <div class="results-head">
            <h1>Вы искали: «<?php echo htmlspecialchars($what);?>» в <?php echo ($search_condition == 'point' ? 'точке с координатами ' . $lat . '; ' . $lon . ' в радиусе ' . $rad . 'м' : '«' . ( isset($filials->where) ? $filials->where : htmlspecialchars($where) ) . '»')?></h1>
            <p class="founded"><?php if (isset($filials->total)):?>Найдено фирм: <?php echo $filials->total?><?php elseif(!$rawJson): ?>Не удалось выполнить запрос.<?php else: ?>Ничего не найдено, попробуйте уточнить запрос.<?php endif;?></p>
        </div><!-- /results-head -->
    <?php $this->endWidget();?>

    <?php if (isset($filials->did_you_mean) && ((isset($filials->did_you_mean->rubrics) && $where) || isset($filials->did_you_mean->geo))) :?>
        <div class="dym clearfix" style="position: relative;">
            <?php if (isset($filials->did_you_mean->rubrics) && $where) :?>
                <div class="dym-column">
                    <div class="dym-title">Возможно вы имели ввиду:</div>
                    <ul class="dym-list">
                        <?php foreach($filials->did_you_mean->rubrics as $varirant) : ?>
                            <li>
                                <?php echo CHtml::link($varirant->name, Yii::app()->controller->createUrl('demo/search', array('rubric' => $varirant->name, 'where' => $where, 'workingNow' => $workingNow))); ?>
                                <?php if(isset($varirant->keyword)) :?><div class="keywords"><?php echo $varirant->keyword; ?></div><? endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if(isset($filials->did_you_mean->geo)) :?>
                <div class="dym-column">
                    <div class="dym-title">Возможно вы искали в:</div>
                    <ul class="dym-list">
                        <?php foreach($filials->did_you_mean->geo as $geoObj) : ?>
                            <li>
                                <?php echo CHtml::link($geoObj->name, Yii::app()->controller->createUrl('demo/search', array('page' => $page, $searchParam => $what, 'where' => $geoObj->name, 'sort' => $sort, 'lat' => $lat, 'lon' => $lon, 'rad' => $rad, 'search_condition' => $search_condition, 'workingNow' => $workingNow))); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif;?>
        </div><!-- /didyoumean -->
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($filials->response_code) && $filials->response_code == 200 && ($isSearchResult && $filials->total > 0 || !$isSearchResult)): ?>
    <div class="results clearfix">
    <?php if ($isSearchResult && isset($filials->total) && $filials->total) : ?>
        <dl class="sorting clearfix">
            <dt>Сортировать:</dt>
            <dd <?php echo ($sort == 'relevance' ? 'class="active"' : 'style="position: relative;"')?>><?php echo CHtml::link('по лучшему найденному', Yii::app()->controller->createUrl('demo/search', array('page' => $page, $searchParam => $what, 'where' => $where, 'sort' => 'relevance', 'lat' => $lat, 'lon' => $lon, 'rad' => $rad, 'search_condition' => $search_condition, 'workingNow' => $workingNow)), array('class' => 'pseudo'));?>,</dd>
            <dd <?php echo ($sort == 'name' ? 'class="active"' : 'style="position: relative;"')?>><?php echo CHtml::link('по алфавиту', Yii::app()->controller->createUrl('demo/search', array('page' => $page, $searchParam => $what, 'where' => $where, 'sort' => 'name', 'lat' => $lat, 'lon' => $lon, 'rad' => $rad, 'search_condition' => $search_condition, 'workingNow' => $workingNow)), array('class' => 'pseudo'));?><?php if ($searchParam != 'rubric') : ?>,<?php endif; ?></dd>
            <?php if ($searchParam != 'rubric') : ?>
            <dd <?php echo ($sort == 'distance' ? 'class="active" style="position: relative;"' : 'style="position: relative;"')?>>
                <?php echo CHtml::link('по расстоянию', Yii::app()->controller->createUrl('demo/search', array('page' => $page, $searchParam => $what, 'where' => $where, 'sort' => 'distance', 'lat' => $lat, 'lon' => $lon, 'rad' => $rad, 'search_condition' => $search_condition, 'workingNow' => $workingNow)), array('class' => 'pseudo'));?>.
            </dd>
            <?php endif; ?>
        </dl>
    <?php endif; ?>
    
    <?php
        $markers = array();
        foreach ($filials->result as $filial) {
            if (isset($filial->lat) && isset($filial->lon)) {
                $url = $isSearchResult ?
                    $this->createUrl('demo/profile', array('filial_id' => $filial->id, 'hash' => $filial->hash, $searchParam => $what, 'where' => $where, 'sort' => $sort, 'lat' => $lat, 'lon' => $lon, 'rad' => $rad, 'search_condition' => $search_condition, 'workingNow' => $workingNow)) :
                    $this->createUrl('demo/profile', array('filial_id' => $filial->id, 'hash' => $filial->hash, 'lat' => $lat, 'lon' => $lon, 'rad' => $rad));

                $text = '<a href="' . $url . '" class="fname-s">' . addcslashes($filial->name, "\"") . '</a><br><span class="s-text">'.Helper::getFullAddress($filial).'</span>';
                $markers[] = array(
                    'point' => array(
                                'lat' => $filial->lat,
                                'lon' => $filial->lon,
                        ),
                    'text' => $text
                );
            } else {
                $markers[] = null;
            }
        }
        $this->widget('application.components.widgets.dgMap.DGMap', array('markers' => $markers, 'centroid' => $centroid, 'mapsApiUrl' => Yii::app()->params['mapsApiUrl']));
    ?>

    <div class="results-content">
    <?php if (isset($filials->advertising)) : ?>
        <ul class="adverts">
        <?php foreach ($filials->advertising as $advertising) :?>
            <li>
                <a class="advert-link" href="<?php echo Yii::app()->controller->createUrl('demo/profile', array('filial_id' => $advertising->firm_id, 'hash' => $advertising->hash))?>">
                    <h2 class="title"><?php echo $advertising->title?></h2>
                    <div class="description"><?php echo $advertising->text?></div>
                    <?php if ($advertising->fas_warning):?><div class="fas-msg-list"><?php echo $advertising->fas_warning ?></div><?php endif;?>
                    <div class="advert-icon"></div>
                </a>
            </li>
        <?php endforeach;?>
        </ul>
    <?php endif;?>

    <ol class="results-org">
    <?php
        $isFirstBranch = true;
    ?>
    <?php foreach ($filials->result as $key => $filial) :
        $num = ($limit * $page + $key + 1) - $limit;
    ?>
        <li class="results-org-row">
            <div class="number"><?php echo $num?></div>
            <?php
                $dist = '';
                if (isset($filial->dist)) {
                    $dist = ', <i><span style="color: #D83D27;" title="Расстояние от геообъекта" >' . $filial->dist . '&nbsp;м</span></i>';
                }
            ?>
            <h2 class="title">
            <?php
                $params = ($isSearchResult)
                      ? array('filial_id' => $filial->id, 'hash' => $filial->hash, $searchParam => $what, 'where' => $where, 'sort' => $sort, 'lat' => $lat, 'lon' => $lon, 'rad' => $rad, 'search_condition' => $search_condition, 'workingNow' => $workingNow)
                      : array('filial_id' => $filial->id, 'hash' => $filial->hash);
                echo CHtml::link($filial->name, Yii::app()->controller->createUrl('demo/profile', $params));
                echo $dist;
            ?>
            </h2>
            <?php if(isset($filial->micro_comment) && strlen($filial->micro_comment) > 0) :?>
                <div class="adv-comment" style="position: relative;">
                    <?php echo $filial->micro_comment?>
                        <?php if(isset($filial->fas_warning) && strlen($filial->fas_warning) > 0) :?>
                        <div class="fas-msg-list"><?php echo strip_tags($filial->fas_warning)?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($fullAddress = Helper::getFullAddress($filial)) :?>
                <address class="address"><?php echo $fullAddress; ?></address>
            <?php endif; ?>
            <ul class="rubrics">
                <?php if (isset($filial->rubrics)) :
                    $cnt = 0; $total_rubrics = count($filial->rubrics);
                    foreach($filial->rubrics as $rubric):
                        $cnt++;?>
                <li><?php echo $rubric?><?php if($cnt < $total_rubrics):?>,<?endif;?></li>
                <?php
                    endforeach;
                endif; ?>
            </ul>
            <?php if ($isSearchResult && isset($filial->firm_group) && $filial->firm_group->count > 1) :?>
            <div class="dg-api-firm-branches-link">
                <?php echo CHtml::link(
                '<span id="selection_index67" class="selection_index"></span>'.
                    '<span class="dg-api-with-count-title">Все филиалы</span><span class="dg-api-with-count-number">(' .
                    $filial->firm_group->count . ')</span>',
                Yii::app()->controller->createUrl('demo/filials', array('firm_id' => $filial->firm_group->id)),
                array('class'=>'dg-api-with-count')
            );?>
                <?php if($isFirstBranch):
                $isFirstBranch = false; ?>
                <div class="tooltip tooltip-rtl">
                    <a href="javascript:void(0)" class="toggle" title="Подсказка"></a>
                    <div class="arrow"></div>
                    <div class="balloon">Если у компании есть <a href="<?php echo Yii::app()->controller->createUrl('demo/filials', array('firm_id' => $filial->firm_group->id))?>">филиалы</a>, на их полный список можно поставить ссылку.</div>
                </div>
                <?php endif;?>
            </div>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ol>
    </div>

    </div><!-- /results -->

    <?php
		// @todo: rewrite this with Yii pagers
        $total = (int)$filials->total;
        if ( $total > $limit) { ?>
    <ul class="pager">
		<?php
        $pCount = ceil($total / $limit);
        $back = ( (10 - $page) > 0 ) ? $page : 5;
        $beg = ( (10 - $page) > 0 ) ? 10 - $page : 5;
        $b = $page - $back;
        $l = $page + $beg;
        if( $b < 0 ) $b = 0;
        if( $l > $pCount ) $l = $pCount;
        if ($isSearchResult) {
            $linkParams = array('pagesize' => $limit, $searchParam => $what, 'where' => $where, 'sort' => $sort, 'lat' => $lat, 'lon' => $lon, 'rad' => $rad, 'search_condition' => $search_condition, 'workingNow' => $workingNow);
        } else {
            $linkParams = array('pagesize' => $limit, 'firm_id' => $filial->firm_group->id);
        }
        if( ($page-1) != 0 ): ?>
        <li>
        <?php
            $linkParams['page'] = ($page-1);
            echo CHtml::link('Назад', Yii::app()->controller->createUrl($isSearchResult ? 'demo/search' : 'demo/filials', $linkParams));
        ?>
        </li>
		<?php endif;?>
        
        <?php for ($i = ($b+1); $i <= $l; $i++) : ?>
            <?php if ($i == $page) : ?>
        <li><?php echo $i ?></li>
            <?php else : ?>
        <li>
        <?php
            $linkParams['page'] = $i;
            echo CHtml::link($i, Yii::app()->controller->createUrl($isSearchResult ? 'demo/search' : 'demo/filials', $linkParams));
        ?>
        </li>
            <?php endif; ?>
        <?php endfor;?>
        
        <?php if( ($page) != $pCount ): ?>
        <li>
        <?php
            $linkParams['page'] = ($page+1);
            echo CHtml::link('Вперед', Yii::app()->controller->createUrl($isSearchResult ? 'demo/search' : 'demo/filials', $linkParams));
        ?>
        </li>
        <?php endif;?>
        
        <?php if( ($page + 10) <= $pCount ): ?>
        <li>
        <?php
            $linkParams['page'] = ($page+10);
            echo CHtml::link('...', Yii::app()->controller->createUrl($isSearchResult ? 'demo/search' : 'demo/filials', $linkParams));
        ?>
        </li>
        <?php endif;?>
    </ul><!-- /pager -->
        <?php } ?>
<?php endif;?>