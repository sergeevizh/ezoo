{* Вкладки *}
{capture name=tabs}
{if in_array('orders', $manager->permissions)}
<li><a href="index.php?module=OrdersAdmin&status=0">Новые</a></li>
<li><a href="index.php?module=OrdersAdmin&status=1">Приняты</a></li>
<li><a href="index.php?module=OrdersAdmin&status=2">Выполнены</a></li>
<li><a href="index.php?module=OrdersAdmin&status=3">Удалены</a></li>
{if $keyword}
<li class="active"><a href="{url module=OrdersAdmin keyword=$keyword id=null label=null}">Поиск</a></li>
{/if}
{/if}
{if in_array('labels', $manager->permissions)}
<li><a href="{url module=OrdersLabelsAdmin keyword=null id=null page=null label=null}">Метки</a></li>
{/if}
{if in_array('couriers', $manager->permissions)}
<li><a href="{url module=OrdersCouriersAdmin keyword=null id=null page=null label=null}">Курьеры</a></li>
{/if}
{if in_array('delivery_area', $manager->permissions)}
<li class="active"><a href="{url module=OrdersDeliveryArea keyword=null id=null page=null label=null}">Области
		доставки</a></li>
{/if}
{/capture}

{*<script src="https://api-maps.yandex.ru/2.1/?apikey=06121448-377d-4258-b280-81672bfc97b6&lang=ru_RU"
		type="text/javascript">
</script>*}
<script type="text/javascript" src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=60445215-6d3a-4f88-87fe-8d52b72e5bc9"></script>

<script>
    var delivery_count = {$delivery_areas|@count};
    var areas = {$polygons};
</script>
{literal}
<script type="text/javascript">

    ymaps.ready(function () {
        var myPolygon,
            isDrawing = false;

        var map = new ymaps.Map("map", {
            center: [53.902284, 27.561831],
            zoom: 10,
            behaviors: ["default", "scrollZoom"],
            type: 'yandex#map',
            controls: []
        });

        document.addEventListener('click', function (event) {
            switch (event.target.id) {
                case "startDrawing" :
                    createPolygon();
                    break;
                case "stopDrawing":
                    stopPolygon();
                    break;
            }
        });

        renderMapControls();

        var BalloonContentLayout = ymaps.templateLayoutFactory.createClass(
            '<b>{% if properties.id %}Номер области: {{ properties.id }}{% endif %}</b><br>' +
            '<b>{% if properties.courier %}Курьер: {{ properties.courier }}{% else %}Курьер не выбран{% endif %}</b><br><br>' +
            '<button class="delete-polygon"> Удалить область </button>', {
                build: function () {
                    BalloonContentLayout.superclass.build.call(this);
                    $('.delete-polygon').bind('click', this.onButtonClick);
                },
                clear: function () {
                    $('.delete-polygon').unbind('click', this.onButtonClick);
                    BalloonContentLayout.superclass.clear.call(this);
                },
                onButtonClick: function () {

                    var areas = ymaps.geoQuery(map.geoObjects);
                    areas.each(function (item) {
                        if (item.balloon.isOpen()) {
                            ymaps.geoQuery(item).removeFromMap(map);
						}
                    })

                }
            });

        function createPolygon() {

            isDrawing = true;
            myPolygon = new ymaps.Polygon([], {}, {
                editorDrawingCursor: "crosshair",
                balloonContentLayout: BalloonContentLayout,
                balloonPanelMaxMapArea: 0,
                draggable: true
            });
            map.geoObjects.add(myPolygon);

            myPolygon.editor.startDrawing();
            myPolygon.editor.events.add("drawingstop", function () {
                isDrawing = false;
                renderMapControls();
            });
            renderMapControls();
        }

        drawExistingAreas();

        function drawExistingAreas() {
            for (let area of areas) {
                var polygonGeometry = new ymaps.geometry.Polygon([JSON.parse(area.polygon)[0]]),
                    polygonGeoObject = new ymaps.GeoObject({geometry: polygonGeometry, properties: {courier: area.courier ,id: area.id}}, {
                        draggable: isDrawing,
                        balloonContentLayout: BalloonContentLayout,
                        balloonPanelMaxMapArea: 0
                    });
                map.geoObjects.add(polygonGeoObject);
                polygonGeoObject.editor.startEditing();
            }
        }

        function stopPolygon() {
            myPolygon.editor.stopDrawing();
        }

        function renderMapControls() {
            var startDrawingButton = '<button id="startDrawing">Начать редактирование</button>',
                controlsContainer = document.getElementById('map-controls'),
                stopDrawingButton = '<button id="stopDrawing">Завершить редактирование</button>';

            controlsContainer.innerHTML = isDrawing ? stopDrawingButton : startDrawingButton;
        }

        $('#save-drawing').click(function () {
            $('.polygon_input').remove();
            delivery_count = 0;
            var delivery_areas = ymaps.geoQuery(map.geoObjects);
            delivery_areas.each(function (item) {
                if (item.geometry.getCoordinates()[0].length > 0) {
                    delivery_count++;
                    var id = item.properties.get('id') ? item.properties.get('id') : '';
                    var html = '<input type="hidden" name=polygons[' + delivery_count + '][area] value=' + JSON.stringify(item.geometry.getCoordinates()) + '>';
                    html += '<input type="hidden" name=polygons[' + delivery_count + '][id] value=' + id + '>';
                    $('#delivery_area').append(html);
				}
            });
        });
    });
</script>
{/literal}
<div id="header">
	<h1>Области доставки</h1>
</div>

<h2>Редактирование областей доставки</h2>
<form method=post enctype="multipart/form-data" id=delivery_area>
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<input class="button_green button_save" id="save-drawing" type="submit" value="Сохранить области доставки"/>
</form>


<div id="map-controls">
</div>
<div id="map" style="width: 100%; height: 650px;margin-top: 12px;"></div>
<hr>
{if !$delivery_areas}
<h2>На карте не выделены области</h2>
{else}
<h2>Выбор курьера для области доставки</h2>
<form method=post enctype="multipart/form-data">
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<table id="couriers-polygons">
		<thead>
		<td><b>Номер области</b></td>
		<td><b>Курьер</b></td>
		</thead>
		<tbody>
		{foreach $delivery_areas as $delivery_area}
		<tr>
			<td>{$delivery_area->id}</td>
			<td>
				{if $couriers}
				<select name="couriers[{$delivery_area->id}]">
					<option value="" {if $delivery_area->courier == ''} selected {/if} >--Выберите курьера--</option>
					{foreach $couriers as $courier}
					<option value="{$courier->id}" {if $courier->id == $delivery_area->courier_id} selected {/if} >{$courier->name}</option>
					{/foreach}
				</select>
				{/if}
			</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
	<input class="button_green button_save" type="submit" value="Сохранить"/>
</form>
{/if}





