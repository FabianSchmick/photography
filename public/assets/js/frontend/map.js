import * as L from 'leaflet';
import * as omnivore from '@mapbox/leaflet-omnivore/index';
import markerPoi from '../../img/layout/icons/map-poi-icon.svg';
import markerStart from '../../img/layout/icons/map-start-icon.svg';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

/**
 * Create a new map with gpx track
 */
export function map() {
    let $map = $('#map');

    if (!$map.length) {
        return;
    }

    let map = L.map($map.attr('id'), {
        scrollWheelZoom: false
    }).setView($map.data('coordinates'), 13);

    map.on('focus', () => { map.scrollWheelZoom.enable(); });
    map.on('blur', () => { map.scrollWheelZoom.disable(); });

    // stupid hack so that leaflet's images work after going through webpack
    // https://github.com/PaulLeCam/react-leaflet/issues/255#issuecomment-388492108
    delete L.Icon.Default.prototype._getIconUrl;

    L.Icon.Default.mergeOptions({
        iconRetinaUrl: markerPoi,
        iconUrl: markerPoi,
        shadowUrl: markerShadow
    });

    L.tileLayer('https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Start icon should be bigger than rest
    let startIcon = L.icon({
        iconUrl: markerStart,
        shadowUrl: markerShadow,
        iconSize: [38, 120],
        shadowSize: [50, 64],
        iconAnchor: [22, 94],
        shadowAnchor: [15, 65],
        popupAnchor: [ -4, -60]
    });

    L.marker($map.data('coordinates'), { icon: startIcon })
        .addTo(map)
        .bindPopup('Tour start');

    let track = omnivore.gpx($map.data('gpx'), {}, L.geoJson());
    track.on('ready', function() {
        this.setStyle({
            color: '#00cdcd',
            weight: 5
        });
        this.eachLayer(marker => {
            marker.bindPopup(marker.feature.properties.name);
        });
    });

    map.addLayer(track);
}
