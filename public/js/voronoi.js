
    map = L.mapbox.map('map', 'zetter.i73ka9hn')
      .fitBounds([[19.4889 ,  -99.1836], [19.38 , -99]]);

    url = 'js/voronoi.csv';
    initialSelection = d3.set(['Heineken']);
    voronoiMap(map, url, initialSelection);