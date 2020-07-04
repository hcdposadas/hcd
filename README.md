Sistema Interno del Honorable Concejo Deliberante de la Ciudad de Posadas
===

A Symfony project created on April 3, 2017, 4:21 pm.

Prerequisitos
==

- apache2/nginx
- postgres
- php7.4+
- composer
- node 10
- redis
- pm2

Parámetros:
=

Se debe configurar el archivo `.env.local` con los parámetros correspondientes a cada organismo

Para la sesión:
=

para compilar los js:

`$ npm run build`

para correr el server node que se conecta a los sockets y a redis

`$ npm run start`

