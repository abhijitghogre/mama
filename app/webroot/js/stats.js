$(document).ready(function() {
    var $url = document.URL ;
    
    $('#statsdate').datepicker({
        dateFormat: 'dd-mm-yy'
    }).datepicker("setDate", new Date());
    // default view i.e; current day stats on load 
    if($url.indexOf('logs/statistics') > 0){
        $('#day').addClass('active');
        var projectId = $('#project_id').val();
        var statsDate = $('#statsdate').val();
        var filter = $('#filter').val();
        var statstype = $('#day').val();
        $.post(BASE_URL + '/logs/statistics', {projectId: projectId, statsDate: statsDate, filter: filter, statstype:statstype}, function(data) {
            barChart(data);
        });
    }
    $('.statsbtn').on('click', function(data){
        $('#bar-chart').remove();
        $('.statsbtn').removeClass('active');
        $(this).addClass('active');
        var projectId = $('#project_id').val();
        var statsDate = $('#statsdate').val();
        var filter = $('#filter').val();
        var statstype = $(this).val();
        if(filter != 4){
            $.post(BASE_URL + '/logs/statistics', {projectId: projectId, statsDate: statsDate, filter: filter, statstype:statstype}, function(data) {
                $('#stats-box').html('<div id="bar-chart" style="height: 230px;"></div>');
                barChart(data);
            });
        }else{
            $.post(BASE_URL + '/logs/statistics', {projectId: projectId, statsDate: statsDate, filter: filter, statstype:statstype}, function(data) {
                $('#bar-chart').remove();
                $('#stats-box').html('<div id="stacked-chart" style="height: 230px;"></div>');
                stackedChart(data);
            });
        }
    });
    $('#project_id,#statsdate,#filter').on('change', function(data){
        $('#bar-chart').remove();
        var projectId = $('#project_id').val();
        var statsDate = $('#statsdate').val();
        var filter = $('#filter').val();
        var statstype = $('.statsbtn.active').val();
        if(filter != 4){
            $.post(BASE_URL + '/logs/statistics', {projectId: projectId, statsDate: statsDate, filter: filter, statstype:statstype}, function(data) {
                $('#stats-box').html('<div id="bar-chart" style="height: 230px;"></div>');
                barChart(data);
            });
        }else{
            $.post(BASE_URL + '/logs/statistics', {projectId: projectId, statsDate: statsDate, filter: filter, statstype:statstype}, function(data) {
                $('#bar-chart').remove();
                $('#stats-box').html('<div id="stacked-chart" style="height: 230px;"></div>');
                stackedChart(data);
            });
        }
    });
});
function barChart(data){
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
function stackedChart(){
    Morris.Bar({
        element: 'stacked-chart',
        data: [
          {x: '2011 Q1', y: 3, z: 2, a: 3},
          {x: '2011 Q2', y: 2, z: null, a: 1},
          {x: '2011 Q3', y: 0, z: 2, a: 4},
          {x: '2011 Q4', y: 2, z: 4, a: 3}
        ],
        xkey: 'x',
        ykeys: ['y', 'z', 'a'],
        labels: ['Y', 'Z', 'A'],
        stacked: true
    });
}
