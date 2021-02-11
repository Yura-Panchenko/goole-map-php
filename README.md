Google Map Tool

#Данная утилита упрощает вывод простых Google-карт с маркерами на них.
В дополнительных плагинах не нуждается.
Рекомендуется применять в связке с плагином Advanced Custom Fields с полем типа Google Map.
##Возможности:

   - 4 режима отображения карты HYBRID, ROADMAP, SATELLITE, TERRAIN
   - можно задавать нестандартные иконки маркерам
   - можно выводить несколько маркеров на одной карте
   - можно выводить произвольное содержимое во всплывающих подсказках маркеров

Инструкция по применению:

Необходимо скопировать файл GMapTool.php в папку с темой и подключить его в functions.php:
```php
  include( get_template_directory() .'/constants.php' );
  include( get_template_directory() .'/classes.php' );
  include( get_template_directory() .'/widgets.php' );
  include( get_template_directory() .'/GMapTool.php' );
```
В файле шаблона где нужно вывести карту пишем код для получения координат маркера из плагина ACF, например:
```php
$location = get_field('location');  //acf google map field
$marker_title = get_field('marker_title'); //acf text field
$popup_content = get_field('popup_content'); //acf textarea field
$marker_image = get_field('marker_image'); //acf image, return value = image url
$styles = get_field('styles'); //acf textarea field
```
Создаем карту:
```php
$map = new GMapTool();
```
Добавляем маркер:
```php
$map->addMarker(array(
	'position' => $location,
	'title' => $marker_title ? $marker_title : null,
	'popup' => $popup_content ? $popup_content : null,
	'icon' => $marker_image ? $marker_image : null,
));
```
Выводим карту:
```php
$map->display(array(
	'id' => 'google-map',
	'center' => $location,
	'height' => '300px',
	'zoom' => 12,
	'type' => 'roadmap',
	'styles' => $styles ? $styles : null,
));
```
Если карта должна содержать только один маркер, можно упростить её вывод, задав параметры методов addMarker и display в конструкторе. В таком случае вывод карты потребует всего один вызов конструктора:

```php
new GMapTool(array(
	'id' => 'google-map',
	'height' => '300px',
	'position' => $location,
	'default_icon' => $marker_image,
	'title' => $marker_title ? $marker_title : null,
	'popup' => $popup_content ? $popup_content : null,
	'styles' => $styles ? $styles : null,
));
```
Как видно из последнего примера, в конструкторе можно совместить передачу параметров для добавления маркера и вывода карты, в этом случае функции addMarker и display сработают автоматически. Если параметр 'center' пропущен, он будет задан равным параметру 'position' маркера.

Рассмотрим подробнее возможные параметры каждого метода:
```php
public function addMarker($args = null)
```
Параметры:

    **position:
    Значение: array('lat' => lat_value, 'lng' => lng_value)
    Обязательный параметр.
    Именно в таком виде хранятся в поле ACF типа Google Map, т.е. в этот параметр можно передавать сразу значение поля ACF.
    **icon:
    Значение: URL к файлу изображения для маркера.
    Необязательный параметр.
    Если не задано – будет использован маркер по-умолчанию, заданный в параметре default_icon метода display().
    **popup:
    Значение: Текст окна подсказки открывающегося по клику на маркер. Может содержать любой HTML.
    Необязательный параметр.
    Если не задано – подсказка не открывается по клику на маркер.
    **title:
    Значение: Текст подсказки, который всплывает при наведении на маркер.
    Необязательный параметр.
    Если не задано – подсказка не появляется при наведении.
