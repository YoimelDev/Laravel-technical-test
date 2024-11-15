# Atrinium Backend Technical Test

API RESTful desarrollada con Laravel 11 para la gestión de usuarios, empresas y actividades.

## Requisitos

- Docker y Docker Compose
- PHP 8.3+
- Composer 2.x

## Configuración Inicial

1. Clonar el repositorio:
```bash
git clone https://github.com/your-username/atrinium-api.git
cd atrinium-api
```

2. Copiar el archivo de entorno:
```bash
cp .env.example .env
```

3. Iniciar los contenedores Docker:
```bash
./vendor/bin/sail up -d
```

4. Instalar dependencias:
```bash
./vendor/bin/sail composer install
```

5. Generar key de la aplicación:
```bash
./vendor/bin/sail artisan key:generate
```

6. Ejecutar migraciones y seeders:
```bash
./vendor/bin/sail artisan migrate --seed
```

## Características Principales

- Autenticación de usuarios con Laravel Sanctum
- Gestión de roles y permisos con spatie/laravel-permission
- API RESTful siguiendo especificación JSON:API
- Sistema de notificaciones por email
- Conversión de monedas con caché
- Importación de tipos de cambio históricos
- Pruebas unitarias y de integración con Pest

## Estructura del Proyecto

- `app/Http/Controllers/Api/V1/` - Controladores API
- `app/Models/` - Modelos Eloquent
- `app/Services/` - Servicios de la aplicación
- `app/Notifications/` - Notificaciones
- `database/migrations/` - Migraciones
- `database/seeders/` - Seeders
- `tests/` - Pruebas unitarias y de integración

## API Endpoints

La documentación completa de la API está disponible en:
- Local: `http://localhost/docs`

### Endpoints Principales:

#### Auth
- `POST /api/v1/register` - Registro de usuarios
- `POST /api/v1/login` - Login de usuarios

#### Users
- `GET /api/v1/users` - Listar usuarios
- `POST /api/v1/role-requests` - Solicitar cambio de rol

#### Companies
- `GET /api/v1/companies` - Listar empresas
- `POST /api/v1/companies` - Crear empresa
- `GET /api/v1/companies/{id}` - Ver empresa
- `PATCH /api/v1/companies/{id}` - Actualizar empresa
- `DELETE /api/v1/companies/{id}` - Eliminar empresa

## Pruebas

Ejecutar las pruebas:
```bash
./vendor/bin/sail test
```

## Comandos Útiles

- Importar tipos de cambio históricos:
```bash
./vendor/bin/sail artisan currency:import-historical
```

## Notas de Desarrollo

- Los usuarios nuevos se registran con rol 'basic'
- Las solicitudes de cambio de rol deben ser aprobadas por un admin
- Las empresas solo pueden ser creadas por usuarios con rol 'company_owner'
- La conversión de monedas utiliza caché para optimizar rendimiento

## Contribución

1. Fork el repositorio
2. Crear una rama feature (`git checkout -b feature/AmazingFeature`)
3. Commit los cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## Licencia

[MIT](https://opensource.org/licenses/MIT)