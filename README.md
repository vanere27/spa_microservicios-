# spa_microservicios-
Este es el proyecto  para spa de uñas basado en la arquitectura de microservicios, permite gestionar la reserva de servicios en un spa de uñas.
La plataforma facilitará a los clientes la reserva de citas, a los administradores la gestión de usuarios y servicios, y a los dueños del negocio la visualización de reportes sobre desempeño y ventas.

Cada microservicio puede ejecutarse de forma independiente y cuenta con su propia base de datos.  
La comunicación entre servicios se realiza mediante **peticiones REST** y, en algunos casos, **mensajería o notificaciones por correo electrónico.**

Microservicios:

1. Microservicio de Seguridad (Laravel/puerto 8000)
- Autenticación y generación de tokens 
- Registro y gestión de usuarios  
- Control de roles(Administrador, Cliente, etc.)
Realizado con: 
- Laravel  
- MySQL  
- Sanctum 

2. Microservicio de Servicios (Django/puerot 8001)
- Gestión de servicios del spa 
- CRUD completo: creación, actualización, eliminación y consulta  
Realizado con
- Django  
- MySQL  
- Django REST Framework  

3. Microservicio de Reservas (Flask) 
- Creación y gestión de reservas  
realzado con: 
- Flask  
- MongoDB

4. Microservicio de Reportes (Flask)

- Generación de reportes en PDF y Excel 
- Almacenamiento histórico en MongoDB  


- Flask  
- Pandas  
- ReportLab  
- XlsxWriter  
- MongoDB  

5. Microservicio de Notificaciones (Flask)
- Envío de notificaciones por correo electrónico a los usuarios  

Con:
- Flask  
- smtplib  
- EmailMessage  
- Python-dotenv  

Requisitos:

Para ejecutar los microservicios se deb tener:
- Python
- Node js y composer (laravel)
- MongoDB
- MySQL





