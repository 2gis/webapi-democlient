<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <?php
            $cs = Yii::app()->getClientScript();
            $cs->registerCssFile('/css/reset.css');
            $cs->registerCssFile('/css/design.css');
            $cs->registerCssFile('/css/geo-objects.css');
            $cs->registerScriptFile('/js/jquery-1.7.min.js');
            $cs->registerScriptFile('/js/script.js');
        ?>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>
    
    <body>
        <noscript>
            <style> .main { display: none } </style>
            <center>
            <h1>Отключена подержка JavaScript</h1>
            <p>Для работы приложения необходима поддержка JavaScript. Включите JavaScript и обновите страницу.</p>
            </center>
        </noscript>

        <div>
            <?php
                if($this->showSearchForm)
                    $this->widget('application.components.widgets.searchForm.SearchForm', array('params' => $this->params));
            ?>

            <div class="trunk">
                <?php echo $content;?>
            </div>
        </div>
    </body>
</html>
