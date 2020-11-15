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
        Highcharts.chart(this.selector, {

            chart: {
                type: 'area',
                zoomType: 'x',
                panning: true,
                panKey: 'shift'
            },

            title: {
                text: 'Höhendiagramm'
            },

            annotations: [{
                labelOptions: {
                    backgroundColor: 'rgba(255,255,255,0.5)',
                    verticalAlign: 'top',
                    y: 15
                },
                labels: []
            }],

            xAxis: {
                labels: {
                    format: '{value} km'
                },
                minRange: 5,
                title: {
                    text: 'Entfernung'
                }
            },

            yAxis: {
                startOnTick: true,
                endOnTick: false,
                maxPadding: 0.35,
                title: {
                    text: null
                },
                labels: {
                    format: '{value} m'
                }
            },

            tooltip: {
                headerFormat: 'Entfernung: {point.x:.1f} km<br>',
                pointFormat: '{point.y} m a. s. l.',
                shared: true
            },

            legend: {
                enabled: false
            },

            series: [{
                data: this.$el.data('elevation'),
                lineColor: Highcharts.getOptions().colors[1],
                color: Highcharts.getOptions().colors[2],
                fillOpacity: 0.5,
                name: 'Höhe',
                marker: {
                    enabled: false
                },
                threshold: null
            }]

        });
    }
}

// Singleton pattern, so all vars have their correct state
export default (new Chart());
