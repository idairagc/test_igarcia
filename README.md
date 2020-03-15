/ test_igarcia
 test idaira garcia

Para desarrollar este proyecto voy a utilizar:
SO: Windows 10
Framework: Laravel 6
Tecnología: PHP 7.2
Herramienta de desarrollo: Laragon
Herramienta para gestionar las apis: Postman

EMPECEMOS:

GESTIÓN DE RESPUESTAS

Me he creado un controlador, para gestionar las respuestas: 
\app\Http\Controllers\API\ResponseController.php
Este contiene 2 métodos:

sendResponse, devuelve el mensaje Json cuando la operación tiene éxito
Respuesta: 
	200 OK

sendError, devuelve el mensaje Json cuando hay algún error
Respuestas: (Si no me equivoco solo utilizo estos)
    401 Unauthorized  
    404 Not Found 
    422 Unprocessable Entity 

NOTAS: este controlador extiende del Controller y el resto de controladores que creemos extenderán de este.

GESTIÓN DE LA AUTENTIFICACIÓN

Para la autentificación he utilizado el paquete de Laravel Passport, este sigue el protocolo OAuth 2.
Entonces, todas las autentificaciones del proyecto tirarán por aquí.

Configuracion del paquete passport
composer require laravel/passport
php artisan migrate
php artisan passport:install 

Para empezar a trabajar con este proyecto, me he creado un seeder:
\database\seeds\AdminSeeder.php
con esto crearemos el admin y los roles (detallaremos esto más adelante)

Por otro lado me he creado un controlador para el login:
\app\Http\Controllers\API\LoginController.php

NOTA: Este devuelve el tokken que después tenemos que usar para ejecutar cualquier operación en Postman

GESTION DE USUARIOS

Creamos la tabla en bbdd: 
\database\migrations
users Table

id integer autoincremental
name string not null
surmane string not null
email string not null, unique
email_verified_at nullable (viene por defecto con laravel)
password string not null
rememberToken (para la autentificacion, también viene por defecto con laravel)
created_at -> fecha de alta
uddated_at -> fecha de la último modificación

Creamos el modelo: \app\User.php
Creamos el controllador: \app\Http\Controllers\API\UserController.php
	Este tendrá los siguientes métodos
		index 		 | lista los usuarios
		store 		 | crea un usuario
		show 		 | muestra los datos de un usuario
		update 		 | modifica los datos de un usuario
		destroy 	 | elimina un usuario
		changeRole | cambia el role del usuario

NOTA: Vamos a omitir las operaciones create y edit, ya que en api no tienen sentido.

Creamos un Resourse: \app\Http\Resources\User.php
Este facilita la interpretación de los datos para mostrarlos en las respuestas APIs.
NOTA: al mostrar los datos del usuario, he decidido que no muestre la contraseña.

GESTIÓN DE CLIENTES

Creamos la tabla en bbdd
\database\migrations
costumers Table

id integer autoincremental
name string not null
surmane string not null
email string not null, unique
photo string null able
user_id_created -> id del usuario que lo dio de alta
user_id_updated -> id del último usuario que lo modificó
created_at -> fecha de alta
uddated_at -> fecha de la último modificación

Creamos el modelo: \app\Customer.php
Creamos el controllador: \app\Http\Controllers\API\CustomerController.php
	Este tendrá los siguientes métodos
		index 		 | lista los clientes
		store 		 | crea un cliente
		show 		 | muestra los datos de un cliente
		update 		 | modifica los datos de un cliente
		destroy 	 | elimina un cliente

NOTA: Vamos a omitir las operaciones create y edit, ya que en api no tienen sentido.

Creamos un Resourse: \app\Http\Resources\Customer.php
Este facilita la interpretación de los datos para mostrarlos en las respuestas APIs.


GESTIÓN DE IMAGENES 

El cliente tiene un campo photo, como puede que en un futuro otro modelo utilice campos de este tipo he decidido crearme un trait para gestionarlos.
\app\Traits\ImageTrait.php

Este Trait contiene dos metodos:
uploadImage		| guarda la imagen en la ruta \public\images  
destroyImage	| elimina la imagen de la ruta \public\images 

uploadImage lo usaremos en los métodos customer.store y customer.update
destroyImage lo usaremos en los métodos customer.update y customer.destroy

