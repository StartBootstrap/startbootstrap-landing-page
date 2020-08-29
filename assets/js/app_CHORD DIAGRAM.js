var NameProvider = ["California", "Florida", "New Jersey", "New York", "Texas", "Cuba", "Dominican_Republic", "El_Salvador", "Guatemala", "Mexico", "other country", "other states"];

var matrix = [
    [8.33333333333334, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], //"California"
    [0, 8.33333333333334, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], //"Florida"
    [0, 0, 8.33333333333334, 0, 0, 0, 0, 0, 0, 0, 0, 0], //"New Jersey"
    [0, 0, 0, 8.33333333333334, 0, 0, 0, 0, 0, 0, 0, 0], //"New York"
    [0, 0, 0, 0, 8.33333333333334, 0, 0, 0, 0, 0, 0, 0], //"Texas"
    [0.0412804468388801, 2.92953599169688, 0.286052795285387, 0.0727806703632731, 0.133034350750875, 0, 0, 0, 0, 0, 0, 4.87064907839803], //"Cuba"
    [0.00532831973687776, 0.269421171488931, 0.772659250280224, 1.01454215323446, 0.0197656684895049, 0, 0, 0, 0, 0, 0, 6.25161677010334], //"Dominican_Republic"
    [0.269174397992554, 0.0702137883576404, 0.157576395922618, 0.160412447228482, 0.283039109057398, 0, 0, 0, 0, 0, 0, 7.39291719477464], //"El_Salvador"
    [0.170844463852546, 0.124357651489777, 0.14494148258504, 0.0910313315345278, 0.101532456000157, 0, 0, 0, 0, 0, 0, 7.70062594787128], //"Guatemala"
    [0.282100119641855, 0.0456179521432475, 0.041927348266654, 0.0364445246422708, 0.384376586613574, 0, 0, 0, 0, 0, 0, 7.54286680202573], //"Mexico"
    [0.0670643164664723, 0.617039105189387, 0.380554538084648, 0.435004710656993, 0.114049456707892, 0, 0, 0, 0, 0, 0, 6.71962120622794], //"other country"
    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8.33333333333334], //"other states"

];

var colors = ["#C4C4C4", "#69B40F", "#EC1D25", "#C8125C", "#008FC8", "#10218B", "#134B24", "#737373", "#F4D03F", "#3FF4DF", "#D4F43F", "#F47B3F"];

var chord = d3.layout.chord()
    .padding(.04)
    .sortSubgroups(d3.descending) //sort the chords inside an arc from high to low
    .sortChords(d3.descending) //which chord should be shown on top when chords cross. Now the biggest chord is at the bottom
    .matrix(matrix);

var width = 900,
    height = 500,
    innerRadius = Math.min(width, height) * .41,
    outerRadius = innerRadius * 1.05;
var fill = d3.scale.ordinal()
    .domain(d3.range(NameProvider.length))
    .range(colors);
var svg = d3.select("#chart").append("svg:svg")
    .attr("width", width)
    .attr("height", height + 196)
    .append("svg:g")
    .attr("transform", "translate(" + width / 1.8 + "," + (height / 2 + 70) + ")");


var arc = d3.svg.arc()
    .innerRadius(innerRadius)
    .outerRadius(outerRadius);

////////////////////////////////////////////////////////////
////////////////// Draw outer Arcs /////////////////////////
////////////////////////////////////////////////////////////

var g = svg.selectAll("g.group")
    .data(chord.groups)
    .enter().append("svg:g")
    .attr("class", "group")
    .on("mouseover", fade(.02))
    .on("mouseout", fade(.80));

g.append("svg:path")
    .style("stroke", function(d) { return fill(d.index); })
    .style("fill", function(d) { return fill(d.index); })
    .attr("d", arc);

////////////////////////////////////////////////////////////
////////////////// Append Ticks ////////////////////////////
////////////////////////////////////////////////////////////

