FROM ubuntu:15.10
RUN apt-get update
RUN apt-get install -y php5
RUN apt-get install -y postgresql-9.4
RUN apt-get install -y postgis
RUN apt-get install -y php5-pgsql
# Setup DB
RUN service postgresql start && su postgres -c "psql -c \"CREATE EXTENSION postgis\""
RUN service postgresql start && su postgres -c "psql -c \"ALTER USER Postgres WITH PASSWORD 'necmergitur'\""
# Config
RUN a2enmod rewrite
ADD deploy/geoalerte.conf /etc/apache2/sites-available/
RUN a2ensite geoalerte
# App
RUN mkdir /app
ADD . /app/
RUN cd /app && ./composer.phar install
# DB Schema
RUN service postgresql start && su postgres -c "psql -f /app/create_database.sql"
