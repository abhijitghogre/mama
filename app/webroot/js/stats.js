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
        statsAjax(statstype, 0);

    });

    // default view i.e; current day stats on load 
    if ($url.indexOf('logs/statistics') > 0) {
        $('#day').trigger('click');
    }

    //ajax on filter change
    $('#project_id,#statsdatefrom,#statsdateto,#filter').on('change', function() {
        var statstype = $('.statsbtn.active').val();
        statsAjax(statstype, 0);
    });

    //generate stats csv
    $('.report-stats').on('click', function() {
        var statstype = $('.statsbtn.active').val();
        statsAjax(statstype, 1);
    });

});// ready ends

function postData(action, method, input) {
    "use strict";
    var form;
    form = $('<form />', {
        action: action,
        method: method,
        style: 'display: none;'
    });
    if (typeof input !== 'undefined') {
        $.each(input, function(name, value) {
            $('<input />', {
                type: 'hidden',
                name: name,
                value: value
            }).appendTo(form);
        });
    }
    form.appendTo('body').submit();
}


function statsAjax(statstype, csv) {

    var projectId = $('#project_id').val();
    var statsDateFrom = $('#statsdatefrom').val();
    var statsDateTo = $('#statsdateto').val();
    var filter = $('#filter').val();

    $.post(BASE_URL + '/statistics/show/' + csv, {
        projectId: projectId,
        statsDateFrom: statsDateFrom,
        statsDateTo: statsDateTo,
        filter: filter,
        statstype: statstype
    },
    function(data) {
        if (csv === 0) {
            switch (parseInt(filter)) {
                case 1:
                    barChart(data);
                    break;
                case 2:
                case 3:
                case 4:
                case 5:
                    stackedChart(data);
                    break;
            }
        } else {
            postData(BASE_URL + "/statistics/download", 'post', {
                jsondata: data,
                filter: parseInt(filter)
            });
        }
    });
}



function barChart(data) {
    console.log(JSON.parse(data));
    $('#stats-box').html('<div id="bar-chart" style="height: 230px;"></div>');
    if (JSON.parse(data).length === 1) {
        $('#stats-box').html('<div id="bar-chart" style="height: 230px;width:200px;"></div>');
    }
    if (JSON.parse(data).length === 2) {
        $('#stats-box').html('<div id="bar-chart" style="height: 230px;width:400px;"></div>');
    }
    if (JSON.parse(data).length === 3) {
        $('#stats-box').html('<div id="bar-chart" style="height: 230px;width:500px;"></div>');
    }
    if (JSON.parse(data).length == 0) {
        $('#bar-chart').html('No data available');
    } else {
        Morris.Bar({
            element: 'bar-chart',
            data: JSON.parse(data),
            xkey: 'x',
            ykeys: ['count'],
            labels: ['count'],
            barRatio: 0.4,
            xLabelMargin: 10,
            hideHover: 'auto',
            barColors: ["#3d88ba"]
        });
    }
    /**
     * 
     * Morris.Bar({
     element: 'hero-bar',
     data: [
     {x: '1', count: 136},
     {x: '3G', count: 1037},
     {x: '3GS', count: 275},
     {x: '4', count: 380},
     {x: '4S', count: 655},
     {x: '5', count: 1571}
     ],
     xkey: 'x',
     ykeys: ['count'],
     labels: ['count'],
     barRatio: 0.4,
     xLabelMargin: 10,
     hideHover: 'auto',
     barColors: ["#3d88ba"]
     });
     */
}
function stackedChart(data) {
    console.log(JSON.parse(data));
    $('#stats-box').html('<div id="bar-chart" style="height: 230px"></div>');
    if (JSON.parse(data).data.length === 1) {
        $('#stats-box').html('<div id="bar-chart" style="height: 230px;width:200px;"></div>');
    }
    if (JSON.parse(data).data.length === 2) {
        $('#stats-box').html('<div id="bar-chart" style="height: 230px;width:400px;"></div>');
    }
    if (JSON.parse(data).data.length === 3) {
        $('#stats-box').html('<div id="bar-chart" style="height: 230px;width:500px;"></div>');
    }
    if (JSON.parse(data).data.length == 0) {
        $('#bar-chart').html('No data available');
    } else {
        Morris.Bar({
            element: 'bar-chart',
            data: JSON.parse(data).data,
            xkey: 'x',
            ykeys: JSON.parse(data).keys,
            labels: JSON.parse(data).labels,
            stacked: true,
            hideHover: true
        });
    }


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
