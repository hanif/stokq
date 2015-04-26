"use strict";

function NumberFormatter(options) {
    return new google.visualization.NumberFormat(options);
}

function GoogleOnLoadCallback(callback) {
    google.setOnLoadCallback(callback);
}

function ArrayToDataTable(data) {
    return new google.visualization.arrayToDataTable(data);
}

function BaseChart() {
    this.chart = undefined;
    this.data = undefined;
    this.options = {};

    this.format = function(callback) {
        callback(this.data);
    };

    this.draw = function(elemId) {
        if (!this.data) {
            return alert('Data is not set.');
        }

        this.chart = new this.chartClass((typeof elemId == 'string') ? document.getElementById(elemId) : elemId);
        this.chart.draw(this.data, this.options);
        return this;
    };

    this.data = function(data) {
        if (data) {
            if (Array.isArray(data)) {
                this.data = ArrayToDataTable(data);
            } else {
                this.data = data;
            }
            return this;
        }
        return this.data;
    };

    this.config = function(callback) {
        callback(this.options);
        return this;
    };
}

LineChart.prototype = new BaseChart();
function LineChart() {
    this.chartClass = google.visualization.LineChart;
    this.options = {
        legend: {position: 'bottom', alignment: 'center'},
        lineWidth: 4,
        pointSize: 10,
        visibleInLegend: true
    };
}

AreaChart.prototype = new BaseChart();
function AreaChart() {
    this.chartClass = google.visualization.AreaChart;
    this.options = {
        legend: {position: 'bottom', alignment: 'center'},
        lineWidth: 4,
        pointSize: 10,
        visibleInLegend: true
    };
}

ColumnChart.prototype = new BaseChart();
function ColumnChart() {
    this.chartClass = google.visualization.ColumnChart;
    this.options = {
        legend: {position: 'none'},
        visibleInLegend: true
    };
}

PieChart.prototype = new BaseChart();
function PieChart() {
    this.chartClass = google.visualization.PieChart;
    this.options = {
        is3D: false,
        legend: {position: 'labeled', alignment: 'right', maxLines: 1},
        chartArea: {left:10, top:10, width:'90%', height:'90%'},
        pieHole: 0.4
    };
}

MaterialBarChart.prototype = new BaseChart();
function MaterialBarChart() {
    this.chartClass = google.charts.Bar;
    this.options = {
        bars: 'horizontal',
        legend: {position: 'none'}
    };
}

MaterialColumnChart.prototype = new BaseChart();
function MaterialColumnChart() {
    this.chartClass = google.charts.Bar;
    this.options = {
        bars: 'vertical',
        legend: {position: 'none'}
    };
}