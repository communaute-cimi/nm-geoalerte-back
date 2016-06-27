#!/bin/bash

service postgresql start &&
apache2ctl -X &&
tail -f /var/log/apache2/*.log
