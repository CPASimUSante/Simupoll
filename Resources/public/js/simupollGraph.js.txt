(function () {
    'use strict';

    var radarChartData = {};
    var ctx;
    var canvas;
    var userdata;
    var resourcedata;
    var sid = $('#sid').html();

    ///Get the data and Draw the chart
    $('#showgraph').on('click', function(){
        $.ajax({
            type:"GET",
            url: Routing.generate('cpasimusante_simupoll_stats_json', {id:sid}),
            success: function(response) {
                radarChartData = response;
                setRadarChart(radarChartData);
            },
            error: function(jqXHR, textStatus, errorThrown) { }
        });
    });

    //function for creating custom tooltip for datasets
    function annotateAllX(area,ctx,data,statData,posi,posj,othervars) {
        var retstring='<B><U>'+statData[posi][posj].v2+'</U></B><BR>';
        for(var i=data.datasets.length-1;i>=0;i--) {
            var boxLegend="<canvas id=\"canvas_Line"+posi+"_"+posj+"\" height=\"10\" width=\"30\" style=\"border:1px solid black;background:"+data.datasets[i].pointHighlightFill+"\">;</canvas>";
            retstring=retstring+boxLegend+" "+statData[i][posj].datavalue+" "+statData[i][posj].v1+"<BR>";
        }
        return "<%='"+retstring+"'%>".replace(/<BR>/g," ");
    }

    function setRadarChart(radarChartData) {
        var options = {
            canvasBorders : false
            ,canvasBordersWidth : 3
            ,canvasBordersColor : "black"
            ,legend : true
            ,inGraphDataShow : true
            ,annotateDisplay : true
            ,annotateLabel: annotateAllX
            ,responsive: true
        };
        var myRadar = new Chart(
            document.getElementById('radaranalytics')   //<canvas>
            .getContext("2d")
        ).Radar(radarChartData, options);
    }

    $('#exportgraph').on('click', function(){
        downloadCanvas(this, 'radaranalytics', 'radar.png');
    });

    function downloadCanvas(link, canvasId, filename) {
        link.href = document.getElementById(canvasId).toDataURL();
        link.download = filename;/**/
    }

}());
