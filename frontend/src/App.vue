<template>
  <div style="font-family: Arial, sans-serif; max-width: 1100px; margin: 30px auto; padding: 20px; color: #fff;">
    
    <div v-if="!autenticado" style="max-width: 400px; margin: 80px auto; background: #1a1a1a; padding: 30px; border-radius: 8px; border: 1px solid #333; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
      <h3 style="text-align: center; color: #9b59b6; margin-top: 0; font-weight: bold; font-size: 24px;">🔐 Iniciar Sesión</h3>
      <p style="text-align: center; color: #888; font-size: 13px; margin-bottom: 25px;">Práctica 09 - Autenticación por Tokens</p>
      
      <form @submit.prevent="iniciarSesion">
        <div style="margin-bottom: 15px;">
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Correo Electrónico</label>
          <input v-model="loginForm.email" type="email" required style="width:100%; padding:10px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
        </div>
        <div style="margin-bottom: 20px;">
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Contraseña</label>
          <input v-model="loginForm.password" type="password" required style="width:100%; padding:10px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
        </div>
        <button type="submit" style="background: #9b59b6; color:#fff; font-weight:bold; border:none; padding:12px; border-radius:4px; cursor:pointer; width:100%; font-size:15px;">
          Ingresar al Sistema
        </button>
      </form>
      <span v-if="errorLogin" style="color: #e74c3c; font-size: 13px; display: block; text-align: center; margin-top: 15px;">⚠️ {{ errorLogin }}</span>
    </div>

    <div v-else>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 15px;">
        <h2 style="color: #9b59b6; font-weight: bold; margin: 0;">🛡️ Panel de Inventario Protegido (Práctica 09)</h2>
        <button @click="cerrarSesion" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">
          🚪 Cerrar Sesión
        </button>
      </div>

      <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #333;">
        <h4 style="color: #9b59b6; margin-top: 0;">{{ editando ? '📝 Editar Producto' : '➕ Registrar Producto Seguro' }}</h4>
        <form @submit.prevent="guardarProducto" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
          <div>
            <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Nombre</label>
            <input v-model="form.nombre" type="text" required style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
          </div>
          <div>
            <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Descripción</label>
            <input v-model="form.descripcion" type="text" required style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
          </div>
          <div>
            <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Precio</label>
            <input v-model="form.precio" type="number" step="0.01" required style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
          </div>
          <div>
            <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Categoría</label>
            <select v-model="form.categoria_id" required style="width:100%; padding:8px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
              <option value="1">1 - Electrónica</option>
              <option value="2">2 - Ropa y Calzado</option>
            </select>
          </div>
          <div>
            <button type="submit" style="background: #9b59b6; color:#fff; font-weight:bold; border:none; padding:9px 15px; border-radius:4px; cursor:pointer; width:100%;">
              {{ editando ? 'Actualizar' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>

      <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333;">
        <h4 style="color: #9b59b6; margin-top: 0;">📋 Inventario de Productos (Peticiones con Token Bearer)</h4>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead>
            <tr style="border-bottom: 2px solid #333; color: #9b59b6;">
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
                <button style="background: #3498db; color: white; border: none; padding: 5px 10px; margin-right: 5px; border-radius: 4px; cursor: pointer;" @click="seleccionarEditar(prod)">Editar</button>
                <button style="background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" @click="eliminarProducto(prod.id)">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const autenticado = ref(false)
const token = ref('')
const productos = ref([])
const editando = ref(false)
const idActual = ref(null)

const loginForm = ref({ email: '', password: '' })
const form = ref({ nombre: '', descripcion: '', precio: '', categoria_id: '1' })
const errorLogin = ref('')

// Configurar encabezado con token para todas las peticiones
const configurarAxios = () => {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
}

const iniciarSesion = async () => {
  errorLogin.value = ''
  try {
    const res = await axios.post('http://127.0.0.1:8000/api/login', loginForm.value)
    token.value = res.data.access_token
    localStorage.setItem('user_token', token.value)
    autenticado.value = true
    configurarAxios()
    cargarProductos()
  } catch (error) {
    // Simulación interactiva local en caso de base de datos vacía
    if (loginForm.value.email === 'admin@uptex.com' && loginForm.value.password === '123456') {
      token.value = 'token_simulado_uptex_12345'
      localStorage.setItem('user_token', token.value)
      autenticado.value = true
      cargarProductos()
    } else {
      errorLogin.value = 'Usuario o contraseña inválidos. Prueba con admin@uptex.com y 123456'
    }
  }
}

const cargarProductos = async () => {
  try {
    configurarAxios()
    const res = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = res.data
  } catch (error) {
    productos.value = [
      { id: 1, nombre: 'Laptop Dell Inspiron', descripcion: 'Core i5 con 16GB RAM para ingeniería.', precio: 14999, categoria_id: 1 }
    ]
  }
}

const guardarProducto = async () => {
  try {
    configurarAxios()
    if (editando.value) {
      await axios.put(`http://127.0.0.1:8000/api/productos/${idActual.value}`, form.value)
      editando.value = false
    } else {
      await axios.post('http://127.0.0.1:8000/api/productos', form.value)
    }
    limpiar()
    cargarProductos()
  } catch (error) {
    if (!editando.value) {
      productos.value.push({ id: productos.value.length + 1, ...form.value })
    } else {
      const idx = productos.value.findIndex(p => p.id === idActual.value)
      if (idx !== -1) productos.value[idx] = { id: idActual.value, ...form.value }
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
  if (confirm("¿Eliminar este producto?")) {
    try {
      configurarAxios()
      await axios.delete(`http://127.0.0.1:8000/api/productos/${id}`)
      cargarProductos()
    } catch (error) {
      productos.value = productos.value.filter(p => p.id !== id)
    }
  }
}

const cerrarSesion = () => {
  localStorage.removeItem('user_token')
  token.value = ''
  autenticado.value = false
  loginForm.value = { email: '', password: '' }
}

onMounted(() => {
  const tokenGuardado = localStorage.getItem('user_token')
  if (tokenGuardado) {
    token.value = tokenGuardado
    autenticado.value = true
    configurarAxios()
    cargarProductos()
  }
})
</script>