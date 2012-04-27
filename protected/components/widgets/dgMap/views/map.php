        <script type="text/javascript">
            var markers = <?php echo json_encode($markers)?>;
            var centroid = <?php echo json_encode($centroid)?>;
            var geometries = <?php echo json_encode($geometries)?>;
            var assetsUrl = '<?php echo $assetsUrl?>';
        </script>
        <div class="results-sidebar">
            <div class="results-map-wrapper">
                <div class="results-map" id="map" style="width:308px;height:308px"></div>
            </div>
        </div>