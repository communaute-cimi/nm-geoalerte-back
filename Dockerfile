FROM ubuntu:15.10

RUN apt-get update && apt-get install -y \
  php5 \
  postgresql-9.4 \
  postgis \
  php5-pgsql \
  git \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/*

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

ADD deploy/docker-start.sh /usr/local/bin/
RUN chmod a+rx /usr/local/bin/docker-start.sh

CMD ["/usr/local/bin/docker-start.sh"]

EXPOSE 8000
