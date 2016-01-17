//Variable globale:
var URL_API = "http://geoalerte.info/public/v1/alerts";
var ALERTE_LYR_TITLE = "ALERTES"
var feature;
var draw;
var modify;
var select;

//Configuration de la MAP
//Couche MapQuest
var raster = new ol.layer.Tile({
  source: new ol.source.MapQuest({
    layer: 'sat'
  })
});

var source = new ol.source.Vector({
  wrapX: false
});

var vector = new ol.layer.Vector({
  source: source,
  style: new ol.style.Style({
    fill: new ol.style.Fill({
      color: 'rgba(255, 255, 255, 0.2)'
    }),
    stroke: new ol.style.Stroke({
      color: '#ffcc33',
      width: 2
    }),
    image: new ol.style.Circle({
      radius: 7,
      fill: new ol.style.Fill({
        color: '#ffcc33'
      })
    })
  })
});

//couche OSM
var osm = new ol.layer.Tile({
  source: new ol.source.OSM({
    attributions: [
      new ol.Attribution({
        html: 'Tiles &copy; <a href="http://www.opencyclemap.org/">' +
          'OpenCycleMap</a>'
      }),
      ol.source.OSM.ATTRIBUTION
    ],
    url: 'http://{a-c}.tile.openstreetmap.org/{z}/{x}/{y}.png'
  })
});

//création de la map
var map = new ol.Map({
  layers: [osm, vector],
  target: 'map',
  view: new ol.View({
    center: [257420.802007, 6224481.304326],
    zoom: 10,
    wkid: 3857
  })
});

//loadLayerBouchon();
loadLayer();



//Configuration de l'outil de saisie
var polygonTool = document.getElementById('polygonTool');
var polygonModifyTool = document.getElementById('polygonModifyTool');
var cancelButton = document.getElementById('cancelBtn');


/**
 * Ajout d'un outil de dessin à la map
 */
function addDrawInteraction(interactionType) {
  if (interactionType !== 'None') {
    var geometryFunction, maxPoints;
    var value = interactionType;

    draw = new ol.interaction.Draw({
      source: source,
      type: /** @type {ol.geom.GeometryType} */ (value)
    });

    //Event déclenché en fin de dessin
    draw.on('drawend', function(e) {
      //Initialisation de la variable globale feature
      feature = e.feature;

      //affichage du panel d'attributs
      addPanelRenseignement();

      //suppression de l'interaction
      removeDrawInteraction(draw);
    });
    map.addInteraction(draw);
  }
}

function addModifyInteraction() {
  var alerteLyr;
  var mapLayers = map.getLayers();
  mapLayers.forEach(function(element) {
    if (element.get('title') == ALERTE_LYR_TITLE) {
      alerteLyr = element;
    }
  }, this);

  select = new ol.interaction.Select({
    wrapX: false
  });

  modify = new ol.interaction.Modify({
      features: select.getFeatures(),
      // the SHIFT key must be pressed to delete vertices, so
      // that new vertices can be drawn at the same position
      // of existing vertices
      deleteCondition: function(event) {
        return ol.events.condition.shiftKeyOnly(event) &&
          ol.events.condition.singleClick(event);
        }
      });

    select.on('select', function(event) {
      console.log("select");
      if (event.selected.length > 0) {
        addPanelRenseignement();
        var feature = event.selected[0];
        addAttributesToPanel(feature);
      }
    });

    map.addInteraction(select);
    map.addInteraction(modify); 

  }

  function addAttributesToPanel(feature) {
    $('#categoryTxt').val(feature.get('category'));
    $('#msgShortTxt').val(feature.get('message'));
    $('#msgLongTxt').val(feature.get('long_message'));
    $('#urlTxt').val(feature.get('url'));
    $('#sourceTxt').val(feature.get('emetteur'));
  }

  function removeModifyInteraction() {
    map.removeInteraction(modify);
    map.removeInteraction(select);
  }

  /**
   * Suppression d'un outil de dessin de la map
   */
  function removeDrawInteraction(interactionType) {
    if (interactionType !== 'None') {
      map.removeInteraction(interactionType);
    }
  }

  /**
   * Affichage du formulaire d'attributs
   */
  function addPanelRenseignement() {
    $('#panelProperties').removeClass('hidden');
    $('#saveBtn').on('click', this, function() {
      saveFeature();
    });
  }

  /**
   * Sauvegarde de la feature
   */
  function saveFeature() {
    //Ajout des attributs à la feature
    var category = $('#categoryTxt').val();
    var msgShort = $('#msgShortTxt').val();
    var msgLong = $('#msgLongTxt').val();
    var url = $('#urlTxt').val();
    var source = $('#sourceTxt').val();

    feature.set('category', category);
    feature.set('message', msgShort);
    feature.set('long_message', msgLong);
    feature.set('url', url);
    feature.set('source', source);

    //Transformation de la feature en geoJSON
    var featureAsGeoJSON = getFeatureAsGeoJSON(feature);
    console.log(featureAsGeoJSON);
    postFeature(postFeatureCallback, this, featureAsGeoJSON);
  }

  /**
   * Transformation d'une feature en feature
   * au format geoJSON
   */
  function getFeatureAsGeoJSON(feature) {
    var geoJSON = new ol.format.GeoJSON();
    return geoJSON.writeFeature(feature, {
      dataProjection: 'EPSG:4326',
      featureProjection: 'EPSG:3857'
    });
  }

  /**
   * Appel AJAX POST de la feature
   */
  function postFeature(callback, scope, params) {
    console.log('savefeature');
    $.ajax({
      type: 'POST',
      dataType: 'json',
      contentType: 'application/json; charset=utf-8',
      url: URL_API,
      data: params,
      timeout: 10000,
      success: function(data) {
        callback.call(scope, data);
      },
      statusCode: {
        403: function() {
          alert('Opération interdite');
        },
        404: function() {
          alert('Ressource introuvable');
        },
        500: function(data) {
          alert('erreur serveur interne');
        }
      },
      error: function(err) {
        console.log(err);
      }
    });
  };

  /**
   * Callback du POST
   */
  function postFeatureCallback(data) {
    console.log(data);
    $('#panelProperties').addClass('hidden');
    refreshLayer();
  };

  /**
   * Activation de l'outil polygon
   * @param {Event} e Change event.
   */
  polygonTool.onclick = function(e) {
    map.removeInteraction(draw);
    removeModifyInteraction();
    addDrawInteraction('Polygon');
  };

  /**
   * Activation de l'outil polygon
   * @param {Event} e Change event.
   */
  polygonModifyTool.onclick = function(e) {
    map.removeInteraction(draw);
    addModifyInteraction();
  };

