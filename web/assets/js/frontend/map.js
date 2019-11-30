import * as L from 'leaflet/src/leaflet';
import * as omnivore from '@mapbox/leaflet-omnivore/index';
import marker from 'leaflet/dist/images/marker-icon.png';
import marker2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

/**
 * Create a new map with gpx track
 */
export function map() {
    let $map = $('#map');

    if (!$map.length) {
        return;
    }

    let map = L.map('map', {
        scrollWheelZoom: false
    }).setView($map.data('coordinates'), 13);

    map.on('focus', () => { map.scrollWheelZoom.enable(); });
    map.on('blur', () => { map.scrollWheelZoom.disable(); });

    // stupid hack so that leaflet's images work after going through webpack
    // https://github.com/PaulLeCam/react-leaflet/issues/255#issuecomment-388492108
    delete L.Icon.Default.prototype._getIconUrl;

    L.Icon.Default.mergeOptions({
        iconRetinaUrl: marker2x,
        iconUrl: marker,
        shadowUrl: markerShadow
    });

    L.tileLayer('https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

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
