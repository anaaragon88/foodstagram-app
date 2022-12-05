
![](./public/img/logo.png)

# Una plataforma para subir las fotos de tu comida favorita
Esta es una propuesta de una aplicación donde debes registrarte, loggearte y después puedes subir fotografías de comida, su descripción, poder darle like y comentar las fotos de los demás miembros de la comunidad, editar y eliminar tus publicaciones.

## !🔌 Instala dependencias:
Debes tener instalado COMPOSER una vez hayas clonado el proyecto, posteriormente ejecutar el comando:

### `composer install`
Una vez ejecutado este comando se creará la carpeta vendor la cual contiene los bundles y códigos de terceros.

Estos comandos son para crear/actualizar la base de datos
### `php bin/console doctrine:database:create`
### `php bin/console doctrine:schema:update --force`

Ejecútalo con:
### `symfony server:start`
______ 

## Stack utilizado

- HTML    
- CSS
- JS
- SYMFONY 5.4
- PHP 8.1
- PHPUNIT
- XAMPP

______ 
# Vistas 
______ 
### Página principal

![](./public/img/pagina_principal.png)
______ 
### Perfil
![](./public/img/perfil.png)
______ 
### Ver Post
![](./public/img/ver_post.png)
______ 
### Crear Post
![](./public/img/crear_post.png)
______ 
### Editar 
![](./public/img/editar_post.png)
______ 

## Proceso de Desarrollo

Arquitectura: 
![](./public/img/arquitectura.png)
______ 

Entidades en base de datos:
![](./public/img/entidades.png)

______ 
# Buenas prácticas:
- Experiencia de Usuario: 
    - Se buscó que los botones tuvieran los colores y tamaños estandarizados (ej: rojo para eliminar y su tamaño más pequeño que el de editar)
    - Jerarquía en el tamaño de las tipografías dependiendo de su importancia
    - Uso de iconografía estandarizada
    - Similitudes con plataformas similares para que el proceso de aprendizaje fuera intuitivo
    - Se desarrollaron las vistas (wireframes) en baja calidad antes de llevarlos a código.

- DRY (Don't Repeat Yourself):
    - Se utilizó el método de symfony "{% extends 'base.html.twig' %}" para extender el navbar y background en toda la página web y así no repetir código innecesariamente.
    - Para el CSS se utilizó principalmente bootstrap.

- Lenguaje unificado:
    - En medida de lo posible, se evitó el uso de inglés y español en el naming de funciones, las variables, el nombre de los ficheros, atributos, etc.

- TDD. Se realizaron 2 test con phpunit:
    - "PublicPostTest" para comprobar el texto h1 en la ruta /registro
    - "TextLoginTest" para comprobar el texto dentro del botón LOG IN.

- Seguridad:
    - Se realizaron las respectivas restricciones de acceso de las rutas en el archivo "security.yaml" (Solo usuarios registrados y loggeados pueden acceder a la plataforma para interactuar con la comunidad)
    - La contraseña del usuario se guarda encriptada en la base de datos

- Arquitectura:
    Previamente al desarrollo del código se ideó la estructura de la arquitectura de la información a través de un organigrama

______ 
# S.O.L.I.D.
De estos principios, puedo identificar los siguientes en el proyecto:

- El principio de Responsabilidad Única nos viene a decir que un objeto debe realizar una única cosa. En el caso de este proyecto, el objeto "Comentarios" (que se refiere a los comentarios de los usuarios dentro de los posts) fue creado para no darle doble responsabilidad a la entidad "Post".

- El principio Open/Closed nos dice que una entidad de software debería estar abierta a extensión pero cerrada a modificación. En este proyecto se cumple este principio ya que la entidad "Post" fue extendida para agregarle la funcionalidad de los "Likes" y sin modificar el código anteriormente creado.



