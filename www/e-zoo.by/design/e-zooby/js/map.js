ymaps.ready(init);

function init () {
var myMap = new ymaps.Map("map", {
center: [53.835005,27.527497],
zoom: 16
}),

// Создаем метку и задаем изображение для ее иконки
myPlacemark = new ymaps.Placemark([53.835005,27.527497], {
balloonContentHeader: 'wwww.e-zoo.by',
balloonContentBody: 'Санкт-Петербург Чапыгина д.8',
balloonContentFooter: '+7 (812) 0000000'
}, {
iconImageHref: 'https://e-zoo.by/design/e-zooby/images/icon-map.png', // картинка иконки
iconImageSize: [93, 73], // размеры картинки
iconImageOffset: [-78, -20] // смещение картинки
});
// Добавление метки на карту
myMap.geoObjects.add(myPlacemark);
}
