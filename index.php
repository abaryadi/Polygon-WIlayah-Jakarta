<?php

if(!isset($_GET['mode'])){
	$mode = 'kecamatan';
}else{
	$mode = $_GET['mode'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Map Jakarta</title>
	
	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

	<link rel="stylesheet" href="assets/css/style.css">

	<link rel="stylesheet" href="assets/css/bootstrap-multiselect.min.css"/>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" ></script>

	<script src="assets/js/bootstrap-multiselect.min.js"></script>

	<script src="https://requirejs.org/docs/release/2.3.6/minified/require.js"></script>

	
	<style>
		html, body {
			height: 100%;
			margin: 0;
		}
		.leaflet-container {
			height: 400px;
			width: 600px;
			max-width: 100%;
			max-height: 100%;
		}

		.search-bar {
    position: fixed;
    z-index: 99999;
    top: 0;
    width: 25%;
    margin: auto;
    right: 1%;
    top: 12%;
    border-radius: 15px;
    background-color: #ffffffe3;
    padding: 18px;
    box-shadow: 4px 6px 7px -1px #9b939347;
}

.search-bar2{
    position: fixed;
    z-index: 99999;
    top: 0;
    width: 25%;
    margin: auto;
    right: 1%;
    top: 25%;
    border-radius: 15px;
    background-color: #ffffffe3;
    padding: 18px;
    box-shadow: 4px 6px 7px -1px #9b939347;
}
	</style>

	<style>#map { width: 100%; height: 100%; }
.info { padding: 6px 8px; font: 14px/16px Arial, Helvetica, sans-serif; background: white; background: rgba(255,255,255,0.8); box-shadow: 0 0 15px rgba(0,0,0,0.2); border-radius: 5px; } .info h4 { margin: 0 0 5px; color: #777; }
.legend { text-align: left; line-height: 18px; color: #555; } .legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7; }</style>
</head>
<body>

<div id='map'></div>
<div class="search-bar">
	<div class="container ">
		<div class="col-md-12">
			<div class="form-group">
				<label style="margin-bottom:10px">Tampilan</label>
				<br>
				<i class="fa fa-sun-o" aria-hidden="true"></i>
				<label class="switch btn-color-mode-switch">
					  <input type="checkbox" name="color_mode" id="color_mode" value="1" <?=($mode == 'wilayah') ? 'checked':''?>>
					  <label for="color_mode" data-on="Wilayah" data-off="Kecamatan" class="btn-color-mode-switch-inner"></label>
				  </label>
				<i class="fa fa-moon-o" aria-hidden="true"></i>
				
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
				<label>Pilih Daerah</label>
				<br>
				
				<div class="dropdown" data-control="checkbox-dropdown">
					<label class="dropdown-label">Wilayah</label>
					
					<div class="dropdown-list">
					<a href="#" data-toggle="check-all" class="dropdown-option">
						Check All  
					</a>

					<div id="dropdown-isi">

					</div>

				
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 mt-3">
			<div class="form-group">
				<label>Operator</label>
				<br>
				
				<div class="dropdown" data-control="checkbox-dropdown">
					<label class="dropdown-label">Wilayah</label>
					
					<div class="dropdown-list">
					<a href="#" data-toggle="check-all" class="dropdown-option">
						Check All  
					</a>

					<div id="dropdown-isi-operator">

					</div>

				
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
if($mode == 'kecamatan'){
?>
	<script type="text/javascript" src="batas_jakarta.js"></script>
<?php
}else{
	?>
	<script type="text/javascript" src="jakarta.js"></script>
	<?php
}
?>



<script type="text/javascript">

$(document).ready(function() {
  $.ajaxSetup({ cache: false });

  $("#color_mode").on("change", function () {
        //colorModePreview(this);
		if($('#color_mode').is(':checked')){
			window.location.replace("index.php?mode=wilayah");
		}else{
			window.location.replace("index.php?mode=kecamatan");
		}
		//
  });
});


	var operatorJson;
	$.getJSON('operator.json', function(json) {
		operatorJson = json;
	});
	var operator = [];
	//var operatorJson = require('./operator.json'); 

	var kotaSelected = [];
	var selected = [];
	var layerSelected = [];

	var unSelectedOperator = [];

	var map = L.map('map').setView([-6.207579, 106.86156], 12);

	var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	}).addTo(map);

	
	// control that shows state info on hover
	var info = L.control();

	info.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'info');
		this.update();
		return this._div;
	};

	info.update = function (props) {
		<?php
			if($mode == 'kecamatan'){
				?>
			this._div.innerHTML = '<h4>Peta Jakarta</h4>' +  (props ?
			'<b>' + props.KECAMATAN + '</b><br />'+ ' Wilayah '+ props.WILAYAH : 'Arahkan untuk detail');
				<?php
			}else{
				?>
			this._div.innerHTML = '<h4>Peta Jakarta</h4>' +  (props ?
			'<b>'+ ' Wilayah '+ props.WILAYAH : 'Arahkan untuk detail');
				<?php
			}
		?>
		
	};

	info.addTo(map);


	// get color depending on population density value
	function getColor(d) {

		return d == 'JAKARTA PUSAT' ? '#BB2F59' :
			d == 'JAKARTA BARAT'  ? '#1FBD00' :
			d == 'JAKARTA UTARA'  ? '#1E4DE9' :
			d == 'JAKARTA TIMUR'  ? '#2AD2FC' :
			d == 'JAKARTA SELATAN'  ? '#FEB24C' :
			d > 50   ? '#FD8D3C' :
			d > 20   ? '#FEB24C' :
			d > 10   ? '#FCFAF5' : '#A9A0FF';
	}

	function style(feature) {
		return {
			weight: 2,
			opacity: 1,
			color: 'white',
			dashArray: '3',
			fillOpacity: 0.6,
			fillColor: getColor(feature.properties.WILAYAH.toUpperCase())
		};
	}

	function style2(feature) {
		return {
			weight: 2,
			opacity: 1,
			color: 'white',
			dashArray: '3',
			fillOpacity: 0.6,
			fillColor: getColor(feature.properties.name)
		};
	}

	function highlightFeature(e) {
		var layer = e.target;

		layer.setStyle({
			weight: 5,
			color: '#666',
			dashArray: '',
			fillOpacity: 0.6
		});

		if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
			layer.bringToFront();
		}

		info.update(layer.feature.properties);
	}

	var geojson;

	function resetHighlight(e) {
		geojson.resetStyle(e.target);
		info.update();
	}

	function zoomToFeature(e) {
		map.fitBounds(e.target.getBounds());
	}

	var last_wilayah = "";

	function onEachFeature(feature, layer) {
		layer.on({
			mouseover: highlightFeature,
			mouseout: resetHighlight,
			click: zoomToFeature
		});

		if(last_wilayah != feature.properties.WILAYAH){
			$('#dropdown-isi').append("<label class='dropdown-head'>"+feature.properties.WILAYAH+
							"</label>");

			last_wilayah = feature.properties.WILAYAH;
		}

		<?php
			if($mode == 'kecamatan'){
				?>
				$('#dropdown-isi').append("<label class='dropdown-option'>"+
					"<input type='checkbox' class='check-in' name='dropdown-group' value='"+feature.properties.KECAMATAN+"' onchange='updateList(this)' checked/>"+feature.properties.KECAMATAN+
				"</label>");

				kotaSelected.push(feature.properties.KECAMATAN);
				<?php
			}else{
				?>
				$('#dropdown-isi').append("<label class='dropdown-option'>"+
					"<input type='checkbox' class='check-in' name='dropdown-group' value='"+feature.properties.WILAYAH+"' onchange='updateList(this)' checked/>"+feature.properties.WILAYAH+
				"</label>");

				kotaSelected.push(feature.properties.WILAYAH);
				<?php
			}
		?>

		
	}
	function onEachFeature2(feature, layer) {

		layer.on({
			mouseover: highlightFeature,
			mouseout: resetHighlight,
			click: zoomToFeature
		});
	}

	$(".check-in").change(function() {
		if(this.checked) {
			alert('anans');
		}
	});

	/* global statesData */
	geojson = L.geoJson(jakarta, {
		style: style,
		onEachFeature: onEachFeature
	}).addTo(map);

	layerSelected = kotaSelected;

	function updateList(d){

		selected = [];
		console.log(selected);
		
		$('.check-in:checked').each(function() {
			selected.push($(this).attr('value'));
		});

		console.log(selected);
		layerSelected = selected;

		map.eachLayer((layer) => {
			if(layer.feature){
				layer.remove();
			}
			if(layer.options.icon){
				layer.remove();
			}	
			//
		});


        var geojson = L.geoJson(jakarta, {
			filter: selectedFilter,
             style: style,
             onEachFeature: onEachFeature2
			
        }).addTo(map);

		loadOperator2();
	}

	function updateMap(){

		map.eachLayer((layer) => {
			if(layer.feature){
				layer.remove();
			}
			if(layer.options.icon){
				layer.remove();
			}	
			//
		});


		var geojson = L.geoJson(jakarta, {
			filter: selectedFilter,
			style: style,
			onEachFeature: onEachFeature2
			
		}).addTo(map);

		loadOperator2();
	}

	function updateListOperator(d){

		unSelectedOperator = [];

		$('.check-in-operator:not(:checked)').each(function() {
			unSelectedOperator.push($(this).attr('value'));
		});


		map.eachLayer((layer) => {
			if(layer.options.icon){
				layer.remove();
			}	
			//
		});

		updateLayerOperator2();
	}

	function selectedFilter(feature) {
		//alert('sdads');

		<?php
		if($mode == 'kecamatan'){
			?>
			if(selected.includes(feature.properties.KECAMATAN)){
				return true
			}
			<?php
		}else{
			?>
			if(selected.includes(feature.properties.WILAYAH)){
				return true
			}
			<?php
		}
		?>
		

	}

	/// Add operator , masih static saat ini
	var LeafIcon = L.Icon.extend({
		options: {
			iconSize:     [40, 40]
		}
	});

	var greenIcon = new LeafIcon({iconUrl: 'assets/img/map-marker.png'});

	// L.marker([-6.110756, 106.791833], {icon: greenIcon}).bindPopup('I am a green leaf.').addTo(map);
	// L.marker([-6.111756, 106.741833], {icon: greenIcon}).bindPopup('I am a green leaf.').addTo(map);
	// L.marker([-6.112756, 106.731833], {icon: greenIcon}).bindPopup('I am a green leaf.').addTo(map);

	loadOperator2();



	function updateLayerOperator2(){
		$.getJSON('operator.json?idx='+getRndInteger(1,9999), function(json) {
			operatorJson = json;


			console.log('operator :', operatorJson);

			$.each(operatorJson, function(key, value) {

				operators = value;

				console.log('nama kecamatan :', operators);

				var markers = [];

				var l = 0;

				for (let k = 0; k < operators.length; k++) {
					console.log('sdas :', operators[k]);

					var idOperator = operators[k].idOperator;
					var namaOperator = operators[k].nama;
					var deskripsiOperator = operators[k].deskripsi;
					var cordinateOperator = operators[k].coordinate;
					var kecamatan = operators[k].kecamatan;

					// if(layerSelected.includes(kecamatan)){
					
						if(!unSelectedOperator.includes(idOperator)){
							markers[l] = L.marker(cordinateOperator, {icon: greenIcon, draggable: 'true'}).bindPopup(namaOperator).addTo(map);

							markers[l].on("dragend", function(e) {

								var marker = e.target;
								var lat = marker.getLatLng().lat;
								var long = marker.getLatLng().lng;

								console.log('lat', lat);
								console.log('long', long);

								sendUpdate(operators[k].idOperator, lat, long);
							});



							l++;
							
						}
					// }
					
				}


			});
		});
	}

	// marker.on('dragend', function (e) {
	// 	document.getElementById('latitude').value = marker.getLatLng().lat;
	// 	document.getElementById('longitude').value = marker.getLatLng().lng;
	// });

	

	function getRndInteger(min, max) {
		return Math.floor(Math.random() * (max - min) ) + min;
	}

	function loadOperator2(){


		$.getJSON('operator.json?idx='+getRndInteger(1,9999), function(json) {
			operatorJson = json;


			$('#dropdown-isi-operator').html('');
			console.log('operator :', operatorJson);

			$.each(operatorJson, function(key, value) {

				operators = value;

				console.log('nama kecamatan :', operators);

				var markers = [];

				var l = 0;

				for (let k = 0; k < operators.length; k++) {


					var idOperator = operators[k].idOperator;
					var namaOperator = operators[k].nama;
					var deskripsiOperator = operators[k].deskripsi;
					var cordinateOperator = operators[k].coordinate;
					var kecamatan = operators[k].kecamatan;

					// if(layerSelected.includes(kecamatan)){
						
						
						$('#dropdown-isi-operator').append("<label class='dropdown-option'>"+
								"<input type='checkbox' class='check-in-operator' name='dropdown-group-2' value='"+idOperator+"' onchange='updateListOperator(this)' checked/>"+namaOperator+
							"</label>");

						if(!unSelectedOperator.includes(idOperator)){
							markers[l] = L.marker(cordinateOperator, {icon: greenIcon, draggable: 'true'}).bindPopup(namaOperator).addTo(map);

							markers[l].on("dragend", function(e) {

								var marker = e.target;
								var lat = marker.getLatLng().lat;
								var long = marker.getLatLng().lng;

								console.log('lat', lat);
								console.log('long', long);

								sendUpdate(operators[k].idOperator, lat, long);
							});



							l++;
							
						}
					// }
					
				}


			});
		});
	}

	function sendUpdate(id, lat, long){
		$.post('ajax/update_lokasi.php' ,{id : id, lat : lat, long : long},function(data){

			console.log('ajax update', data);

		});
	}


</script>

<script type="text/javascript" src="assets/js/core.js"></script>



</body>
</html>
