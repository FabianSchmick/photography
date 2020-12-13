import Highcharts from 'highcharts';

/**
 * Class Chart with methods for tour page
 */
class Chart {
    constructor() {
        this.selector = 'tour-elevation-chart';
        this.$el = $('#'+this.selector);
    }

    /**
     * Create a new elevation chart for a gpx track
     */
    initChart() {
        Highcharts.setOptions({
            lang: {
                decimalPoint: TRANSLATION_MAP['unit.decimalSeparator'],
                thousandsSep: TRANSLATION_MAP['unit.thousandsSeparator']
            }
        });

        Highcharts.chart(this.selector, {
            chart: {
                type: 'area',
                panning: false,
                style: {
                    fontFamily: '"Roboto", "Helvetica", "Arial", sans-serif'
                }
            },

            title: false,
            subtitle: false,
            legend: false,

            xAxis: {
                labels: {
                    format: '{value} km'
                }
            },

            yAxis: {
                startOnTick: true,
                endOnTick: false,
                maxPadding: 0.35,
                title: false,
                labels: {
                    format: '{value} m'
                }
            },

            tooltip: {
                headerFormat: TRANSLATION_MAP['chart.headerFormat']+'<br>',
                pointFormat: TRANSLATION_MAP['chart.pointFormat'],
                shared: true
            },

            series: [{
                data: this.$el.data('elevation'),
                lineColor: '#00cdcd',
                color: '#eee',
                fillOpacity: 0.5,
                threshold: null
            }]

        });
    }
}

// Singleton pattern, so all vars have their correct state
export default (new Chart());
