// @TODO: YOUR CODE HERE!
d3.json("assets/data/data_prep.json").then((data) => {
    //  Create the Traces
    var trace1 = {
        x: data.Region,
        y: data.States.map(val => Math.sqrt(val)),
        type: "box",
        name: "Migration by region",
        boxpoints: "all"
    };

    // Create the data array for the plot
    var data = [trace1];

    // Define the plot layout
    var layout = {
        title: "Migration to US by analized region",
        xaxis: { title: "Region where migrants move to the US" },
        yaxis: { title: "Number of Migrants" }
    };

    // Plot the chart to a div tag with id "plot"
    Plotly.newPlot("plot", data, layout);
});