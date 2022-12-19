<div class="content">
    <div id="map" style="width: 100%; height: 530px; color:black;"></div>
</div>
<script>
    var Masjid = new L.LayerGroup();
    var sungai = new L.LayerGroup();
    var provinsi = new L.LayerGroup();

    var map = L.map('map', {
        center: [-1.7912604466772375, 116.42311966554416],
        zoom: 5,
        zoomControl: false,
        layers: []
    });

    var GoogleSatelliteHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        attribution: 'Latihan Web GIS'
    }).addTo(map);

    var Esri_NatGeoWorldMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC',
        maxZoom: 16
    });

    var GoogleMaps = new
    L.TileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        opacity: 1.0,
        attribution: 'Latihan Web GIS'
    });

    var GoogleRoads = new
    L.TileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        opacity: 1.0,
        attribution: 'Latihan Web GIS'
    });

    var Stadia_AlidadeSmoothDark = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
    });

    var baseLayers = {
        'Google Satellite Hybrid': GoogleSatelliteHybrid,
        'Esri_NatGeoWorldMap': Esri_NatGeoWorldMap,
        'GoogleMaps': GoogleMaps,
        'GoogleRoads': GoogleRoads,
        'Stadia_AlidadeSmoothDark': Stadia_AlidadeSmoothDark
    };

    var groupedOverlays = {
        "Peta Dasar": {
            'Masjid': Masjid,
            'Sungai': sungai,
            'Provinsi': provinsi
        }
    };

    L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);

    var osmUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
    var osmAttrib = 'Map data &copy; OpenStreetMap contributors';
    var osm2 = new L.TileLayer(osmUrl, {
        minZoom: 0,
        maxZoom: 13,
        attribution: osmAttrib
    });
    var rect1 = {
        color: "#ff1100",
        weight: 3
    };
    var rect2 = {
        color: "#0000AA",
        weight: 1,
        opacity: 0,
        fillOpacity: 0
    };
    var miniMap = new L.Control.MiniMap(osm2, {
        toggleDisplay: true,
        position: "bottomright",
        aimingRectOptions: rect1,
        shadowRectOptions: rect2
    }).addTo(map);

    L.Control.geocoder({
        position: "topleft",
        collapsed: true
    }).addTo(map);

    /* GPS enabled geolocation control set to follow the user's location */
    var locateControl = L.control.locate({
        position: "topleft",
        drawCircle: true,
        follow: true,
        setView: true,
        keepCurrentZoomLevel: true,
        markerStyle: {
            weight: 1,
            opacity: 0.8,
            fillOpacity: 0.8
        },
        circleStyle: {
            weight: 1,
            clickable: false
        },
        icon: "fa fa-location-arrow",
        metric: false,
        strings: {
            title: "My location",
            popup: "You are within {distance} {unit} from this point",
            outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
        },
        locateOptions: {
            maxZoom: 18,
            watch: true,
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 10000
        }
    }).addTo(map);

    var zoom_bar = new L.Control.ZoomBar({
        position: 'topleft'
    }).addTo(map);

    L.control.coordinates({

        position: "bottomleft",
        decimals: 2,
        decimalSeperator: ",",
        labelTemplateLat: "Latitude: {y}",
        labelTemplateLng: "Longitude: {x}"
    }).addTo(map);
    /* scala */
    L.control.scale({
        metric: true,
        position: "bottomleft"
    }).addTo(map);

    var north = L.control({
        position: "bottomleft"
    });
    north.onAdd = function(map) {
        var div = L.DomUtil.create("div", "info legend");
        div.innerHTML = '<img src="<?= base_url() ?>assets/arah-mata-angin.png"style=width:200px;>';
        return div;
    }
    north.addTo(map);

    $.getJSON("<?= base_url() ?>assets/latihangis.geojson", function(data) {

        var ratIcon = L.icon({
            iconUrl: '<?= base_url() ?>assets/marker.png',
            iconSize: [10, ]
        });
        L.geoJson(data, {
            pointToLayer: function(feature, latlng) {
                var marker = L.marker(latlng, {
                    icon: ratIcon
                });
                marker.bindPopup(`${latlng}`);
                return marker;
            }
        }).addTo(Masjid);
    });

    $.getJSON("<?= base_url() ?>/assets/sungai.geojson", function(kode) {
        L.geoJson(kode, {
            style: function(feature) {
                var color,
                    kode = feature.properties.kode;
                if (kode < 2) color = "#f2051d";
                else if (kode > 0) color = "#f2051d";
                else color = "#f2051d"; // no data
                return {
                    color: "#999",
                    weight: 5,
                    color: color,
                    fillOpacity: .8
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup()
            }
        }).addTo(sungai);
    });

    $.getJSON("<?= base_url() ?>/assets/provinsi.geojson", function(kode) {
        L.geoJson(kode, {
            style: function(feature) {
                var fillColor,
                    kode = feature.properties.kode;
                if (kode > 34) fillColor = "#9792c0";
                else if (kode > 33) fillColor = "#596dd5"
                else if (kode > 32) fillColor = "#9e8bb1"
                else if (kode > 31) fillColor = "#a30f03"
                else if (kode > 30) fillColor = "#0b8443"
                else if (kode > 29) fillColor = "#11e9da"
                else if (kode > 28) fillColor = "#8fa9bf"
                else if (kode > 27) fillColor = "#3d70b3"
                else if (kode > 26) fillColor = "#6140a9"
                else if (kode > 25) fillColor = "#eb3940"
                else if (kode > 24) fillColor = "#4e7d97"
                else if (kode > 23) fillColor = "#16c914"
                else if (kode > 22) fillColor = "#1eb0e9"
                else if (kode > 21) fillColor = "#9ee94e"
                else if (kode > 20) fillColor = "#65d36c"
                else if (kode > 19) fillColor = "#d50b0a"
                else if (kode > 18) fillColor = "#b7361e"
                else if (kode > 17) fillColor = "#438c33"
                else if (kode > 16) fillColor = "#cb0ebd"
                else if (kode > 15) fillColor = "#5fb148"
                else if (kode > 14) fillColor = "#c63f96"
                else if (kode > 13) fillColor = "#0a8208"
                else if (kode > 12) fillColor = "#334554"
                else if (kode > 11) fillColor = "#7f94c5"
                else if (kode > 10) fillColor = "#6fe107"
                else if (kode > 9) fillColor = "#df1924"
                else if (kode > 8) fillColor = "#d17aef"
                else if (kode > 7) fillColor = "#274a77"
                else if (kode > 6) fillColor = "#1ed5b9"
                else if (kode > 5) fillColor = "#ab55b6"
                else if (kode > 4) fillColor = "#c5c629"
                else if (kode > 3) fillColor = "#0eb515";
                else if (kode > 2) fillColor = "#e93234";
                else if (kode > 1) fillColor = "#3363c3";
                else if (kode > 0) fillColor = "#fc2577";
                else fillColor = "#088c9b"; // no data
                return {
                    color: "#999",
                    weight: 1,
                    fillColor: fillColor,
                    fillOpacity: .6
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(feature.properties.PROV)
            }
        }).addTo(provinsi);
    });

    // Legenda
    const legend = L.control.Legend({
            position: "bottomright",
            title: "Keterangan",
            collapsed: true,
            symbolWidth: 24,
            opacity: 1,
            column: 1,
            legends: [{
                label: "Masjid",
                type: "image",
                url: "<?= base_url() ?>/assets/marker.png",
            }, {
                label: "Sungai",
                type: "polyline",
                color: "#f2051d",
                fillColor: "#f2051d",
                weight: 2
            }, {

                title: "Sungai"

            }, {
                label: "Provinsi",
                font: 29,
                type: "polygon",
                sides: 4,
                color: "#FF0000",
                fillColor: "#FF0000",
                weight: 2
            }]
        })
        .addTo(map);
</script>