A tener en cuenta:
- En bbdd se guardará sólo el nombre de la imagen, que será time()."nombre_original"
- Al mostrar este campo en las respuestas API, si que se mostrarán con la url.

GESTIÓN DE VALIDACIONES

Para las validaciones, laravel tiene la clase Validator, así que la voy a usar con el añadido que me he creado un Trait por modelo con las reglas de validación para los métodos store y update.

NOTA: Esta parte quería hacerla creando archivos Requests, pero siendo sincera... no terminé de entender como hacerlos para API, así que opté por esta solución que no sé si será la mejor, pero si que mejora la legibilidad y simplicidad del código.

Validaciones de Users: \app\Traits\UserValidationRulesTrait.php

Para el método store y register: 
StoreValidationRules
	'name'       => 'required|alpha|max:20',
    'surname'    => 'required|alpha|max:20',
    'email'      => 'required|email|unique:users|max:100',
    'password'   => 'required|min:8|max:20|alpha_num',
    'c_password' => 'required|same:password',

Para el método update: 
UpdateValidationRules
	'name'       => 'required|alpha|max:20',
    'surname'    => 'required|alpha|max:20',
    'email'      => 'required|email|max:100|unique:users,email,'.$id,

Para el método changeRole:
ChangeRoleValidationRules
	'role' => 'required',

NOTA: al modificar el usuario, no damos la opción de modificar la contraseña, ya que considero que esta parte debería hacerse en otro método a parte. 

Validaciones de Customer: \app\Traits\CustomerValidationRulesTrait.php
Para el método store: 
StoreValidationRules
	'name'       => 'required|alpha|max:20',
	'surname'    => 'required|alpha|max:20',
	'email'      => 'required|email|unique:customers|max:100',
	'photo'      => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

Para el método update:
UpdateValidationRules
    'name'       => 'required|alpha|max:20',
    'surname'    => 'required|alpha|max:20',
    'email'      => 'required|email|max:100|unique:customers,email,'.$id,
    'photo'      => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'


GESTIÓN DE ROLES Y PERMISOS

Para esta parte he utilizado el paquete de Laravel spatie/laravel-permission

Configuración del paquete

composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
php artisan migrate

Este paquete es fantástico, ya que te incluye las siguiente tablas de bbdd

roles 					| contiene los roles existentes
permissions 			| contiene los permisos existentes
role_has_permissions 	| relación de roles con permisos
model_has_roles 		| relación de usuarios por modelo con roles
model_has_permissions 	| relación de usuarios por modelo con permisos

Y toda la gestión de las mismas, de manera que sólo te tienes que preocupar de crear roles, permisos y asignar los mismos a usuarios. 
Como se puede ver, este paquete nos abre un gran abanico de posibilidades.
Para este proyecto, como sólo vamos a tener dos roles, únicamente voy a usar las tablas roles y model_has_roles, pero está todo listo para que en un futuro se pueda usar en su totalidad.

Los roles que vamos a tener son: 
	Admin con id 1
	Employee con id 2
NOTA: estos los creará automáticamente el seeder.

VISTAS A FUTURO
Como se puede ver al usar MVC no habría problema en crear un Proveedor
Del mismo modo que lo hicimos para el cliente, sólo tendríamos que crear el modelo, el controllador, la tabla de bbdd, etc.

LISTA DE RUTAS:

php artisan route:list

