
Instalar dependencias

```
composer install
```

lanzar los tests:

```
php vendor/phpunit/phpunit/phpunit
```

File System

Decidi Hacer este test con una estructura DDD.

Las entidades base son File y Folder como específicado en el test.
Estas dos solo tendran referencias de otras entidades y no podran contenerse la una a la otra.

FileSystem Hanldler es la capa que se encargará de coordinar el dominio.

El ejercicio solo habla de navegación. Para navegar a través de las carpetas primero
deben crearse. y es lo que emulo en los tests: la creación para luego navegar.
Para identificar a las entidades (Aggregates) uso una clase AggredateId que en teoría
tiene que generar un id único. Ya que cada fichero y carpeta es única. Aqui no tomo en
cuenta que solo un fichero tiene que ser único igualmente (generalmente los sistemas operan asi).

Para moverme a través de las carpetas utilizo un punteador (pointer) que se va "desplazando"
y cuando nos encontramos dentro de una carpeta y creamos un fichero, el fichero pertenecerá
a esa carpeta.

Testeo el borrado de ficheros y carpetas en cascada (uso recursividad).

No uso el layer memoria pero lo dejo ahi ya que es un pattern habitual y no se pedía en el
test.

Con este test no protendo cubrir todas las funcionalidades posibles sino mostrar que se
pueden ir implementado más.


