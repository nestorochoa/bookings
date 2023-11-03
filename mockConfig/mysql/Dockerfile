FROM mariadb:10.6.4-focal

ENV MARIADB_ROOT_PASSWORD=password
ENV MYSQL_DATABASE=smartfol_book
ENV MARIADB_USER=user
ENV MARIADB_PASSWORD=password
RUN	mkdir /usr/sql
RUN	chmod 644 /usr/sql

ADD ["data/baseData.sql", "/docker-entrypoint-initdb.d/schema.sql"]
