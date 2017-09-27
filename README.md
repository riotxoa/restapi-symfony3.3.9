# restapi-symfony3.3.9
Simple REST API developed with Symfony 3.3.9 (PHP)

# Instalación

## Requisitos previos
* [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).
* PHP
* MySQL

## Proceso de instalación
Desde su línea de comandos, sitúese en la ruta o directorio donde desee iniciar la instalación y siga los siguientes pasos:
1. Clonar el repositorio donde se encuentra el código:

    * git clone https://github.com/riotxoa/restapi-symfony3.3.9.git

2. Situarse en la nueva ruta creada:

    * cd restapi-symfony3.3.9

3. Instalar dependencias:

    * composer install

    _Habrá un momento donde el proceso le pedirá los datos de acceso a servidor MySQL: rellenar con los valores correspondientes a su sistema._
    _También le pedirá el nombre de la base de datos que desea crear: elija cualquier nombre (p.ej. basicrestapi)._

4. Crear la base de datos:

    * php bin/console doctrine:database:create

5. Crear la tabla:

    * php bin/console doctrine:schema:update --force

# Ejecución de la API

Desde su línea de comandos, sitúese en la ruta o directorio donde ha clonado el código del repositorio (restapi-symfony3.3.9) y ejecutar el siguiente comando:

* php bin/console server:run
