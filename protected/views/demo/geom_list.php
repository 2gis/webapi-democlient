<?php
$cs = Yii::app()->getClientScript();
$script = "$(document).ready(function(){ $('.search-tab:last').click(); });";
$cs->registerScript('scr', $script, CClientScript::POS_HEAD);
    
if (isset($geoms->response_code) && $geoms->response_code == 200): ?>
    <?php $this->beginWidget('CHtmlPurifier');?>
        <div class="results-head">
            <?php if (isset($where)): ?>
            <h1>По названию «<?php echo $where; ?>»
                <? if ($geoms->total == 1): ?>
                    найден следующий геообъект:
                    <? else: ?>
                    найдены следующие геообъекты:
                    <? endif; ?>
            </h1>
            <?php else: ?>
                <h1>В точке с координатами <?php echo $lat . ',' . $lon; ?> располагаются следующие геообъекты:</h1>
                <p class="founded">Всего геообъектов: <?php echo $geoms->total; ?></p>
            <?php endif; ?>
        </div><!-- /results-head -->
    <?php $this->endWidget();?>
 
    <div class="results clearfix  dg-api-geo-objects-results">
        <div class="results-content">
            <ul class="results-geo dg-api-geo-objects-list">
                <?php foreach ($geoms->result as $key => $geom): ?>
                <li class="dg-api-geo-objects-row" id="results-geo-row<? echo $key; ?>">
                    <span class="results-icon <?php echo Helper::getGeoTypeIcon($geom->type) ?>"></span>
                    <a href="javascript:void(0)" class="dg-api-geo-objects-title"><?php echo $geom->name; ?></a>
                    <p class="dg-api-geo-objects-info"><?php echo Helper::getGeoTypeRussianName($geom->type); ?></p>
                </li><!-- /row -->
                <?php endforeach; ?>
            </ul>
        </div>

        <?php
            $geometries = array();
            foreach ($geoms->result as $geom) {
                $geometries[] = array(
                    'wkt' =>  $geom->selection,
                    'name' => $geom->name,
                    'type' => Helper::getGeoTypeRussianName($geom->type)
                );
            }
            $this->widget('application.components.widgets.dgMap.DGMap', array('centroid' => $center, 'geometries' => $geometries, 'mapsApiUrl' => Yii::app()->params['mapsApiUrl']));
        ?>
    </div><!-- /results -->
<?php else: ?>
    <?php $this->beginWidget('CHtmlPurifier');?>
        <div class="results-head">
            <h1>По названию «<?php echo $where; ?>» ничего не найдено.</h1>
            <?php if (!$rawJson): ?>
                <p class="founded">Не удалось выполнить запрос.</p>
            <?php endif; ?>
        </div><!-- /results-head -->
    <?php $this->endWidget();?>
<?php endif; ?>