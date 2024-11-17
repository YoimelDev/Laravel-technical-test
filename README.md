# Laravel Technical Test

API RESTful desarrollada con Laravel 11 para la gestión de usuarios, empresas y actividades.

## Requisitos

- Docker
- PHP 8.3+
- Composer 2.x

## Configuración Inicial

1. Clonar el repositorio:
```bash
git clone https://github.com/YoimelDev/Laravel-technical-test
cd Laravel-technical-test
```

2. Copiar el archivo de entorno:
```bash
cp .env.example .env
```

3. Configurar las variables de entorno:
```bash
FIXER_API_KEY=your_api_key
FIXER_API_URL=http://data.fixer.io/api/
```

4. Iniciar los contenedores Docker:
```bash
./vendor/bin/sail up -d
```

5. Instalar dependencias:
```bash
./vendor/bin/sail composer install
```

6. Generar key de la aplicación:
```bash
./vendor/bin/sail artisan key:generate
```

7. Ejecutar migraciones y seeders:
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
- Pruebas unitarias y de integración con PHPUnit

## API Endpoints

#### Auth
- `POST /api/v1/register` - Registro de usuarios
- `POST /api/v1/login` - Login de usuarios

#### Users
- `GET /api/v1/users` - Listar usuarios
- `POST /api/v1/role-requests` - Solicitar cambio de rol
- `PATCH /api/v1/role-requests/{id}/process` - Procesar solicitud de cambio de rol

#### Companies
- `GET /api/v1/companies` - Listar empresas
- `POST /api/v1/companies` - Crear empresa
- `GET /api/v1/companies/{id}` - Ver empresa
- `PATCH /api/v1/companies/{id}` - Actualizar empresa
- `DELETE /api/v1/companies/{id}` - Eliminar empresa

#### Activity Types
- `GET /api/v1/activity-types` - Listar tipos de actividad
- `POST /api/v1/activity-types` - Crear tipo de actividad
- `POST /api/v1/companies/{id}/relationships/activity-types` - Asociar actividades a empresa
- `DELETE /api/v1/companies/{id}/relationships/activity-types` - Desasociar actividades de empresa

#### Currency Conversion
- `POST /api/v1/currency/convert` - Convertir moneda
- `GET /api/v1/currency/history` - Obtener historial de conversiones

## Headers

- Para autenticación: `Authorization: Bearer {token}`
- Para API JSON:API:
```
Accept: application/vnd.api+json
Content-Type: application/vnd.api+json
```

## Comandos Útiles

- Importar tipos de cambio históricos:
```bash
./vendor/bin/sail artisan currency:import-historical
```
- Ejecutar pruebas:
```bash
./vendor/bin/sail test
```

- Ejecutar pruebas con cobertura:
```bash
./vendor/bin/sail test --coverage
```

## Notas de Desarrollo

- Los usuarios nuevos se registran con rol 'basic'
- Listar usuarios solo está permitido para roles 'admin'
- Las solicitudes de cambio de rol deben ser aprobadas por un admin
- Las empresas solo pueden ser creadas por usuarios con rol 'business_owner' o 'admin'
- La conversión de monedas utiliza caché para optimizar rendimiento
- La API implementa el estándar JSON:API para recursos y relaciones
