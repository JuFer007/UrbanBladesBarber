<div align="center">
  <img src="recursos/logoBarberia.png" alt="Urban Blades Barber" width="200"/>
  <br><br>
  <p><strong>Sistema de gestión integral para barbería con reservas online, administración de empleados, servicios, clientes y generación de reportes</strong></p>

  ![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
  ![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
  ![Node.js](https://img.shields.io/badge/Node.js-339933?logo=node.js&logoColor=white)
  ![Express](https://img.shields.io/badge/Express-000000?logo=express&logoColor=white)
  ![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
  ![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)
  ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)
  ![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?logo=bootstrap&logoColor=white)
  ![Puppeteer](https://img.shields.io/badge/Puppeteer-40B5A4?logo=puppeteer&logoColor=white)
</div>

<br>

## Descripción

**Urban Blades Barber** es un sistema web full-stack diseñado para la gestión completa de una barbería. Permite a los clientes explorar servicios, registrarse con Google, y reservar citas online. El panel administrativo ofrece control total sobre empleados, servicios, clientes, reservas y reportes financieros con gráficos estadísticos.

### Funcionalidades principales

- **Landing page** con presentación de barberos, servicios y carrusel de comentarios
- **Reservas online** con selección de servicio, fecha y hora
- **Autenticación** con Google OAuth 2.0 (clientes) y login local (empleados)
- **Dashboard administrativo** con gráficos de reservas e ingresos
- **CRUD completo** de empleados, servicios, clientes y reservas
- **Gestión de horarios** con calendario interactivo (FullCalendar)
- **Generación de tickets** en PDF con Puppeteer
- **Consulta de DNI** desde la SUNAT/RENIEC
- **Panel por roles:** Administrador, Barbero y Recepcionista

---

## Capturas del proyecto

### Landing Page

<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
  <img src="capturas/home1.png" alt="Home 1" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/home2.png" alt="Home 2" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/home3.png" alt="Home 3" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/home4.png" alt="Home 4" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/home5.png" alt="Home 5" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/home6.png" alt="Home 6" style="width: 48%; max-width: 600px;"/>
</div>

### Inicio de sesión

<div align="center">
  <img src="capturas/login.png" alt="Login" width="500"/>
</div>

### Dashboard

<div align="center">
  <img src="capturas/dashboard.png" alt="Dashboard" width="800"/>
</div>

### Gestión de empleados

<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
  <img src="capturas/empleados.png" alt="Empleados" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/empleadosModal.png" alt="Modal Empleados" style="width: 48%; max-width: 600px;"/>
</div>

### Gestión de servicios

<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
  <img src="capturas/servicios.png" alt="Servicios" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/agregarServicio.png" alt="Agregar Servicio" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/editarServicio.png" alt="Editar Servicio" style="width: 48%; max-width: 600px;"/>
</div>

### Gestión de clientes

<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
  <img src="capturas/clientes.png" alt="Clientes" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/editarCliente.png" alt="Editar Cliente" style="width: 48%; max-width: 600px;"/>
</div>

### Gestión de citas

<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
  <img src="capturas/citas.png" alt="Citas" style="width: 48%; max-width: 600px;"/>
  <img src="capturas/agregarCita.png" alt="Agregar Cita" style="width: 48%; max-width: 600px;"/>
</div>

### Vista del barbero

<div align="center">
  <img src="capturas/vistaBarbero.png" alt="Vista Barbero" width="800"/>
</div>

---

## Arquitectura del proyecto

```
UrbanBladesBarber/
├── index.html              # Landing page principal
├── scriptCreacion.sql      # Script de base de datos MySQL
│
├── api/                    # Microservicios Node.js (Express)
│   └── server/
│       ├── serverGoogle.js     # Autenticación Google OAuth (puerto 3000)
│       ├── serverDNI.js        # Consulta de DNI RENIEC (puerto 3001)
│       ├── serverTicket.js     # Generación de tickets PDF (puerto 3000)
│       └── credenciales.json   # Credenciales de Google OAuth
│
├── backEnd/                # Backend PHP (MVC)
│   ├── conexionBD_MySQL.php   # Conexión a MySQL
│   ├── controladores/         # Controladores (endpoints API)
│   ├── dao/                   # Data Access Objects
│   ├── modelos/               # Modelos de datos
│   └── servicios/             # Sesiones y login Google
│
├── frontEnd/               # Frontend
│   ├── css/                   # Estilos CSS
│   ├── html/                  # Páginas HTML
│   │   ├── login.html
│   │   ├── dashboard.html
│   │   └── dashboard2.html
│   └── js/                    # JavaScript
│       ├── scriptInicioSesion.js
│       ├── scriptValidacion.js
│       └── otrosScripts/
│
├── recursos/               # Recursos estáticos (imágenes)
└── capturas/               # Capturas de pantalla del proyecto
```


```
http://localhost:8000
```

## Stack tecnológico

| Tecnología | Propósito |
|---|---|
| **PHP** | Backend principal con arquitectura MVC |
| **MySQL** | Base de datos relacional |
| **Node.js + Express** | Microservicios (OAuth, DNI, PDF) |
| **HTML5 + CSS3 + JavaScript** | Frontend vanilla |
| **Bootstrap 5** | Framework CSS responsivo |
| **Chart.js** | Gráficos estadísticos en dashboard |
| **FullCalendar** | Calendario interactivo de reservas |
| **Puppeteer** | Generación de tickets PDF |
| **Passport.js** | Estrategia Google OAuth 2.0 |

---
