# Projet Géo-Alerte - backoffice

## Présentation du projet

Le projet Géo-Alerte propose une plateforme permettant :
- à une autorité (Préfecture, SDIS, Police, Gendarmerie...) de diffuser une alerte
- à tout acteur internet (réseaux sociaux, opérateurs, institutions...) de savoir si une localisation est concernée et d'en connaître l'emprise.

Les "clients" concernés :
- Les services de l'Etat
- Les opérateurs mobiles
- Les réseaux sociaux
- Les développeurs d'application mobiles ou de sites internet

Une alerte est consituée :
- d'un émetteur
- d'un descriptif de l'alerte
- d'une consigne
- d'une emprise géographique


##L'API

C'est un backoffice qui propose une API permettant de
- Pousser les évènements géolocalisés (autorités certifiées)
- Consommer les données (sites, applis mobiles, administrations...)

##Docker
Vous pouvez tester le projet avec docker (installation préalable nécessaire)
```shell
# Récupérer le dépôt
git clone https://github.com/communaute-cimi/nm-geoalerte-back

cd nm-geoalerte-back

# Créer l'image
docker build --rm -t geoalerte .

# Commande : bind port, lancement PG et Apache
docker run -p 8000:8000 -d geoalerte /bin/bash -c "service postgresql start && apache2ctl -X"

# accéder à la VM (container_id = 3 premiers caractères de l'ID)
docker exec -it ${container_id} /bin/bash

```

##Pile technique

- OS : Unbuntu 15.10
- Langage : PHP + framework slim (pour l'API)
- BDD : PostgreSQL/PostGIS
- API : rest/json

##Tests
Lister toutes les alertes : http://geoalerte.info/public/v1/alerts
Est-ce qu'une alerte existe sur un point ? http://geoalerte.info/public/v1/alerts/2.30697/48.90464

