jQuery(document).ready(function($){$(".store-location-link").click(function(a){a.preventDefault();var e=$(this).data("entry-id");console.log("storeId: "+e),google.maps.event.trigger(smartMap.marker["smartmap-mapcanvas-1."+e+".storeLocation"],"click")});var a=[{featureType:"landscape",stylers:[{hue:"#F1FF00"},{saturation:-27.4},{lightness:9.4},{gamma:1}]},{featureType:"road.highway",stylers:[{hue:"#0099FF"},{saturation:-20},{lightness:36.4},{gamma:1}]},{featureType:"road.arterial",stylers:[{hue:"#00FF4F"},{saturation:0},{lightness:0},{gamma:1}]},{featureType:"road.local",stylers:[{hue:"#FFB300"},{saturation:-38},{lightness:11.2},{gamma:1}]},{featureType:"water",stylers:[{hue:"#00B6FF"},{saturation:4.2},{lightness:-63.4},{gamma:1}]},{featureType:"poi",stylers:[{hue:"#9FFF00"},{saturation:0},{lightness:0},{gamma:1}]}];smartMap.map["smartmap-mapcanvas-1"].setOptions({styles:a});for(var e={icon:{url:"/assets/img/theme/map-marker.png",scaledSize:new google.maps.Size(40,37),origin:new google.maps.Point(0,0),anchor:new google.maps.Point(17,37)},animation:google.maps.Animation.DROP},t=smartMap.listMarkers(),s=0;s<t.length;s++)smartMap.marker[t[s]].setOptions(e)});