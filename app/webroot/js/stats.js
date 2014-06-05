$(document).ready(function() {
    Morris.Bar({
        element: 'daily-stats',
        data: [
            {device: '1', sells: 6},
            {device: '3G', sells: 9},
            {device: '3GS', sells: 4},
            {device: '4', sells: 3},
            {device: '4S', sells: 2},
            {device: '5', sells: 1}
        ],
        xkey: 'device',
        ykeys: ['sells'],
        labels: ['Sells'],
        barRatio: 0.4,
        xLabelMargin: 10,
        hideHover: 'auto',
        barColors: ["#3d88ba"]
    });
    Morris.Bar({
        element: 'weekly-stats',
        data: [
            {device: '1', sells: 6},
            {device: '3G', sells: 9},
            {device: '3GS', sells: 4},
            {device: '4', sells: 3},
            {device: '4S', sells: 2},
            {device: '5', sells: 1}
        ],
        xkey: 'device',
        ykeys: ['sells'],
        labels: ['Sells'],
        barRatio: 0.4,
        xLabelMargin: 10,
        hideHover: 'auto',
        barColors: ["#3d88ba"]
    });
    Morris.Bar({
        element: 'monthly-stats',
        data: [
            {device: '1', sells: 6},
            {device: '3G', sells: 9},
            {device: '3GS', sells: 4},
            {device: '4', sells: 3},
            {device: '4S', sells: 2},
            {device: '5', sells: 1}
        ],
        xkey: 'device',
        ykeys: ['sells'],
        labels: ['Sells'],
        barRatio: 0.4,
        xLabelMargin: 10,
        hideHover: 'auto',
        barColors: ["#3d88ba"]
    });
    Morris.Bar({
        element: 'yearly-stats',
        data: [
            {device: '1', sells: 6},
            {device: '3G', sells: 9},
            {device: '3GS', sells: 4},
            {device: '4', sells: 3},
            {device: '4S', sells: 2},
            {device: '5', sells: 1}
        ],
        xkey: 'device',
        ykeys: ['sells'],
        labels: ['Sells'],
        barRatio: 0.4,
        xLabelMargin: 10,
        hideHover: 'auto',
        barColors: ["#3d88ba"]
    });
});
