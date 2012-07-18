                <div class="dg-firm-schedule">
                    <?if($roundTheClock):?>  
                    <span class="dg-firm-schedule-24">24/7</span>
                    <div class="dg-firm-schedule-short">
                        Работает круглосуточно
                        <?if($comment):?><div class="dg-work-note"><?=$comment?></div><?endif;?>
                    </div>
                    <?else:?>
                    <span class="dg-firm-schedule-clock<?if($opened):?> opened<?endif?>"></span>
                    <div class="dg-firm-schedule-short">
                        <span class="dg-work-time"><?=$today?></span>
                        <span class="dg-icon-expand"></span>
                        <span style="display: none;" class="dg-icon-collapse"></span>
                        <div class="dg-weekly-schedule">
                            <?foreach($days as $k => $v):?>
                            <?if($k == date('D')):?>
                            <span style="font-weight:bolder;">
                            <?endif;?>
                            <?=$v?> <span class="dg-label"><?=(isset($whData[$k]) ? $whData[$k] : '&ndash; выходной')?></span><?if($k != 'Sun'):?>,<br/><?endif;?>
                            <?if($k == date('D')):?>
                            </span>
                            <?endif;?>
                            <?endforeach;?>
                            
                            <?if($comment):?><div class="dg-work-note"><?=$comment?></div><?endif;?>
                        </div>
                    </div>
                    <?endif;?>
                </div>

<script type="text/javascript">
    $(function(){
        $('.dg-work-time').live('click',function(){
            var p = $(this).parent();
            $('.dg-icon-collapse, .dg-icon-expand, .dg-work-note', p).toggle();
            $('.dg-weekly-schedule', p).slideToggle(100);

        });
    });
</script>