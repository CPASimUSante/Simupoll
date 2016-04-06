(function () {
    'use strict';

    var radarChartData = {};
    var ctx;
    var canvas;
    var userdata;
    var resourcedata;
    var sid = $('#sid').html();
    var radar = [];
    var downloadBtn = [];

    ///Get the data and Draw the chart
    $('#showgraph').on('click', function(){
        $.ajax({
            type:"GET",
            url: Routing.generate('cpasimusante_simupoll_stats_json', {id:sid}),
            success: function(response) {
                var radarChartDataRaw = response;
                $.each(radarChartDataRaw, function( index, radarChartData ) {
                    //create canvas for each period
                    radar[index]= $('<canvas id="radaranalytics'+index+'" class="radarstat" height="550" width="500">');
                    $('#containerradar').append(radar[index]);
                    //create downloadBtn for each canvas
                    var title = radarChartData['graphtitle'];
                    downloadBtn[index]= $('<a class="btn btn-primary" id="exportgraph'+index+'"><i class="fa fa-download"></i>Enregistrer '+radarChartData['graphtitle']+'</a>');
                    downloadBtn[index].on('click', function(){
                        downloadCanvas(this, 'radaranalytics'+index, 'radar'+index+'.png');
                    });
                    $('#containerradar').append(downloadBtn[index]);
                    //display radar
                    setRadarChart(radarChartData['graph'], radarChartData['graphtitle'], index);
                });
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

    function setRadarChart(radarChartData, title, index) {
        var options = {
            canvasBorders : false
            ,canvasBordersWidth : 3
            ,canvasBordersColor : "black"
            ,legend : true
            ,inGraphDataShow : true
            ,annotateDisplay : true
            ,annotateLabel: annotateAllX
            ,responsive: true
            ,graphTitle : title
        };
        var myRadar = new Chart(
            document.getElementById('radaranalytics'+index)   //<canvas>
            .getContext("2d")
        ).Radar(radarChartData, options);
    }
/*
    $('#exportgraph').on('click', function(){
        downloadCanvas(this, 'radaranalytics4', 'radar4.png');
    });
*/
    function downloadCanvas(link, canvasId, filename) {
        link.href = document.getElementById(canvasId).toDataURL();
        link.download = filename;/**/
    }

}());