------------+--------------------------+-------------------+--------------------------------+---------------------------------
| Method    | URI                      | Name              | Action 			               | Middleware                   +-----------+--------------------------+-------------------+-----------------+--------------+---------------------------------
| GET|HEAD  | /                        |                   | Closure         	           | web                      
| POST      | api/customers            | customers.store   | \API\CustomerController@store  | api,auth:api,role:Admin|Employee
| GET|HEAD  | api/customers            | customers.index   | \API\CustomerController@index  | api,auth:api,role:Admin|Employee
| DELETE    | api/customers/{customer} | customers.destroy | \API\CustomerController@destroy| api,auth:api,role:Admin|Employee 
| PUT|PATCH | api/customers/{customer} | customers.update  | \API\CustomerController@update | api,auth:api,role:Admin|Employee
| GET|HEAD  | api/customers/{customer} | customers.show    | \API\CustomerController@show   | api,auth:api,role:Admin|Employee 
| POST      | api/customers/{customer} |                   | \API\CustomerController@update | api,auth:api,role:Admin|Employee 
| POST      | api/login                |                   | \API\LoginController@login 	| api  
| GET|HEAD  | api/user                 |                   | Closure 					    | api,auth:api    
| GET|HEAD  | api/users                | users.index       | \API\UserController@index      | api,auth:api,role:Admin          
| POST      | api/users          	   | users.store       | \API\UserController@store      | api,auth:api,role:Admin          
| POST      | api/users/{users}        | users.changeRole  | \API\UserController@changeRole | api,auth:api,role:Admin          
| DELETE    | api/users/{user}         | users.destroy     | \API\UserController@destroy    | api,auth:api,role:Admin          
| PUT|PATCH | api/users/{user}         | users.update      | \API\UserController@update     | api,auth:api,role:Admin          
| GET|HEAD  | api/users/{user}         | users.show        | \API\UserController@show       | api,auth:api,role:Admin 

| DELETE    | oauth/authorize  | passport.authorizations.deny      | \DenyAuthorizationController@deny       | web,auth 
| GET|HEAD  | oauth/authorize  | passport.authorizations.authorize | \AuthorizationController@authorize      | web,auth 
| POST      | oauth/authorize  | passport.authorizations.approve   | \ApproveAuthorizationController@approve | web,auth 
| POST      | oauth/clients    | passport.clients.store            | \ClientController@store                 | web,auth 
| GET|HEAD  | oauth/clients    | passport.clients.index            | \ClientController@forUser               | web,auth
| DELETE    | oauth/clients/{client_id} | passport.clients.destroy | \ClientController@destroy               | web,auth
| PUT       | oauth/clients/{client_id} | passport.clients.update  | \ClientController@update                | web,auth
| POST      | oauth/personal-access-tokens|passport.personal.tokens.store |\PersonalAccessTokenController@store  |web,auth 
| GET|HEAD  | oauth/personal-access-tokens| passport.personal.tokens.index|\PersonalAccessTokenController@forUser| web,auth 
| DELETE | oauth/personal-access-tokens/{token_id}|passport.personal.tokens.destroy|\PersonalAccessTokenController@destroy|web,auth 
| GET|HEAD  | oauth/scopes            | passport.scopes.index    | \ScopeController@all                     | web,auth 
| POST      | oauth/token             | passport.token           | \AccessTokenController@issueToken        | throttle 
| POST      | oauth/token/refresh     | passport.token.refresh   | \TransientTokenController@refresh        | web,auth 
| GET|HEAD  | oauth/tokens            | passport.tokens.index    | \AuthorizedAccessTokenController@forUser | web,auth 
| DELETE    | oauth/tokens/{token_id} | passport.tokens.destroy  | \AuthorizedAccessTokenController@destroy | web,auth  


PRUEBAS 
Utilizaremos PostMan

Para dar de alta el admin y los roles tenemos el Seeder
\database\seeds\AdminSeeder.php
Los datos serán:
	Tabla Users
	'name' 		=> 'Admin',
	'surname' 	=> 'Admin',
	'email' 	=> 'admin@mitest.com',
	'password' 	=> bcrypt('12345678abc'), //12345678abc
	'created_at' => now(),
	'updated_at' => now(),

	Generaría el id = 1

	Tabla roles 
	'name' => 'Admin',
	'guard_name' => 'web',
	'created_at' => now(),
	'updated_at' => now(),

	Generaría el id = 1

	'name' => 'Employee',
	'guard_name' => 'web',
	'created_at' => now(),
	'updated_at' => now(),

	Generaría el id = 1

	Tabla model_has_roles
	'role_id' => 1,
	'model_type' => 'App\User',
	'model_id' => 1,

por tanto el usuario admin tendrá el rol de Admin.

Para empezar de cero sólo tendrían que hacer:

php artisan migrate:refresh
php artisan passport:install --force
php artisan db:seed

comando para iniciar el servidor
php artisan serve

