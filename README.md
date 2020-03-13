/ test_igarcia
 test idaira garcia

09/03/2020
Para desarrollar este procyecto voy a utilizar:
SO: Windows 10
Framework: Laravel 6
Herramienta de desarrollo: Laragon
Herramienta para gestionas las apis: client rest de Chrome
Sistema de gestión de versiones: GitHub

Hoy sólo he estado instalando herramientas y buscando información para estructurar el desarrollo.

10/03/2020
Autentificación de usuarios.
Voy a usar el paquete de autentificación API Passport de Laravel, este sigue el protocolo OAuth 2

En este punto me he creado 3 controladores:

API\ResponseController: gestiona las respuestas de las solicitudes API
API\LoginController: gestion el login de usuarios
API\RegisterController: gestiona el registro de usuario (Este creo que lo voy a usar para dar de alta el admin)

Pruebas:
http://127.0.0.1:8000/api/register?name=idaira&email=igc@gmail.com&password=12345678&c_password=12345678
http://127.0.0.1:8000/api/login?email=igc@gmail.com&password=12345678

También he probado casos erróneos. Hasta aquí todo correcto.

11/03/2020
