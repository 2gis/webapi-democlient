function addMarker(map, mapPoint, content)
{
    var geoPoint = new DG.GeoPoint(mapPoint.lon, mapPoint.lat);
    
    var marker = new DG.Markers.Common({
        geoPoint: geoPoint,
        icon: new DG.Icon(assetsUrl + '/m_orange_m.png', new DG.Size(26, 26)),
        //hoverIcon: new DG.Icon(assetsUrl + '/m_red_m.png', new DG.Size(26, 26)),
        clickCallback: function () {
            map.balloons.removeAll();
            map.balloons.add(new DG.Balloons.Common({
                geoPoint: geoPoint,
                contentSize: new DG.Size(200, 100),
                contentHtml: content
            }));
        }
    });

   map.markers.add(marker);
   
   return map.markers.getDefaultGroup().indexOf(marker)
}

DG.autoload(function () {
    var myMap = new DG.Map('map');
    myMap.setCenter(new DG.GeoPoint(centroid.lon, centroid.lat));
    myMap.zoomTo(15);
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

        var lim = geometries.length - 1;
        for(var i = lim; i > -1 ; i--) {
            if (DG.WKTParser.getObjectType(geometries[i].wkt) == 'POINT') {
                point = DG.WKTParser.getObject(geometries[i].wkt);
                addMarker(myMap, point.getPosition(), geometries[i].name);
                geometry = null;
            } else {
                var geometry = DG.WKTParser.getObject(geometries[i].wkt);
                myMap.geometries.add(geometry);
                geometry.setStyle(style);

            }
        }

        if (DG.WKTParser.getObjectType(geometries[0].wkt) == 'POINT' ) {
            myMap.setCenter(new DG.GeoPoint(point.lon,point.lat), 17);
        } else {
            myMap.setBounds(geometry.getBounds());
        }

        $('#results-geo-row0').parent().addClass('dg-api-geo-objects-selected');
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

    $('a[id^="results-geo-row"]').click(function(){
        var arr = $('a[id^="results-geo-row"]*').get();
        for(var ind in arr) {
            $('#' + arr[ind].getAttribute('id')).parent().removeClass('dg-api-geo-objects-selected');
        }
        $(this).parent().addClass('dg-api-geo-objects-selected');
        var geomId = parseInt($(this).attr('id').substr(15));
        if (DG.WKTParser.getObjectType(geometries[geomId].wkt) == 'POINT' ) {
            var center = DG.WKTParser.getPoint(geometries[geomId].wkt).getPosition();
            myMap.setCenter(new DG.GeoPoint(center.lon,center.lat), 17);
        } else {
            var geometry = DG.WKTParser.getObject(geometries[geomId].wkt);
            myMap.setBounds(geometry.getBounds());
        }
        myMap.redraw();
    });
})