Login
	Método: POST 
	Authentificación: No Auth
	Url: http://127.0.0.1:8000/api/login
	Parámetros: ?email=admin@mitest.com&password=12345678abc
	"message": "User login successfully."
	- Intentamos loguearnos con un usuario no registrado
		http://127.0.0.1:8000/api/login?email=cualquiera@mitest.com&password=cualquiera1
		"error": "Unauthorised"
	- Nos logueamos con un usuario correcto, pero con contraseña incorrecta
		http://127.0.0.1:8000/api/login?email=admin@mitest.com&password=12345678qwert
		"error": "Unauthorised"
	- Si intentamos hacer cualquier operacion sin estar logueados
		"error": "Unauthorised"

	NOTA: Esto nos devuelve un tokken, así que a partir de aquí tenemos que poner las siguientes cabeceras:
		'Accept' => 'application/json',
		'Authorization Bearer' => "poner el token que devuelve al hacer login"

Desde el login del Admin

Crear un usuario (store)
	Método: POST 
	Url: http://127.0.0.1:8000/api/users
	Parámetros: 
	?name=Juan&surname=Martin&email=juan@gmail.com&password=passworduser2&c_password=passworduser2
	?name=Marta&surname=Arraez&email=marta@gmail.com&password=passworduser3&c_password=passworduser3
	?name=Pedro&surname=Perez&email=pedro@gmail.com&password=passworduser4&c_password=passworduser4
	"message": "User register successfully."
	- Intentamos crear un usuario faltando parametros o con parametros erróneos
		http://127.0.0.1:8000/api/users?name=Juan&surname=Martin&email=juan@gmail.com
		"message": "Validation Error."

Listar usuarios (index)
	Método: GET 
	Url: http://127.0.0.1:8000/api/users
	"message": "Users list successfully."
	- Si no hay usuarios.
	  Siempre va a haber un usuario porque esta el admin y he decidido listarlo también.

Ver los datos de un usuario (show)
	Método: GET 
	Url: http://127.0.0.1:8000/api/users/2
	"message": "User retrieved successfully."
	- Si pasamos un id que no está
	http://127.0.0.1:8000/api/users/5
	"message": "User not found."

Modificar un usuario (update)
	Método:PUT 
	Url: http://127.0.0.1:8000/api/users
	Parámetros: ?name=Juan&surname=Martin&email=juan@gmail.com
	NOTA: obligo a pasar todos los datos, por que entiendo que esto se mandará desde un formulario, entonces los tendremos todos.
	NOTA2: No modificamos la password, porque esto se debería hacerse en otro método.
	"message": "User updated successfully."
	- Si faltan parametros o los metes erróneos
	"message": "Validation Error."

Eliminar un usuario (destroy)
	Método: DELETE 
	Url: http://127.0.0.1:8000/api/users/4
	"message": "User deleted successfully."
	- Si no existe el user
	http://127.0.0.1:8000/api/users/5
	"message": "User not found."
	- Si intentas destruirte a ti mismo (el sería un admin que es el único que tiene permiso para eliminar)
	http://127.0.0.1:8000/api/users/1
	"message": "Can not delete yourself."

Cambiar Role de un usuario (changeRole)
	Método: POST 
	Url: http://127.0.0.1:8000/api/users/2?role=1
	NOTA: Recordemos que el role 1 es admin
	"message": "Status change successfully."
	- si mandamos un role que no existe numerico y alpha
	"message": "No exist that role."
	- si le damos el mismo role
	"message": "User already has this role."
	- volvemos a ponerles el role usuario
	"message": "Status change successfully."

Crear un cliente (store)
	Método: POST 
	Url: http://127.0.0.1:8000/api/customers
	Parámetros por FORM (debido a que tiene la foto)  
	name=Sara
	surname=Ramirez
	email=sara@gmail.com
	photo=null
	Permite Crear sin incluir la foto

	name=Luis
	surname=Fernandes
	email=luis@gmail.com
	photo=1584293973IMG_20160408_174245.jpg

	name=Marcos
	surname=Gomez
	email=marcos@gmail.com
	photo=1584294054IMG_20170213_164944.jpg
	"message": "Customer created successfully."

	-Si nos falta un campo o lo metemos incorrecto.
	"message": "Validation Error."
	- controla que el email no se repita
	- controla que la foto sea una imagen
	- inserta el usuario de creacion y modificación.


Listar clientes (index)
	Método: GET 
	Url: http://127.0.0.1:8000/api/customers
	"message": "Customers list successfully."
	-Si no hay clientes devuelve 
	"message": "No customers."

