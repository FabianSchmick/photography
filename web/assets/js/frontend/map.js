import * as L from "leaflet/src/leaflet";
import * as omnivore from "@mapbox/leaflet-omnivore/index";

// Create a new map with gpx track
export function map() {
    var $map = $('#map');

    var map = L.map("map", {
        scrollWheelZoom: false
    }).setView($map.data('coordinates'), 13);

    map.on("focus", function() { map.scrollWheelZoom.enable(); });
    map.on("blur", function() { map.scrollWheelZoom.disable(); });

    L.tileLayer("https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png", {
        maxZoom: 18,
        attribution: "&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>"
    }).addTo(map);

    var track = omnivore.gpx($map.data('gpx'), {}, L.geoJson());
    track.on("ready", function() {
        this.setStyle({
            color: "#00cdcd",
            weight: 5
        });
        this.eachLayer(function(marker) {
            marker.bindPopup(marker.feature.properties.name);
        });
    });

    map.addLayer(track);
}
