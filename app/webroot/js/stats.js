$(document).ready(function() {
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    $('#statsdate').val(today);
    $('#stats-title').html(today.toString());
    $('#day').addClass('active');
    var projectId = $('#project_id').val();
    var statsDate = $('#statsdate').val();
    var filter = $('#filter').val();
    $.post(BASE_URL + '/logs/statistics', {projectId: projectId, statsDate: statsDate, filter: filter}, function(data) {
        dailystats();
    });
    /*$('#project_id,#statsdate,#filter').on('change change.dfhdatepicker', function(data) {
        var projectId = $('#project_id').val();
        var statsDate = $('#statsdate').val();
        var filter = $('#filter').val();
        $.post(BASE_URL + '/logs/statistics', {projectId: projectId, statsDate: statsDate, filter: filter}, function(data) {
            alert(data);
            dailystats(data);
        });
    });*/
});
function dailystats(){
    Morris.Bar({
        element: 'daily-stats',
        data: [
            {slot: '9:00 AM - 12:00 PM', calls: 6},
            {slot: '12:00 PM - 3:00 PM', calls: 9},
            {slot: '3:00 PM - 6:00 PM', calls: 4},
            {slot: '6:00 PM - 9:00 PM', calls: 3},
            {slot: '9:00 PM - 11:00 PM', calls: 2}
        ],
        xkey: 'slot',
        ykeys: ['calls'],
        labels: ['calls'],
        barRatio: 0.4,
        xLabelMargin: 10,
        hideHover: 'auto',
        barColors: ["#3d88ba"]
    });
}
function weeklystats(){
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
}
function monthlystats(){
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
}
function yearlystats(){
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
}