var ticks = svg.append("svg:g").selectAll("g.ticks")
    .data(chord.groups)
    .enter().append("svg:g").selectAll("g.ticks")
    .attr("class", "ticks")
    .data(groupTicks)
    .enter().append("svg:g")
    .attr("transform", function(d) {
        return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")" +
            "translate(" + outerRadius + 40 + ",0)";
    });

ticks.append("svg:line")
    .attr("x1", 1)
    .attr("y1", 0)
    .attr("x2", 5)
    .attr("y2", 0)
    .style("stroke", "#000");

ticks.append("svg:text")
    .attr("x", 8)
    .attr("dy", ".35em")
    .attr("transform", function(d) { return d.angle > Math.PI ? "rotate(180)translate(-16)" : null; })
    .style("text-anchor", function(d) { return d.angle > Math.PI ? "end" : null; })
    .text(function(d) { return d.label; });

////////////////////////////////////////////////////////////
////////////////// Append Names ////////////////////////////
////////////////////////////////////////////////////////////

g.append("svg:text")
    .each(function(d) { d.angle = (d.startAngle + d.endAngle) / 2; })
    .attr("dy", ".35em")
    .attr("class", "titles")
    .attr("text-anchor", function(d) { return d.angle > Math.PI ? "end" : null; })
    .attr("transform", function(d) {
        return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")" +
            "translate(" + (innerRadius + 55) + ")" +
            (d.angle > Math.PI ? "rotate(180)" : "");
    })
    .text(function(d, i) { return NameProvider[i]; });

////////////////////////////////////////////////////////////
////////////////// Draw inner chords ///////////////////////
////////////////////////////////////////////////////////////

svg.selectAll("path.chord")
    .data(chord.chords)
    .enter().append("svg:path")
    .attr("class", "chord")
    .style("stroke", function(d) { return d3.rgb(fill(d.source.index)).darker(); })
    .style("fill", function(d) { return fill(d.source.index); })
    .attr("d", d3.svg.chord().radius(innerRadius));

////////////////////////////////////////////////////////////
////////////////// Extra Functions /////////////////////////
////////////////////////////////////////////////////////////

// Add a tooltip div. Here I define the general feature of the tooltip: stuff that do not depend on the data point.
// Its opacity is set to 0: we don't see it by default.
var tooltip = d3.select("#chart")
    .append("div")
    .style("opacity", 0)
    .attr("class", "tooltip")
    .style("background-color", "white")
    .style("border", "solid")
    .style("border-width", "1px")
    .style("border-radius", "5px")
    .style("padding", "10px")

// A function that change this tooltip when the user hover a point.
// Its opacity is set to 1: we can now see it. Plus it set the text and position of tooltip depending on the datapoint (d)
var showTooltip = function(d) {
    tooltip
        .style("opacity", 1)
        .html("Source: " + names[d.source.index] + "<br>Target: " + names[d.target.index])
        .style("left", (d3.event.pageX + 15) + "px")
        .style("top", (d3.event.pageY - 28) + "px")
}

// A function that change this tooltip when the leaves a point: just need to set opacity to 0 again
var hideTooltip = function(d) {
    tooltip
        .transition()
        .duration(1000)
        .style("opacity", 0)
}

// Returns an event handler for fading a given chord group.
function fade(opacity) {
    return function(d, i) {
        svg.selectAll("path.chord")
            .filter(function(d) { return d.source.index != i && d.target.index != i; })
            .transition()
            .style("stroke-opacity", opacity)
            .style("fill-opacity", opacity);
    };
} //fade

// Returns an array of tick angles and labels, given a group.
function groupTicks(d) {
    var k = (d.endAngle - d.startAngle) / d.value;
    return d3.range(0, d.value, 1).map(function(v, i) {
        return {
            angle: v * k + d.startAngle,
            label: i % 5 ? null : v + "%"
        };
    });
} //groupTicks