<div class="search">
    <ul class="search-tabs">
        <li class="active"><a href="javascript:void(0)" data-type="firm-search" class="search-tab pseudo">Организации</a></li>
        <li><a href="javascript:void(0)" data-type="geo-search" class="search-tab pseudo">Геообъекты</a></li>
    </ul><!-- /Search-tabs -->

    <div class="search-wrapper search-organizations">

        <div class="firm-search">
        <?php echo CHtml::beginForm(Yii::app()->createUrl("demo/search"), 'GET', array('class'=>'search-form organizations bytitle', 'onsubmit'=>'return validate();')); ?>
            <div>
                <div class="cell what">
                    <label class="label" for="what_org">Что:</label>
                    <span class="placeholder">мото</span>
                    <?php echo CHtml::textField('what', (isset($what) ? $what : ''), array('class' => 'textfield' , 'id'=>'what_org', 'maxlength' => 500)) ?>
                    <span class="tip">Укажите название или сферу деятельности организации</span>
                </div>
                <div class="cell where">
                    <label class="label" for="where_org">Где:</label>
                    <span class="placeholder">Новосибирск</span>
                    <?php echo CHtml::textField('where', (isset($where) ? $where : ''), array('class' => 'textfield', 'id'=>'where_org', 'maxlength' => 500)) ?>
                    <a href="javascript:void(0)" class="pseudo change-method">искать по координатам</a>
                    <span class="tip">Обязательно укажите название города</span>
                </div>
            </div><!-- /organizations by title -->
            <input type="submit" class="submit" value="Найти" />
            <span class="is-working-now"><?php echo CHtml::checkBox('workingNow', $workingNow, array('id' => 'working_now')) ?> <label for="working_now">Работает сейчас</label></span>
            <?php echo CHtml::hiddenField('sort', isset($sort) ? $sort : 'relevance'); ?>
        <?php echo CHtml::endForm(); ?>
        
        <?php echo CHtml::beginForm(Yii::app()->createUrl("demo/search"), 'GET', array('class'=>'search-form organizations bycoordinates', 'onsubmit'=>'return validate();')); ?>    
            <div>
                <div class="cell what">
                    <label class="label" for="what_coord_org">Что:</label>
                    <span class="placeholder">мото</span>
                    <?php echo CHtml::textField('what', (isset($what) ? $what : ''), array('class' => 'textfield' , 'id'=>'what_coord_org', 'maxlength' => 500)) ?>
                    <span class="tip">Укажите название или сферу деятельности организации</span>
                </div>
                <div class="cell latitude">
                    <label class="label" for="latitude_org">Широта:</label>
                    <?php echo CHtml::textField('lat', (isset($lat) ? $lat : ''), array('title'=>'Lat', 'class' => 'textfield', 'id' => 'latitude_org')) ?>
                    <a href="javascript:void(0)" class="pseudo change-method">искать по названию</a>
                </div>
                <div class="cell longitude">
                    <label class="label" for="longitude_org">Долгота:</label>
                    <?php echo CHtml::textField('lon', (isset($lon) ? $lon : ''), array('title'=>'Lon', 'class' => 'textfield', 'id' => 'longitude_org')) ?>
                </div>
                <div class="cell radius">
                    <label class="label" for="radius_org">Радиус:</label>
                    <?php echo CHtml::textField('rad', (isset($rad) ? $rad : ''), array('title'=>'Radius', 'class' => 'textfield', 'id' => 'radius_org')) ?>
                </div>
            </div><!-- /organizations by coordinates -->

            <input type="submit" class="submit" value="Найти" />
            
            <span class="is-working-now"><?php echo CHtml::checkBox('workingNow', $workingNow, array('id' => 'working_now')) ?> <label for="working_now">Работает сейчас</label></span>
            <?php echo CHtml::hiddenField('search_condition', ((isset($search_condition) && ($search_condition == 'point')) ? 'point' : 'where'), array('id' => 'sc')); ?>
            <?php echo CHtml::hiddenField('sort', isset($sort) ? $sort : 'relevance'); ?>
        <?php echo CHtml::endForm(); ?><!-- /Organizations search -->
        </div>
        
        <?php echo CHtml::beginForm(Yii::app()->createUrl('demo/geoSearch'), 'GET', array('class' => 'search-form geoobjects', 'onsubmit'=>'return validateGeo();')); ?>

            <div class="cell what">
                <label class="label" for="what_geo">Что:</label>
                <span class="placeholder">Ленина, Новосибирск</span>
                <?php echo CHtml::textField('q', (isset($q) ? $q : ''), array('class' => 'textfield', 'id'=>'what_geo', 'maxlength' => 500)) ?>
                <span class="tip">Введите запрос, указав город. Будет найден один наиболее релевантный вариант</span>
            </div>

            <input type="submit" class="submit" value="Найти" />

            <?php echo CHtml::hiddenField('searchType', 'byName'); ?>
        <?php echo CHtml::endForm(); ?><!-- /Geoobjects search -->

    </div><!-- /search-wrapper -->
</div><!-- /search -->
