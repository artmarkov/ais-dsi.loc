<?php
/**
 * Created by internetsite.com.ua
 * User: Tymofeiev Maksym
 * Date: 24.05.2016
 * Time: 12:56
 */

namespace common\widgets;


use yii\widgets\InputWidget;
use yii\bootstrap\Html;
use yii\base\Model;
use Yii;

class YandexGetCoordsWidget extends InputWidget
{
  public $model;
  
  public $coords_attribute = 'coords';
  public $zoom_attribute = 'map_zoom';
  public $address_attribute = 'map_address';
  public $center = '55.755776,37.617705';
  public $autoinit = true;
  public $zoom = 15;
  
  public function init(){
    return parent::init();
  }
   /**
   * @return bool whether this widget is associated with a data model.
   */
  protected function hasModel()
  {
    return $this->model instanceof Model && $this->attribute !== null;
  }
  /**
   * @return type
   */
  public function run(){
    $zoom_input = '';
      if ($this->hasModel()) {
          
            $coords_input = Html::activeInput('text', $this->model, $this->coords_attribute, ['class' => 'form-control','readonly'=> true]);
            $address_input = Html::activeTextarea($this->model, $this->address_attribute, ['class' => 'form-control','readonly'=> true]);

          if ($this->model->hasAttribute($this->zoom_attribute)) {
              $zoom_input = Html::activeInput('text', $this->model, $this->zoom_attribute, ['class' => 'form-control','readonly'=> true]);
          }
          if (!empty($this->model[$this->coords_attribute])) {
              $this->center = $this->model[$this->coords_attribute];
              $this->zoom = $this->model[$this->zoom_attribute];
          }
      }
      else {
          $coords_input = Html::input('text', $this->coords_attribute, '', ['class' => 'form-control','readonly'=> true]);
          $zoom_input = Html::input('text', $this->zoom_attribute, '', ['class' => 'form-control','readonly'=> true]);
          $address_input = Html::textarea($this->address_attribute, '', ['class' => 'form-control','readonly'=> true]);
      }
          $button_input = Html::a(Yii::t('art', 'Paste address to form'), ['#'], ['class' => 'btn btn-default insert-coords-form']);
        
        
      $this->view->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru', ['position' => 1, 'type' => 'text/javascript']);

      if ($this->autoinit) {
          $this->view->registerJs("ymaps.ready(init);");
      }
      
      $this->view->registerJs("
                    function init() {
                        var myPlacemark,
                            myMap = new ymaps.Map('map', {
                                center: [" . $this->center . "],
                                zoom: [" . $this->zoom . "]
                            }, {
                                searchControlProvider: 'yandex#search'
                            });

                        myPlacemark = createPlacemark([" . $this->center . "]);
                        getAddress(myPlacemark.geometry.getCoordinates());
                        myMap.geoObjects.add(myPlacemark);
                        //слушаем изменение зума
                        myMap.events.add('boundschange', function (e) {
                            var zoom = e.get('newZoom');
                                $('#" . Html::getInputId($this->model, $this->zoom_attribute) . "').val(zoom);
                        });
                        // Слушаем клик на карте
                        myMap.events.add('click', function (e) {
                            var coords = e.get('coords');
                            var zoom = myPlacemark.geometry._lastZoom;
                                $('#" . Html::getInputId($this->model, $this->coords_attribute) . "').val(coords);
                                $('#" . Html::getInputId($this->model, $this->zoom_attribute) . "').val(zoom);


                            // Если метка уже создана – просто передвигаем ее
                            if (myPlacemark) {
                                myPlacemark.geometry.setCoordinates(coords);

                            }
                            // Если нет – создаем.
                            else {
                                myPlacemark = createPlacemark(coords);
                                myMap.geoObjects.add(myPlacemark);
                                // Слушаем событие окончания перетаскивания на метке.
                                myPlacemark.events.add('dragend', function () {
                                    getAddress(myPlacemark.geometry.getCoordinates());


                                });
                            }
                            getAddress(coords);

                        });
                        //клик end
                          
                        myPlacemark.events.add('dragend', function (e) {
                            var coords = myPlacemark.geometry.getCoordinates();
                            var zoom = myPlacemark.geometry._lastZoom;
                                $('#" . Html::getInputId($this->model, $this->coords_attribute) . "').val(coords);
                                $('#" . Html::getInputId($this->model, $this->zoom_attribute) . "').val(zoom);
                            getAddress(coords);
                            });

                        // Создание метки
                        function createPlacemark(coords) {
                            return new ymaps.Placemark(coords, {
                                iconContent: ''
                            }, {
                                preset: 'islands#violetStretchyIcon',
                                draggable: true
                            });
                        }

                        // Определяем адрес по координатам (обратное геокодирование)
                        function getAddress(coords) {
                            myPlacemark.properties.set('iconContent', 'поиск...');
                            ymaps.geocode(coords).then(function (res) {
                                var firstGeoObject = res.geoObjects.get(0);
                                myPlacemark.properties
                                    .set({
                                        iconContent: firstGeoObject.properties.get('name'),
                                        balloonContent: firstGeoObject.properties.get('text')
                                        });
                                        var map_address = firstGeoObject.properties._data.text;
                                        $('#" . Html::getInputId($this->model, $this->address_attribute) . "').val(map_address);
                                    })                                   
                            }
                        }
      ");
                
      return '<div class="row">
                    <div class="col-xs-7 col-sm-8 col-md-9">
                        <div style="width: 100%; height: 400px;">
                        <div id="map" style="width: 100%; height: 400px;"></div>
                       </div>
                    </div>
                    <div class="col-xs-5 col-sm-4 col-md-3">
                             <label class="control-label">' . \Yii::t('art', 'The coordinates of the place') . '</label>
                                ' . $coords_input . '
                        <div class="help-block"></div>
                             <label class="control-label">' . \Yii::t('art', 'Map Zoom') . '</label>
                                ' . $zoom_input . '
                        <div class="help-block"></div>
                        <label class="control-label">' . \Yii::t('art', 'Map Address') . '</label>
                                ' . $address_input . '
                        <div class="help-block"></div>
                                ' . $button_input . '
                    </div>

             </div>';
  }

}

