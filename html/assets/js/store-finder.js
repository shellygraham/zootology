jQuery( document ).ready(function( $ ) {

  if (typeof smartMap === "undefined") {
    return;
  }

	$('.store-location-link').click(function(e){
		e.preventDefault();
		var storeId = $(this).data('entry-id');
		console.log('storeId: ' + storeId);
		google.maps.event.trigger(smartMap.marker['smartmap-mapcanvas-1.' + storeId + '.storeLocation'], 'click');
	});

	var style =
	[
	    {
	        "featureType": "landscape",
	        "stylers": [
	            {
	                "hue": "#F1FF00"
	            },
	            {
	                "saturation": -27.4
	            },
	            {
	                "lightness": 9.4
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "road.highway",
	        "stylers": [
	            {
	                "hue": "#0099FF"
	            },
	            {
	                "saturation": -20
	            },
	            {
	                "lightness": 36.4
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "road.arterial",
	        "stylers": [
	            {
	                "hue": "#00FF4F"
	            },
	            {
	                "saturation": 0
	            },
	            {
	                "lightness": 0
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "road.local",
	        "stylers": [
	            {
	                "hue": "#FFB300"
	            },
	            {
	                "saturation": -38
	            },
	            {
	                "lightness": 11.2
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "water",
	        "stylers": [
	            {
	                "hue": "#00B6FF"
	            },
	            {
	                "saturation": 4.2
	            },
	            {
	                "lightness": -63.4
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "poi",
	        "stylers": [
	            {
	                "hue": "#9FFF00"
	            },
	            {
	                "saturation": 0
	            },
	            {
	                "lightness": 0
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    }
	];


	smartMap.map['smartmap-mapcanvas-1'].setOptions({styles: style});

	// Custom markers
	var markerOptions = {icon: {
        url: '/assets/img/theme/map-marker.png',
        // This marker is 20 pixels wide by 32 pixels tall.
        scaledSize: new google.maps.Size(40, 37),
        // The origin for this image is 0,0.
        origin: new google.maps.Point(0,0),
        // The anchor for this image is the base of the flagpole at 0,32.
        anchor: new google.maps.Point(17, 37)
    },
	animation: google.maps.Animation.DROP };

    var myMarkers = smartMap.listMarkers();
    for( var i = 0; i < myMarkers.length; i++ ){
    	smartMap.marker[myMarkers[i]].setOptions(markerOptions);
    }
});

