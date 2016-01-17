# Projet Géo-Alerte - backoffice

## Présentation

Le projet Géo-Alerte propose une plateforme permettant :
- à une autorité (Préfecture, SDIS, Police, Gendarmerie...) de diffuser une alerte
- à tout acteur sur internet de savoir si une localisation est concernée par une alerte et si oui de connaître son emprise.


Une alerte est consituée :
- d'un émetteur
- d'un descriptif de l'alerte
- d'une consigne
- d'une emprise géographique


##L'API

C'est un backoffice qui propose une API permettant de 
- Pousser les évennement géolocalisés (autorités certifées)
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
docker run -p 8000:8000 -d geoalert /bin/bash -c "service postgresql start && apache2ctl -X"

# accéder à la VM (container_id = 3 premiers caractères de l'ID)
docker exec -it ${container_id} /bin/bash

```

##Pile technique

- OS : Unbuntu 15.10
- Langage : PHP + framework slim (pour l'API)
- BDD : PostgreSQL/PostGIS
- API : rest/json