/**
   * Activation de l'outil polygon
   * @param {Event} e Change event.
   */
  cancelButton.onclick = function(e) {
    $('#panelProperties').addClass('hidden');
  };


  //chargement des couches externes
  function loadLayerBouchon() {
    var data = '[{"id":2,"message":"Projet 42 en cours, DO NOT DISTURB !!!","geom":{"type":"Polygon","coordinates":[[[2.2219848632812,48.818167793351],[2.2219848632812,48.775650278076],[2.3524475097656,48.775650278076],[2.35107421875,48.808220115534],[2.2219848632812,48.818167793351]]]},"long_message":"Ceci est une alerte de ecole 42.","url":"http:\/\/www.paris.fr\/necmergitur","category":"Hackathon"}]';
    data = JSON.parse(data);
    loadLayerCallBack(data);
  }

  /**
   * Chargement de la layer
   */
  function loadLayer() {
    getJSON(this.loadLayerCallBack, this, URL_API);
  }

  /**
   * Refresh de la layer
   */
  function refreshLayer() {
    console.log('refreshLayer');
    getJSON(refreshLayerCallBack, this, URL_API);
  }

  /**
   * Récupération des zones alertes depuis API GEO-ALERTE
   */
  function getJSON(callback, scope, url, params) {

    $.ajax({
      type: 'GET',
      dataType: 'json',
      url: url,
      data: params,
      timeout: 10000,
      success: function(data) {
        callback.call(scope, data);
      },
      statusCode: {
        403: function() {
          alert('Opération interdite');
        },
        404: function() {
          alert('Ressource introuvable');
        },
        500: function() {
          alert('Erreur interne');
        }
      },
      error: function(err) {
        console.log(err);
      }
    });
  };

  /**
   * Callback du GET alertes
   */
  function loadLayerCallBack(data) {
    var vectorSource = this.getVectorSource(data);
    var style = new ol.style.Style({
            image: new ol.style.Circle({
                fill: new ol.style.Fill({
                    color: 'rgba(255,0,0,0.5)'
                }),
                stroke: new ol.style.Stroke({
                    color: 'rgba(255,0,0,0.5)',
                    width: 1.25
                }),
                radius: 5
            }),
            fill: new ol.style.Fill({
                color: 'rgba(255,0,0,0.5)'
            }),
            stroke: new ol.style.Stroke({
                color: 'rgba(255,0,0,0.5)',
                width: 2.5
            })
        });

    var layer = new ol.layer.Vector({
      source: vectorSource,
      style: style,
      title: ALERTE_LYR_TITLE
    });


    map.addLayer(layer);
  };

  /**
   * Callback du refreshlayer
   */
  function refreshLayerCallBack(data) {
    var mapLayers = map.getLayers();
    mapLayers.forEach(function(element) {
      if (element.get('title') == ALERTE_LYR_TITLE) {
        var vectorSource = this.getVectorSource(data);
        element.setSource(vectorSource);
      }
    }, this);
  };

  /**
   * Renvoi la source de la layer
   */
  function getVectorSource(data) {
    var features = [];

    for (var idx in data) {
      var object = JSON.parse(data[idx].geom);
      if (object != null && object.coordinates != null) {
        var feature = new ol.Feature({
          geometry: new ol.geom.Polygon(object.coordinates).transform('EPSG:4326', 'EPSG:3857'),
          name: data[idx].id,
          emetteur: data[idx].emetteur,
          message : data[idx].message,
          long_message: data[idx].long_message,
          category : data[idx].category,
          url : data[idx].url
        });
        features.push(feature);
      }
    }

    var vectorSource = new ol.source.Vector({
      features: features
    });

    return vectorSource;
  };