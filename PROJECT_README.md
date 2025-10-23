# CRUD Laravel - Sistema de Gestión

## 📋 Sobre el Proyecto
Sistema web para gestión de clientes y productos desarrollado por **Erick Adrian Mendez Villalpando**. 
Usa Laravel 11 con MySQL - proyecto personal para practicar desarrollo web.

## 🚀 Lo que tiene
- ✅ CRUD básico para Clientes y Productos
- ✅ Dashboard con gráficos (Chart.js)
- ✅ Diseño responsive con Bootstrap 5
- ✅ Confirmaciones con SweetAlert2
- ✅ Base de datos MySQL
- ✅ Tests unitarios y de integración
- ✅ Búsqueda y paginación
- ✅ Validaciones básicas

## 🛠️ Stack Tecnológico
- **Backend:** Laravel 11, PHP 8.3
- **Base de Datos:** MySQL/MariaDB
- **Frontend:** Bootstrap 5, Chart.js, SweetAlert2
- **Testing:** PHPUnit con cobertura completa
- **Desarrollo:** Composer, Artisan CLI

## 📦 Instalación y Configuración

### Requisitos del Sistema
- PHP 8.2 o superior
- MySQL 5.7 o superior
- Composer (gestor de dependencias)
- Node.js (opcional para assets)

### Pasos de Instalación
1. **Clonar el repositorio**
   ```bash
   git clone [url-del-repositorio]
   cd crud-demo
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   # Editar .env con tus datos de MySQL
   ```

4. **Ejecutar migraciones**
   ```bash
   php artisan migrate
   ```

5. **Iniciar servidor de desarrollo**
   ```bash
   php artisan serve
   ```

## 🗄️ Estructura de Base de Datos

### Tabla: `clients`
- `id` - Identificador único
- `name` - Nombre del cliente
- `email` - Email único
- `phone` - Teléfono (opcional)
- `active` - Estado activo/inactivo
- `created_at` / `updated_at` - Timestamps

### Tabla: `products`
- `id` - Identificador único
- `name` - Nombre del producto
- `sku` - Código único del producto
- `price` - Precio (decimal)
- `stock` - Cantidad en inventario
- `created_at` / `updated_at` - Timestamps

## 🧪 Testing y Calidad

### Ejecutar Tests
```bash
php artisan test
```

### Cobertura de Tests
- **65 tests pasando** con 197 assertions
- Tests unitarios para modelos
- Tests de integración para controladores
- Tests de funcionalidad completa
- Validación de casos edge

## 📱 Rutas y Funcionalidades

### URLs Principales
- **Dashboard:** `/` - Panel principal con estadísticas
- **Clientes:** `/clients` - Gestión de clientes
- **Productos:** `/products` - Gestión de productos

### Funcionalidades por Módulo

#### Clientes
- ✅ Listado con búsqueda por nombre/email
- ✅ Crear nuevo cliente
- ✅ Editar datos existentes
- ✅ Eliminar cliente
- ✅ Estado activo/inactivo

#### Productos
- ✅ Listado con búsqueda por nombre/SKU
- ✅ Crear nuevo producto
- ✅ Editar datos existentes
- ✅ Eliminar producto
- ✅ Control de inventario

#### Dashboard
- ✅ Estadísticas generales
- ✅ Gráficos de crecimiento mensual
- ✅ Top productos por stock
- ✅ Distribución de clientes activos/inactivos

## 🔧 Configuración Avanzada

### Variables de Entorno Importantes
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_demo
DB_USERNAME=root
DB_PASSWORD=
```

### Personalización
- Modificar validaciones en controladores
- Ajustar límites de paginación
- Personalizar mensajes de confirmación
- Configurar gráficos del dashboard

## 👨‍💻 Autor y Desarrollo

**Desarrollado por:** Erick Adrian Mendez Villalpando
**Versión:** 1.0
**Fecha:** Octubre 2025
**Tecnologías:** Laravel 11, MySQL, Bootstrap 5, Chart.js

## 📄 Licencia
Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

## 🤝 Contribuciones
Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📞 Soporte
Para soporte técnico o consultas sobre el proyecto, contactar al desarrollador.

---
*Desarrollado con ❤️ usando Laravel 11 y MySQL*
