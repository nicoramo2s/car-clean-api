# CarClean

Plataforma digital para operación, venta y escalamiento de servicios de limpieza vehicular.

## Enfoque de Negocio

`CarClean` está diseñado como un activo tecnológico rentable:

- Captación de clientes con reservas digitales.
- Optimización operativa para reducir tiempos muertos y costo por servicio.
- Gestión comercial para aumentar recurrencia y ticket promedio.
- Trazabilidad de métricas para decisiones de crecimiento y margen.

## Propuesta de Valor

- Experiencia rápida de reserva y atención.
- Estandarización de servicios para garantizar calidad.
- Operación medible con foco en eficiencia y rentabilidad.
- Escalabilidad para múltiples puntos de atención.

## Modelo de Ingresos

- Venta directa de servicios de lavado y detailing.
- Paquetes y suscripciones recurrentes.
- Upselling de servicios premium.
- Programas corporativos para flotas.

## Indicadores Clave (KPI)

- Ingresos por día y por sucursal.
- Ticket promedio por cliente.
- Margen bruto por tipo de servicio.
- Tasa de recompra y retención mensual.
- Productividad operativa por equipo y franja horaria.

## Stack Tecnológico

- PHP 8.3
- Laravel 12
- Sanctum para autenticación API
- Pest para pruebas automatizadas

## Instalación Local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

## Pruebas

```bash
php artisan test --compact
```

## Objetivo del Proyecto

Construir una plataforma robusta orientada a crecimiento comercial sostenible, maximizando ingresos, controlando costos operativos y habilitando expansión del negocio.
