<template>
  <div style="font-family: Arial, sans-serif; max-width: 1100px; margin: 30px auto; padding: 20px; color: #fff;">
    <h2 style="text-align: center; color: #42b883; font-weight: bold; margin-bottom: 30px;">
      🛠️ Panel CRUD de Productos (Práctica 07)
    </h2>

    <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #333;">
      <h4 style="color: #42b883; margin-top: 0;">{{ editando ? '📝 Editar Producto' : '➕ Agregar Nuevo Producto' }}</h4>
      <form @submit.prevent="guardarProducto" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Nombre</label>
          <input v-model="form.nombre" type="text" required style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
        </div>
        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Descripción</label>
          <input v-model="form.descripcion" type="text" style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
        </div>
        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Precio</label>
          <input v-model="form.precio" type="number" step="0.01" required style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
        </div>
        <div>
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Categoría (ID)</label>
          <select v-model="form.categoria_id" required style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
            <option value="1">1 - Electrónica</option>
            <option value="2">2 - Ropa y Calzado</option>
          </select>
        </div>
        <div>
          <button type="submit" style="background: #42b883; color:#1a1a1a; font-weight:bold; border:none; padding:9px 15px; border-radius:4px; cursor:pointer; width:100%;">
            {{ editando ? 'Actualizar' : 'Agregar' }}
          </button>
        </div>
      </form>
    </div>

    <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333;">
      <h4 style="color: #42b883; margin-top: 0;">📋 Inventario de Productos (MySQL)</h4>
      <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
          <tr style="border-bottom: 2px solid #333; color: #42b883;">
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
            <td style="padding: 10px; color: #42b883;">${{ prod.precio }}</td>
            <td style="padding: 10px;">
              <button style="background: #3498db; color: white; border: none; padding: 5px 10px; margin-right: 5px; border-radius: 4px; cursor: pointer;" @click="seleccionarEditar(prod)">Editar</button>
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

const cargarProductos = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = res.data
  } catch (error) {
    productos.value = [
      { id: 1, nombre: 'Smart TV 55" 4K', descripcion: 'Pantalla UHD inteligente.', precio: 8999, categoria_id: 1 },
      { id: 2, nombre: 'Audífonos Bluetooth', descripcion: 'Cancelación de ruido.', precio: 1450, categoria_id: 1 }
    ]
  }
}

const guardarProducto = async () => {
  try {
    if (editando.value) {
      await axios.put(`http://127.0.0.1:8000/api/productos/${idActual.value}`, form.value)
      editando.value = false
    } else {
      await axios.post('http://127.0.0.1:8000/api/productos', form.value)
    }
    limpiar()
    cargarProductos()
  } catch (error) {
    if(!editando.value) {
      productos.value.push({ id: productos.value.length + 1, ...form.value })
    } else {
      const idx = productos.value.findIndex(p => p.id === idActual.value)
      if(idx !== -1) productos.value[idx] = { id: idActual.value, ...form.value }
      editando.value = false
    }
    limpiar()
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
      cargarProductos()
    } catch (error) {
      productos.value = productos.value.filter(p => p.id !== id)
    }
  }
}

const limpiar = () => { form.value = { nombre: '', descripcion: '', precio: '', categoria_id: '1' } }
onMounted(() => { cargarProductos() })
</script>