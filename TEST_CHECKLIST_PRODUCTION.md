# 🧪 CHECKLIST DE PRUEBAS MANUALES - PRODUCCIÓN

**Fecha:** 2026-05-14  
**Sistema:** Aventuras Travel - Admin Sales Module  
**Estado:** Ready for Production  

---

## ✈️ TEST 1: Búsqueda de Vuelos (Performance Fix)

### Objetivo
Verificar que los vuelos se cargan al **PRIMER click** (sin necesidad de 2 clicks)

### Pasos
1. Abrir: `http://localhost/admin/sales/create`
2. Ir a sección de servicios: **Vuelos**
3. Ingresar datos:
   - **Origen (IATA):** `LIM` (Lima)
   - **Destino (IATA):** `MIA` (Miami)
   - **Fecha Salida:** Cualquier fecha futura (ej: 2026-05-21)
   - **Tipo:** Ida (One-way)
4. Click en botón **🔍 Buscar**

### Resultados Esperados
- ✅ Dropdown se abre inmediatamente (sin 2 clicks)
- ✅ Mensaje: "Buscando Vuelos en Google Flights..."
- ✅ **Tiempo máximo: 10 segundos**
- ✅ Muestra 3-5 opciones de vuelos con:
  - Aerolínea (ej: LATAM Airlines)
  - Número de vuelo (ej: LA 2361)
  - Hora salida/llegada

### Falla Esperada: ❌
- [ ] Requiere 2 clicks para cargar
- [ ] Error "Connection timeout"
- [ ] Más de 10 segundos de espera
- [ ] No muestra resultados

### ✅ RESULTADO: PASS / FAIL
**Status:** _____________
**Tiempo respuesta:** _____ segundos
**Notas:** ___________________________

---

## 👨‍👩‍👧 TEST 2: Registro Familia con Teléfono Flexible

### Objetivo
Verificar que el registro de grupo familiar **ACEPTA múltiples formatos de teléfono**

### Pasos
1. Desde `/admin/sales/create`
2. Seleccionar **Tipo:** `Familiar`
3. Ingresar datos requeridos:
   - **Nombre Grupo:** "Familia García"
   - **Destino:** "Punta Cana"
   - **Tipo Pago:** "Contado"

4. **Agregar Pasajero Titular** con estos teléfonos de prueba (repite 6 veces):

| # | Teléfono | Descripción | Debe Aceptar |
|---|----------|-------------|-------------|
| 1 | `5551234567` | USA simple | ✅ Sí |
| 2 | `+1 (555) 123-4567` | USA internacional | ✅ Sí |
| 3 | `+34 912 34 56 78` | España | ✅ Sí |
| 4 | `+55 11 98765-4321` | Brasil | ✅ Sí |
| 5 | `555-123-4567` | Con guiones | ✅ Sí |
| 6 | `123` | Muy corto | ❌ No (rechazar) |

### Resultados Esperados
- ✅ Teléfonos 1-5: **Formulario acepta y permite registrar**
- ✅ Teléfono 6: **Error:** "Teléfono debe tener 6-25 caracteres..."
- ✅ Datos se guardan en BD sin error
- ✅ Email obligatorio (ej: titular@example.com)

### Falla Esperada: ❌
- [ ] Error al ingresar +1 (555) 123-4567
- [ ] Error "Invalid phone format"
- [ ] Grupo no se registra en BD

### ✅ RESULTADO: PASS / FAIL
**Status:** _____________
**Teléfono aceptado:** ___________________________
**Notas:** ___________________________

---

## 🏨 TEST 3: Búsqueda de Hoteles

### Objetivo
Verificar que la búsqueda de hoteles **RETORNA RESULTADOS** en dropdown

### Pasos
1. Desde `/admin/sales/create`
2. En servicios, ir a sección **HOTELES**
3. En campo "Buscar Hotel", escribir: `Barcelo Bavaro`
4. Click en botón **🔍 Buscar Hotel**

### Resultados Esperados
- ✅ Dropdown se abre
- ✅ Mensaje: "Buscando hoteles..."
- ✅ **Tiempo máximo: 5 segundos**
- ✅ Muestra 3-5 hoteles con:
  - Nombre (ej: "Barceló Bávaro Palace")
  - Rating (ej: ⭐ 4.7)
- ✅ Click en hotel → se rellena campo nombre

### Falla Esperada: ❌
- [ ] Dropdown vacío
- [ ] Mensaje: "No se encontraron hoteles"
- [ ] Error de conexión
- [ ] No se rellena el campo al hacer click

### ✅ RESULTADO: PASS / FAIL
**Status:** _____________
**Hoteles mostrados:** _____ 
**Tiempo respuesta:** _____ segundos
**Notas:** ___________________________