```php
public function display($args = null)
```
    **id:
    Значение: уникальный идентификатор, строка.
    Обязательный параметр.
    **center:
    Значение: array('lat' => lat_value, 'lng' => lng_value)
    Обязательный параметр.
    Именно в таком виде хранятся в поле ACF типа Google Map, т.е. в этот параметр можно передавать сразу значение поля ACF.
    **class:
    Значение: класс обвёртки карты.
    Необязательный параметр.
    Если не задан – используется класс “gmap-holder”.
    **width:
    Значение: ширина обвёртки у карты.
    Необязательный параметр.
    Если на задан – имеет значение по-умолчанию 100%.
    **height:
    Значение: высота обвёртки у карты.
    Необязательный параметр.
    Если на задан – имеет значение по-умолчанию 100%.
    **zoom:
    Значение: масштаб карты.
    Необязательный параметр.
    Если не задан – имеет значение по-умолчанию 14
    **type:
    Значение: 4 возможных варианта 'HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN'.
    Не чувствителен к регистру.
    Необязательный параметр.
    Если не задан – имеет значение по-умолчанию 'ROADMAP'.
    **wrapper:
    Значение: шаблон обвёртки карты, строка.
    Не обязательный параметр.
    Если не задан – имеет значение по-умолчанию
```php
    <div id="%1$s" class="%2$s" style="width: %3$s; height: %4$s;"></div>
```
    **default_icon:
    Значение: URL к файлу изображения для маркера по-умолчанию.
    Необязательный параметр.
    Если не задано – по-умолчанию будет использован стандартный маркер.
    **styles:
    Значение: массив стилей для карты.
    Необязательный параметр.
    Если не задано – карта будет иметь стандартный вид.
    Может быть получен с помощью онлайн-утилиты google (http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html)
    Наборы готовых стилей можно найти на сайте (http://snazzymaps.com)
##В theme-function.php добавляем код 
```php
add_action('acf/init', function() {
    acf_update_setting( 'google_api_key', get_field( 'gm_api_key', 'options' ) );
});
```
'gm_api_key' - поле для API-key

##В том месте где хотим вывести карту, добавляем такой код и редактируем под свои нужды
```php
<?php
$location = get_field('location', 'options');  //acf google map field
$marker_title = get_field('marker_title', 'options'); //acf text field
$popup_content = get_field('popup_content', 'options'); //acf textarea field
$marker_image = get_field('marker_image', 'options'); //acf image, return value = image url
$styles = get_field('styles', 'options'); //acf textarea field
?>

<?php
$map = new GMapTool();
$map->addMarker(array(
    'position' => $location,
    'title' => $marker_title ? $marker_title : null,
    'popup' => $popup_content ? $popup_content : null,
    'icon' => $marker_image ? $marker_image : null,
));
?>

<?php
$map->display(array(
    'id' => 'google-map-contact',
    'center' => $location,
    'class'  => 'gmap-holder',
    'zoom' => 12,
    'type' => 'roadmap',
    'styles' => '[
    {
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#e9e9e9"
            },
            {
                "lightness": 17
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#f5f5f5"
            },
            {
                "lightness": 20
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 17
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 29
            },
            {
                "weight": 0.2
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 18
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 16
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#f5f5f5"
            },
            {
                "lightness": 21
            }
        ]
    },
    {
        "featureType": "poi.park",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#dedede"
            },
            {
                "lightness": 21
            }
        ]
    },
    {
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "color": "#ffffff"
            },
            {
                "lightness": 16
            }
        ]
    },
    {
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "saturation": 36
            },
            {
                "color": "#333333"
            },
            {
                "lightness": 40
            }
        ]
    },
    {
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#f2f2f2"
            },
            {
                "lightness": 19
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#fefefe"
            },
            {
                "lightness": 20
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#fefefe"
            },
            {
                "lightness": 17
            },
            {
                "weight": 1.2
            }
        ]
    }
]',
));
?>

<?php
new GMapTool(array(
    'id' => 'google-map',
    'height' => '300px',
    'position' => $location,
    'default_icon' => $marker_image,
    'title' => $marker_title ? $marker_title : null,
    'popup' => $popup_content ? $popup_content : null,
    'styles' => $styles ? $styles : null,
));
?>
```
## Чтобы разместить несколько карт на странице - в выводе карт меняем id  
```php
new GMapTool(array(
    'id' => 'google-map',
));
```
