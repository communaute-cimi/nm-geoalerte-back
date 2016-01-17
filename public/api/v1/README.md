Description de l'API
===

Objet Alert
---

* **message**         Description courte de l'alerte
* **long_message**    Les consignes à suivre
* **category**        Le type d'alerte
* **source**          L'émetteur de l'alerte
* **url**             Un lien à suivre pour avoir plus d'informations sur l'alerte
* **dthr**            Date et heure de l'émission de l'alerte
* **geom**            Le polygône englobant l'alerte au format GeoJSON

Requêtes accessibles à tous
---

### Lister toutes les alertes en cours

GET /v1/alerts

  RETURN Array<Alert> La liste des alertes en cours sur tout le territoire

### Récupérer les alertes concernant l'utilisateur

GET /v1/alerts/{lat}/{lng}

  PARAM float lat     La latitude GPS de l'utilisateur

  PARAM float lng     La longitude GPS de l'utilisateur

  RETURN Array<Alert> La liste des alertes en cours qui concerne l'utilisateur

Requêtes accessibles aux autorités
---

### Ajouter une alerte

INSERT /v1/alert

  POST Alert          Un objet GeoJSON de l'alerte

  RETURN int          L'id de l'alerte insérée

### Modifier une alerte

PUT /v1/alerts/{id}

  PARAM int id        L'id de l'alerte à modifier

  POST Alert          Un objet GeoJSON de l'alerte contenant les champs à modifier

  RETURN bool         Le résultat de la requête de modification

### Supprimer une alerte

DELETE /v1/alerts/{id}

  PARAM int id        L'id de l'alerte à supprimer

  RETURN bool         Le résultat de la requête de suppression