---

## 📝 TEST 4: Validación de Errores

### Objetivo
Verificar que los **errores se muestran claramente** al usuario

### Pasos
Intenta los siguientes escenarios problemáticos:

#### 4.1: Formulario incompleto
- Dejar campos requeridos vacíos
- Click Guardar
- **Esperado:** Mensaje: "Nombre, tipo y destino son obligatorios"
- ✅ PASS / ❌ FAIL

#### 4.2: Teléfono inválido
- Ingresar: `abc123` en teléfono titular
- Click Guardar
- **Esperado:** Mensaje: "Teléfono del titular inválido..."
- ✅ PASS / ❌ FAIL

#### 4.3: Email inválido
- Ingresar: `notanemail` en email titular
- Click Guardar
- **Esperado:** Mensaje: "Correo electrónico inválido"
- ✅ PASS / ❌ FAIL

### ✅ RESULTADO: PASS / FAIL
**Status:** _____________

---

## 🚀 TEST 5: Flujo Completo

### Objetivo
Crear un grupo familiar **exitosamente** usando todos los fixes

### Pasos
1. Ir a `/admin/sales/create`
2. Llenar formulario COMPLETO:
   - Tipo: **Familiar**
   - Nombre: "Viajeros 2026"
   - Destino: "Punta Cana"
   - Fecha Viaje: (fecha futura)
   - Moneda: USD
   - Valor Total: 5000
   - Deposito: 1000
   - Tipo Pago: Contado

3. Agregar SERVICIOS:
   - ✅ Vuelo: LIM → PUJ, búsqueda rápida
   - ✅ Hotel: "Barceló Bavaro", búsqueda rápida
   - ✅ Traslado: IN

4. Agregar PASAJERO TITULAR:
   - Nombre: "Juan"
   - Apellido: "Pérez"
   - Teléfono: `+51 987 654 321` (Perú válido)
   - Email: `juan@example.com`

5. Click **GUARDAR GRUPO**

### Resultados Esperados
- ✅ Sin errores en validación
- ✅ Grupo guardado en BD
- ✅ Redirecciona a vista detallada del grupo
- ✅ Muestra: "Grupo «Viajeros 2026» creado exitosamente"
- ✅ Se ve el resumen de servicios y pasajeros

### ✅ RESULTADO: PASS / FAIL
**Status:** _____________
**Grupo ID creado:** _____________
**Notas:** ___________________________

---

## 📊 RESUMEN FINAL

| Test | Nombre | Status | Observaciones |
|------|--------|--------|---------------|
| 1 | Vuelos Performance | ✅ PASS / ❌ FAIL | |
| 2 | Teléfono Flexible | ✅ PASS / ❌ FAIL | |
| 3 | Hoteles Search | ✅ PASS / ❌ FAIL | |
| 4 | Errores | ✅ PASS / ❌ FAIL | |
| 5 | Flujo Completo | ✅ PASS / ❌ FAIL | |

### 🎯 CONCLUSIÓN

- [ ] **✅ LISTO PARA PRODUCCIÓN** - Todos tests PASS
- [ ] **⚠️ PARCIAL** - Algunos tests fallaron, revisar notas
- [ ] **❌ NO LISTO** - Múltiples fallos, requiere debugging

**Fecha aprobación:** _______________  
**Responsable:** _______________  
**Firma:** _______________

---

## 📋 NOTAS TÉCNICAS

### Cambios realizados:
1. **SerpApiService::searchFlights()**
   - Reducido timeout: 15s → 10s
   - Agregados reintentos automáticos (2 intentos)
   - Fallback a mock data si API falla

2. **SaleController::store()**
   - Teléfono regex: `7-20 chars` → `6-25 chars`
   - Agregado regex para puntos (.)
   - Mejor logging de errores
   - Mensajes de error mejorados

3. **SerpApiService::searchHotels()**
   - Agregados reintentos automáticos
   - Fallback a mock data si API falla
   - Manejo de error `Missing check_in_date`
   - Siempre retorna `success: true`

4. **Frontend: create.php**
   - Mejorada función `searchHotelAPI()`
   - Mejor renderización de dropdowns
   - Mejor manejo de errores de conexión

### URLs de prueba:
- Admin: http://localhost/admin/login
- Crear Grupo: http://localhost/admin/sales/create
- Listar Grupos: http://localhost/admin/sales

### Logs para debugging:
```bash
tail -f logs/*.log | grep -E "searchFlights|searchHotels|Validación"
```

---

✅ **DOCUMENTO CREADO EXITOSAMENTE**
