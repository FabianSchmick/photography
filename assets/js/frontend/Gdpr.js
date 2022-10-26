import Cookies from 'js-cookie';
import { Map } from './Map';

/**
 * Class Gdpr with methods data protection
 */
export class Gdpr {
    constructor() {
        this.$elMap = $('[data-gdpr-map]');
    }

    /**
     * Adds the listener for the gdpr map
     */
    initGdprForMap() {
        if (Cookies.get('gdpr-map')) {
            let map = new Map();
            map.initMap();
        }

        if (!this.$elMap.length) {
            return;
        }

        this.$elMap.on('click', e => {
            let $this = $(e.currentTarget),
                $form = $($this.closest('form.gdpr-form')),
                content = $(e.currentTarget).data('gdpr-content');

            if ($form.find('input.gdpr-checkbox').is(':checked')) {
                Cookies.set('gdpr-map', true);
            }

            $form.replaceWith(content);

            let map = new Map();
            map.initMap();
        });
    }
}
