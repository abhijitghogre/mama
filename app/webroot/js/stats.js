$(document).ready(function() {
    var $url = document.URL;

    //initialize datepicker
    $('#statsdatefrom,#statsdateto').datepicker({
        dateFormat: 'dd-mm-yy'
    }).datepicker("setDate", new Date());

    //ajax on stats button click
    $('.statsbtn').on('click', function(data) {
        $('.statsbtn').removeClass('active');
        $(this).addClass('active');
        var statstype = $(this).val();
        statsAjax(statstype);

    });

    // default view i.e; current day stats on load 
    if ($url.indexOf('logs/statistics') > 0) {
        $('#day').trigger('click');
    }

    //ajax on filter change
    $('#project_id,#statsdatefrom,#statsdateto,#filter').on('change', function() {
        var statstype = $('.statsbtn.active').val();
        statsAjax(statstype);
    });

});// ready endss


function statsAjax(statstype) {

    var projectId = $('#project_id').val();
    var statsDateFrom = $('#statsdatefrom').val();
    var statsDateTo = $('#statsdateto').val();
    var filter = $('#filter').val();

    $.post(BASE_URL + '/statistics/show', {
        projectId: projectId,
        statsDateFrom: statsDateFrom,
        statsDateTo: statsDateTo,
        filter: filter,
        statstype: statstype
    },
    function(data) {
        switch (parseInt(filter)) {
            case 2:
            case 3:
            case 4:
                stackedChart(data);
                break;
        }
    });
}

function barChart(data) {
    $('#stats-box').html('<div id="bar-chart" style="height: 230px;"></div>');
    Morris.Bar({
        element: 'bar-chart',
        data: eval(data),
        xkey: 'slot',
        ykeys: ['count'],
        labels: ['count'],
        barRatio: 0.4,
        xLabelMargin: 10,
        hideHover: 'auto',
        barColors: ["#3d88ba"]
    });
}
function stackedChart(data) {
    console.log(data);
    $('#stats-box').html('<div id="bar-chart" style="height: 230px;"></div>');
    Morris.Bar({
        element: 'bar-chart',
        data: JSON.parse(data).data,
        xkey: 'x',
        ykeys: JSON.parse(data).keys,
        labels: JSON.parse(data).labels,
        stacked: true
    });

    /**
     * element: 'stacked-chart',
     data: [
     {x: '2011 Q1', y: 3, z: 2, a: 3},
     {x: '2011 Q2', y: 2, z: null, a: 1},
     {x: '2011 Q3', y: 0, z: 2, a: 4},
     {x: '2011 Q4', y: 2, z: 4, a: 3}
     ],
     xkey: 'x',
     ykeys: ['y', 'z', 'a'],
     labels: ['Y', 'Z', 'A'],
     */
}
