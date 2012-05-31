function addMarker(map, mapPoint, content)
{
    var geoPoint = new DG.GeoPoint(mapPoint.lon, mapPoint.lat);
    map.setCenter(new DG.GeoPoint(mapPoint.lon,mapPoint.lat), 17);
    var marker = new DG.Markers.MarkerWithBalloon({
        geoPoint: geoPoint,
        icon: new DG.Icon(assetsUrl + '/m_orange_m.png', new DG.Size(26, 26)),
        balloonOptions: {
            contentHtml: content,
            adjustMapCenterToBalloon: true
        }
    });


    map.markers.add(marker);
   
   return map.markers.getDefaultGroup().indexOf(marker)
}

DG.autoload(function () {
    var myMap = new DG.Map('map');
    myMap.controls.add(new DG.Controls.Zoom({enableControl:true}));
    
    var redIcon = new DG.Icon(assetsUrl + '/m_red_m.png', new DG.Size(26, 26));
    var orangeIcon = new DG.Icon(assetsUrl + '/m_orange_m.png', new DG.Size(26, 26));
    var firms2markers = [];
    
    if (markers !== null) {
        
        for(var i = 0; i < markers.length; i++) {
            if (markers[i] !== null) {
                 firms2markers[i] = addMarker(myMap, markers[i].point, markers[i].text);
            } 
        }
        myMap.setBounds(myMap.markers.getBounds());
        
   } else if (geometries !== null) {
       
        var style = new DG.Style.Geometry();
        style.strokeColor = "#ee9900";
        style.strokeWidth = 3;
        style.fillColor = "#ee9900";
        style.fillOpacity = 0.2;

        if (DG.WKTParser.getObjectType(geometries[0].wkt) == 'POINT' ) {
            var point = DG.WKTParser.getObject(geometries[0].wkt).getPosition();
            addMarker(myMap, point, geometries[0].name);
        } else {
            var geometry = DG.WKTParser.getObject(geometries[0].wkt);
            myMap.geometries.add(geometry);
            geometry.setStyle(style);
            myMap.setBounds(geometry.getBounds());
        }

        $('#results-geo-row0').addClass('dg-api-geo-objects-selected');
    }
            
    $("h2.title").hover(
        function () {
            var firmId = $(this).parent().index('li.results-org-row');
            if (firms2markers[firmId] !== undefined) {
                var marker = myMap.markers.getDefaultGroup().get(firms2markers[firmId]);
                marker.setIcon(redIcon);
            }
        }, 
        function () {
            var firmId = $(this).parent().index('li.results-org-row');
            if (firms2markers[firmId] !== undefined) {
                var marker = myMap.markers.getDefaultGroup().get(firms2markers[firmId]);
                marker.setIcon(orangeIcon);
            }
        }
        );

    $('.dg-api-geo-objects-row').click(function(){
        $('.dg-api-geo-objects-row').removeClass('dg-api-geo-objects-selected');
        $(this).addClass('dg-api-geo-objects-selected');
        var geomId = $(this).attr('id').substr(15);
        myMap.geometries.removeAll();
        myMap.markers.removeAll();
        myMap.balloons.removeAll();
        if (DG.WKTParser.getObjectType(geometries[geomId].wkt) == 'POINT' ) {
            var point = DG.WKTParser.getObject(geometries[geomId].wkt).getPosition();
            addMarker(myMap, point, geometries[geomId].name);
        } else {
            var geometry = DG.WKTParser.getObject(geometries[geomId].wkt);
            myMap.geometries.add(geometry);
            geometry.setStyle(style);
            myMap.setBounds(geometry.getBounds());
        }
    });
})