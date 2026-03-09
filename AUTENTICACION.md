# Sistema de Autenticación - Carpintería Don Gusto

## 🎯 Descripción

Se ha implementado un sistema de **autenticación basado en roles** con dos tipos de usuarios:

### 👤 Roles de Usuario

1. **ADMINISTRADOR** - Acceso total a panel de administración
   - Agregar nuevos productos
   - Actualizar productos existentes
   - Eliminar productos
   - Ver estadísticas

2. **USUARIO (Comprador)** - Acceso a catálogo
   - Ver productos disponibles
   - Realizar pedidos
   - Perfil de usuario

---

## 🔐 Credenciales de Prueba

### Administrador
```
📧 Email: admin@carpinteria.com
🔑 Contraseña: admin123
```
**Acceso:** Panel Administrativo en `/panel-admin.html`

### Usuario Regular
```
📧 Email: usuario@correo.com
🔑 Contraseña: usuario123
```
**Acceso:** Catálogo de productos en `/productos.html`

---

## 📂 Estructura de Archivos Nuevos

### Backend
```
backend/src/
├── controllers/
│   └── authController.js          (Lógica de autenticación)
├── middleware/
│   └── authMiddleware.js          (Protección de rutas)
├── routes/
│   ├── authRoutes.js              (Rutas de login)
│   └── productosRoutes.js         (Actualizado con protección)
└── models/
    └── recetaModel.js            (Nuevos métodos CRUD)
```

### Frontend
```
frontend/views/Carpintin-Don-Gusto/
├── login.html                    (Nueva: Página de login)
├── panel-admin.html              (Nueva: Panel administrativo)
├── productos.html                (Actualizado: Con autenticación)
└── index.html                    (Actualizado: Redirección a login)
```

---

## 🔗 Endpoints de API

### Autenticación (Pública)
```
POST /api/auth/login
POST /api/auth/verificar
```

### Productos (Públicos - lectura)
```
GET /api/productos              (Listar todos)
GET /api/productos/:id          (Obtener uno)
```

### Pedidos (Protegido - usuario)
```
POST /api/productos/pedido       (Crear pedido)
```

### Administración (Protegido - admin)
```
POST /api/productos              (Crear producto)
PUT /api/productos/:id           (Actualizar producto)
DELETE /api/productos/:id        (Eliminar producto)
```

---

## 🚀 Cómo Usar

### 1. Iniciar el Servidor
```bash
cd backend
npm start
```
El servidor estará disponible en `http://localhost:3000`

### 2. Acceder a la Aplicación
```
http://localhost:3000/views/Carpintin-Don-Gusto/login.html
```

### 3. Seleccionar un Usuario de Prueba
Haz clic en cualquiera de los usuarios de prueba para iniciar sesión automáticamente.

---

## 🔑 Características Implementadas

✅ Autenticación con JWT (JSON Web Tokens)  
✅ Protección de rutas por rol  
✅ Sistema de login/logout  
✅ Panel administrativo  
✅ CRUD de productos (solo admin)  
✅ Almacenamiento de sesión en localStorage  
✅ Interfaz responsiva y moderna  

---

## 📝 Notas Importantes

- Los tokens JWT expiran después de **24 horas**
- Las sesiones se guardan en `localStorage` del navegador
- Para producción, cambia la `SECRET_KEY` en `authController.js`
- Los usuarios de prueba deben migrarse a la base de datos real

---

## 🛠️ Próximas Mejoras

- [ ] Registrar nuevos usuarios
- [ ] Recuperación de contraseña
- [ ] Persistencia de datos en base de datos
- [ ] Sistema de pagos
- [ ] Notificaciones por email
