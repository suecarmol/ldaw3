/*
var chart_status = 0;
var chart_slider_1;

$(document).ready(function(){

        //D3

        var radius = 960 / 2;

        var cluster = d3.layout.cluster().size([360, radius - 120]);

        var diagonal = d3.svg.diagonal.radial().projection(function(d) { return [d.y, d.x / 180 * Math.PI]; });

        var svg = d3.select("body").append("svg").attr("width", radius * 2).attr("height", radius * 2).append("g")
            .attr("transform", "translate(" + radius + "," + radius + ")");

        d3.json("/d/4063550/flare.json", function(error, root) {
          var nodes = cluster.nodes(root);

          var link = svg.selectAll("path.link")
              .data(cluster.links(nodes))
            .enter().append("path")
              .attr("class", "link")
              .attr("d", diagonal);

          var node = svg.selectAll("g.node")
              .data(nodes)
            .enter().append("g")
              .attr("class", "node")
              .attr("transform", function(d) { return "rotate(" + (d.x - 90) + ")translate(" + d.y + ")"; })

          node.append("circle")
              .attr("r", 4.5);

          node.append("text")
              .attr("dy", ".31em")
              .attr("text-anchor", function(d) { return d.x < 180 ? "start" : "end"; })
              .attr("transform", function(d) { return d.x < 180 ? "translate(8)" : "rotate(180)translate(-8)"; })
              .text(function(d) { return d.name; });
        });

        d3.select(self.frameElement).style("height", radius * 2 + "px");

         chart_slider_1 = ChartSlider('#chart_container','#charts_left_arrow_container','#charts_right_arrow_container');
         chart_slider_1.addElement(chart1);
         chart_slider_1.addElement(chart_2);
         chart_slider_1.addElement(chart_3);
         chart_slider_1.start();

});
    */
    

/*Charts slider*/
/*
function ChartSlider (container_id, left_arrow_id, right_arrow_id) {
    this.left_arrow = $(left_arrow_id);
    this.right_arrow = $(right_arrow_id);
    this.container = $(container_id);
    this.current_status = null;
    this.element_count = 0;
    this.chart_container = null;
    this.charts_container = [];
    this.parent = this;
    
    this.moveRight = function() {
      if(this.current_status != null)
      {
        this.current_status = this.current_status + 1;
        if(this.current_status >= this.element_count)
          this.current_status = 0;
        this.showChart(this.current_status);
      }
      else
      {
        alert("There are no charts available");
      }  
    };
    
    this.moveLeft = function() {
      if(this.current_status != null)
      {
        this.current_status = this.current_status - 1;
        if(this.current_status < 0)
          this.current_status = this.element_count - 1;
        this.showChart(this.current_status);
      }
      else
      {
        alert("There are no charts available");
      }  
    };
    
    this.addElement = function(chart_params)
    {
      this.charts_container[element_count] = chart_params;
      this.element_count =  this.element_count + 1;
    };
    
    this.showChart = function(chart_number)
    {
      if(chart_number < this.element_count)
      {
        this.chart_container = this.container.highcharts(charts_container[chart_number]);
      }
    };
    
    
    this.start = function()
    {
      if(this.element_count > 0)
      {
        this.showChart(0);
        this.current_status = 0;
      }
      else
      {
        alert("There are no charts available");
      }
    };
    */
    /*Navigation*/
    /*
    $(this.right_arrow).click(function(){
      parent.moveRight();
    });
    
    $(this.left_arrow).click(function(){
      parent.moveLeft();
    });
    
    return this;
}
*/