Ver los datos de un cliente (show)
	Método: GET 
	Url: http://127.0.0.1:8000/api/customers/1
	"message": "Customer retrieved successfully."
	Si no exite el cliente
	http://127.0.0.1:8000/api/customers/6
	"message": "Customer not found."

Modificar un cliente (update)
	Método: POST 
	Url: http://127.0.0.1:8000/api/customers/1
	Parámetros por FORM (debido a que tiene la foto)  
	name=Sara
	surname=Ramirez
	email=sara@gmail.com
	photo=1584294536IMG_20160409_143042.jpg
	- Al modificar un foto, borra de /images la anterior si la tuviera
	- Si tenía una foto y la quita, también se borra de /images la que tenía
	"message": "Customer updated successfully."

Eliminar un cliente (destroy)
	Método: DELETE 
	Url: http://127.0.0.1:8000/api/customers/3
	"message": "Customer deleted successfully."
	Al eliminar también elimina la foto, si la tuviera
	-Si no existe el cliente
	http://127.0.0.1:8000/api/customers/4
	"message": "Customer not found."

Desde el login de un usuario
	Método: POST
	http://127.0.0.1:8000/api/login?email=marta@gmail.com&password=passworduser3
	"message": "User login successfully."

Como tiene role Employee en todas las operaciones de los usuarios devolverá el error:
"User have not permission."

Crear un cliente (store)
	Método: POST
	name=Marcos
	surname=Gomez
	email=marcos@gmail.com
	photo=1584296061IMG_20160409_142218.jpg
	"message": "Customer created successfully."

Listar clientes (index)
	Método: GET 
	Url: http://127.0.0.1:8000/api/customers
	"message": "Customers list successfully."

Ver los datos de un cliente (show)
	Método: GET 
	Url: http://127.0.0.1:8000/api/customers/1
	"message": "Customer retrieved successfully."
	- si no exite el cliente
	"message": "Customer not found."

Modificar un cliente (update)
	Método: POST 
	Url: http://127.0.0.1:8000/api/customers/1
	Parámetros por FORM (debido a que tiene la foto)  
	name=Sara
	surname=Ramirez
	email=sara@gmail.com
	photo=1584294536IMG_20160409_143042.jpg
	- cambia el usuario de modificacion
	"message": "Customer updated successfully."
	- si no exite el cliente
	"message": "Customer not found."

Eliminar un cliente (destroy)
Método: DELETE 
	Url: http://127.0.0.1:8000/api/customers/4
	"message": "Customer deleted successfully."
	-Si no existe el cliente
	http://127.0.0.1:8000/api/customers/5
	"message": "Customer not found."

Desde el login de un usuario con role = Admin
	Método: POST
	http://127.0.0.1:8000/api/login?email=juan@gmail.com&password=passworduser2
	"message": "User login successfully."

Como tiene rol admin puede ejecutar las operaciones sobre usuarios y clientes

Crear un usuario (store)
	Método: POST 
	Url: http://127.0.0.1:8000/api/users
	Parámetros: 
	?name=Ana&surname=Gutierrez&email=ana@gmail.com&password=passworduser6&c_password=passworduser6
	"message": "User register successfully."

Listar usuarios (index)
	Método: GET 
	Url: http://127.0.0.1:8000/api/users
	"message": "Users list successfully."

Listar clientes (index)
	Método: GET 
	Url: http://127.0.0.1:8000/api/customers
	"message": "Customers list successfully."

Ver los datos de un cliente (show)
	Método: GET 
	Url: http://127.0.0.1:8000/api/customers/1
	"message": "Customer retrieved successfully."

Modificar un cliente (update)
	Método: POST 
	Url: http://127.0.0.1:8000/api/customers/1
	Parámetros por FORM (debido a que tiene la foto)  
	name=Sara
	surname=Gomez
	email=sara@gmail.com
	photo=1584296830IMG_20160409_142218.jpg
	"message": "Customer updated successfully."


OPINIÓN PERSONAL

Para mi este proyecto ha sido todo un reto, debido a que no tenía conocimientos de Api Rest y muy básicos de Laravel. Seguramente muchas cositas se puedan mejorar y seguramente hayan fallitos, pero la verdad que estoy bastante orgullosa de lo que he conseguido.

Pase lo que pase, muchas gracias a todos por la oportunidad.