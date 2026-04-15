# TODO: Product Deactivate Feature

**Status: Approved - In Progress**

Steps:
- [x] 1. Add ?activar=ID & ?desactivar=ID handlers in index.php
- [x] 2. Split query: $productos_activos (activo=1), $productos_inactivos (activo=0)
- [ ] 3. 2 tables: 'Productos Activos' + 'Productos Desactivados'
- [ ] 4. Buttons per row: Desactivar/Activar + Edit/Delete
- [x] 5. Tienda usuario shows only activo=1 ✓
- [ ] 6. Test toggle

**Goal:** Toggle visibility in user store, safe from FK issues.

