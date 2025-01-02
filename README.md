# IDBI Invoice Recorder Challenge

API REST que permite registrar comprobantes en formato XML y consultarlos. A partir de estos comprobantes, se extrae
información relevante como los datos del emisor y receptor, los artículos o líneas incluidas y los montos totales.

La API utiliza JSON Web Token (JWT) para la autenticación.

---

## Componentes

El proyecto se ha desarrollado utilizando las siguientes tecnologías:

- **PHP 8.x**
- **Laravel 10.x**
- **MySQL** (Base de datos)
- **Docker Compose** (Entorno de contenedores)
- **MailHog** (Gestor de correos para desarrollo)

---

## Preparación del Entorno

El proyecto cuenta con una implementación de Docker Compose para facilitar la configuración del entorno de desarrollo.

> ⚠️ Si no estás familiarizado con Docker, puedes optar por otra configuración para preparar tu entorno. Si decides
> hacerlo, omite los pasos 1 y 2.

Instrucciones para iniciar el proyecto

1. Levantar los contenedores con Docker Compose:

```bash
docker compose up -d
```

2. Acceder al contenedor web:

```bash
docker exec -it idbi-invoice-recorder-challenge-web-1 bash
```

3. Configurar las variables de entorno:

```bash
cp .env.example .env
```

4. Configurar el secreto de JWT en las variables de entorno (genera una cadena de texto aleatoria):

```bash
JWT_SECRET=<random_string>
```

5. Instalar las dependencias del proyecto:

```bash
composer install
```

6. Generar una clave para la aplicación:

```bash
php artisan key:generate
```

7. Ejecutar las migraciones de la base de datos:

```bash
php artisan migrate
```

8. Rellenar la base de datos con datos iniciales:

```bash
php artisan db:seed
```

**¡Y listo!** Ahora puedes empezar a desarrollar.

## Uso

La API estará disponible en: http://localhost:8000/api/v1

### (Opcional) Verificar correos en MailHog

MailHog está configurado para capturar los correos enviados en desarrollo. Accede a: [http://localhost:8025](http://localhost:8025).

### Endpoints Disponibles
![image](https://github.com/user-attachments/assets/73b4b324-fa17-45e9-8b41-57f0bd1bc556)

#### 1. Autenticación

- **POST /api/v1/login**: Inicia sesión y devuelve un token JWT.
- **POST /api/v1/register**: Registra un nuevo usuario.
- **POST /api/v1/logout**: Salir Sesión del usuario.

#### 2. Registro de Comprobantes

- **POST /api/v1/vouchers**: Permite cargar comprobantes XML. El procesamiento se realiza de forma asíncrona (colas).

#### 3. Consulta de Comprobantes
- **GET /api/v1/vouchers**: Lista comprobantes con soporte para:
  - **Filtros avanzados**: Serie, número, tipo de comprobante, moneda, y rango de fechas. Ejemplo: **GET /api/v1/vouchers/filtrar?issuer_name=MERCURIAL&receiver_name=APARICIO&serie=F011&moneda=PEN&date_from=2025-01-01&date_to=2025-01-02&page=1&paginate=10**
  - **Paginación**: Paramétros `page` y `paginate`. Ejemplo: **GET /api/v1/vouchers?page=1&paginate=10**

#### 4. Montos Acumulados

- **GET /api/v1/vouchers/montos-acumulados**: Devuelve los montos acumulados por moneda (US y PEN).

#### 5. Eliminación de Comprobantes

- **DELETE /api/v1/vouchers/{id}**: Elimina un comprobante específico registrado por el usuario autenticado.

---

## Cambios Implementados

### 1. Almacenamiento de Información Adicional

- **Serie, Número, Tipo de Comprobante y Moneda** extraídos del XML y almacenados en la base de datos.
- **Regularización**: Comprobantes existentes actualizados con estos campos desde el contenido del XML.

### 2. Procesamiento Asíncrono

- Implementado mediante **Jobs y Queues**.
- Correos enviados al finalizar con:
  - Comprobantes exitosos.
  - Errores en el procesamiento.

### 3. Montos Acumulados

- Endpoint para calcular y mostrar los montos acumulados por moneda (Soles y Dólares).

### 4. Eliminación de Comprobantes

- Los comprobantes pueden ser eliminados por su `id`, siempre que pertenezcan al usuario autenticado.

### 5. Filtros Avanzados

- Búsqueda con filtros por:
  - Serie
  - Número
  - Tipo de Comprobante
  - Moneda
  - Rango de Fechas (obligatorio).

---

## Ejemplo de Resumen de Correo

Cuando el procesamiento de comprobantes finaliza, se envía un correo al usuario con el siguiente formato:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Comprobantes Subidos</title>
</head>
<body>
    <h1>Estimado John,</h1>
    <p>Hemos recibido tus comprobantes con los siguientes detalles:</p>
    <ul>
        <li>Nombre del Emisor: MERCURIAL TEST S.A.C</li>
        <li>Tipo de Documento del Emisor: 6</li>
        <li>Número de Documento del Emisor: 2094274929</li>
        <li>Nombre del Receptor: APARICIO RAMOS ANGIE EVELYN</li>
        <li>Tipo de Documento del Receptor: 6</li>
        <li>Número de Documento del Receptor: 10102784427</li>
        <li>Monto Total: 805.5</li>
    </ul>
    <p>¡Gracias por usar nuestro servicio!</p>
</body>
</html>
```

---

## Pruebas

### Cobertura

1. **Procesamiento Asíncrono**: Verificado con Laravel Queues.
2. **Filtros Avanzados**: Consultas probadas con combinaciones de filtros.
3. **Montos Acumulados**: Correcta separación de divisas y sumas.
4. **Eliminación Segura**: Asegurando que un usuario no pueda eliminar comprobantes ajenos.

### Proceso

- Usar Postman o herramientas similares para probar los endpoints.
- Verificar el contenido de la base de datos tras cada operación.

---

## Consideraciones Finales

### Mejoras Futuras

1. **Autorización Avanzada**: Roles y permisos.
2. **Integración con Servicios de Facturación**: Ejemplo, SUNAT (Perú).
3. **Notificaciones**: Agregar soporte para SMS o push notifications.

---

**Fecha de finalización**: 2 de enero de 2025.

**Autor**: [TheDanilore].

