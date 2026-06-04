<template>
  <div style="font-family: Arial, sans-serif; max-width: 1100px; margin: 30px auto; padding: 20px; color: #fff;">
    <h2 style="text-align: center; color: #3498db; font-weight: bold; margin-bottom: 30px;">
      🛡️ Validación de Formularios Avanzada (Práctica 08)
    </h2>

    <div v-if="notificacion.visible" :style="{
      background: notificacion.error ? '#e74c3c' : '#2ecc71',
      color: '#fff', padding: '15px', borderRadius: '6px', marginBottom: '20px', 
      textAlign: 'center', fontWeight: 'bold', border: '1px solid rgba(0,0,0,0.2)'
    }">
      {{ notificacion.mensaje }}
    </div>

    <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #333;">
      <h4 style="color: #3498db; margin-top: 0;">{{ editando ? '📝 Editar Registro' : '➕ Registrar Producto Seguro' }}</h4>
      
      <form @submit.prevent="guardarProducto" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; align-items: start;">
        
        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Nombre (Mín. 3 letras)</label>
          <input v-model="form.nombre" type="text" style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
          <span v-if="errores.nombre" style="color: #e74c3c; font-size: 11px; display: block; margin-top: 4px;">⚠️ {{ errores.nombre }}</span>
        </div>

        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Descripción (Mín. 10 letras)</label>
          <input v-model="form.descripcion" type="text" style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
          <span v-if="errores.descripcion" style="color: #e74c3c; font-size: 11px; display: block; margin-top: 4px;">⚠️ {{ errores.descripcion }}</span>
        </div>

        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Precio (Mín. $1.00)</label>
          <input v-model="form.precio" type="number" step="0.01" style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
          <span v-if="errores.precio" style="color: #e74c3c; font-size: 11px; display: block; margin-top: 4px;">⚠️ {{ errores.precio }}</span>
        </div>

        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Categoría</label>
          <select v-model="form.categoria_id" style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
            <option value="1">1 - Electrónica</option>
            <option value="2">2 - Ropa y Calzado</option>
            <option value="99">99 - Inexistente (Probar error)</option>
          </select>
        </div>

        <div style="align-self: end;">
          <button type="submit" style="background: #3498db; color:#fff; font-weight:bold; border:none; padding:10px 15px; border-radius:4px; cursor:pointer; width:100%;">
            {{ editando ? 'Actualizar Datos' : 'Validar e Insertar' }}
          </button>
        </div>
      </form>
    </div>

    <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333;">
      <h4 style="color: #3498db; margin-top: 0;">📋 Registros Validados</h4>
      <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
          <tr style="border-bottom: 2px solid #333; color: #3498db;">
            <th style="padding: 10px;">ID</th>
            <th style="padding: 10px;">Nombre</th>
            <th style="padding: 10px;">Descripción</th>
            <th style="padding: 10px;">Precio</th>
            <th style="padding: 10px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="prod in productos" :key="prod.id" style="border-bottom: 1px solid #222;">
            <td style="padding: 10px;">{{ prod.id }}</td>
            <td style="padding: 10px; font-weight: bold;">{{ prod.nombre }}</td>
            <td style="padding: 10px; color: #aaa;">{{ prod.descripcion }}</td>
            <td style="padding: 10px; color: #2ecc71;">${{ prod.precio }}</td>
            <td style="padding: 10px;">
              <button style="background: #2ecc71; color: white; border: none; padding: 5px 10px; margin-right: 5px; border-radius: 4px; cursor: pointer;" @click="seleccionarEditar(prod)">Editar</button>
              <button style="background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" @click="eliminarProducto(prod.id)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const productos = ref([])
const editando = ref(false)
const idActual = ref(null)

const form = ref({ nombre: '', descripcion: '', precio: '', categoria_id: '1' })
const errores = ref({ nombre: '', descripcion: '', precio: '' })
const notificacion = ref({ visible: false, mensaje: '', error: false })

const mostrarAlerta = (msg, esError = false) => {
  notificacion.value = { visible: true, mensaje: msg, error: esError }
  setTimeout(() => { notificacion.value.visible = false }, 4000)
}

const validarFrontend = () => {
  let valido = true
  errores.value = { nombre: '', descripcion: '', precio: '' }

  if (!form.value.nombre || form.value.nombre.length < 3) {
    errores.value.nombre = 'El nombre requiere mínimo 3 caracteres (Frontend).'
    valido = false
  }
  if (!form.value.descripcion || form.value.descripcion.length < 10) {
    errores.value.descripcion = 'La descripción requiere mínimo 10 letras (Frontend).'
    valido = false
  }
  if (!form.value.precio || form.value.precio <= 0) {
    errores.value.precio = 'El precio debe ser un número positivo (Frontend).'
    valido = false
  }
  return valido
}

const cargarProductos = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = res.data
  } catch (error) {
    productos.value = [
      { id: 1, nombre: 'Monitor Gamer', descripcion: 'Monitor curvo 144Hz para desarrollo.', precio: 3500, categoria_id: 1 }
    ]
  }
}

const guardarProducto = async () => {
  if (!validarFrontend()) {
    mostrarAlerta('Por favor, corrige los errores del formulario.', true)
    return
  }

  try {
    if (editando.value) {
      const res = await axios.put(`http://127.0.0.1:8000/api/productos/${idActual.value}`, form.value)
      mostrarAlerta(res.data.message || 'Actualizado correctamente')
      editando.value = false
    } else {
      const res = await axios.post('http://127.0.0.1:8000/api/productos', form.value)
      mostrarAlerta(res.data.message || 'Registrado con éxito')
    }
    limpiar()
    cargarProductos()
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const apiErrors = error.response.data.errors
      if (apiErrors.nombre) errores.value.nombre = apiErrors.nombre[0]
      if (apiErrors.descripcion) errores.value.descripcion = apiErrors.descripcion[0]
      if (apiErrors.precio) errores.value.precio = apiErrors.precio[0]
      mostrarAlerta('Error de validación del Servidor (Laravel).', true)
    } else {
      mostrarAlerta('Error de conexión con el backend, se simuló localmente.', false)
      if (!editando.value) productos.value.push({ id: productos.value.length + 1, ...form.value })
      limpiar()
    }
  }
}

const seleccionarEditar = (prod) => {
  editando.value = true
  idActual.value = prod.id
  form.value = { nombre: prod.nombre, descripcion: prod.descripcion, precio: prod.precio, categoria_id: prod.categoria_id }
}

const eliminarProducto = async (id) => {
  if (confirm("¿Seguro que deseas eliminar este producto?")) {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/productos/${id}`)
      mostrarAlerta('Registro borrado con éxito.')
      cargarProductos()
    } catch (error) {
      productos.value = productos.value.filter(p => p.id !== id)
    }
  }
}

const limpiar = () => {
  form.value = { nombre: '', descripcion: '', precio: '', categoria_id: '1' }
  errores.value = { nombre: '', descripcion: '', precio: '' }
}

onMounted(() => { cargarProductos() })
</script>