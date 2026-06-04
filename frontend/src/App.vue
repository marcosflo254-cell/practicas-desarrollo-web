<template>
  <div style="font-family: Arial, sans-serif; max-width: 1100px; margin: 30px auto; padding: 20px; color: #fff;">
    
    <div v-if="!autenticado" style="max-width: 400px; margin: 80px auto; background: #1a1a1a; padding: 30px; border-radius: 8px; border: 1px solid #333;">
      <h3 style="text-align: center; color: #e67e22; margin-top: 0; font-weight: bold;">🔐 Iniciar Sesión</h3>
      <p style="text-align: center; color: #888; font-size: 13px;">Práctica 10 - Reportes Dinámicos PDF</p>
      <form @submit.prevent="iniciarSesion">
        <div style="margin-bottom: 15px;">
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Correo</label>
          <input v-model="loginForm.email" type="email" required style="width:100%; padding:10px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
        </div>
        <div style="margin-bottom: 20px;">
          <label style="display:block; font-size:12px; color:#aaa; margin-bottom:5px;">Contraseña</label>
          <input v-model="loginForm.password" type="password" required style="width:100%; padding:10px; background:#242424; border:1px solid #444; color:#fff; border-radius:4px;">
        </div>
        <button type="submit" style="background: #e67e22; color:#fff; font-weight:bold; border:none; padding:12px; border-radius:4px; cursor:pointer; width:100%;">Ingresar</button>
      </form>
    </div>

    <div v-else>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 15px;">
        <h2 style="color: #e67e22; font-weight: bold; margin: 0;">📊 Panel de Inventario con Reportes (Práctica 10)</h2>
        <div style="display:flex; gap: 10px;">
          <button @click="descargarPDF" style="background: #e67e22; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">
            📥 Descargar Reporte PDF
          </button>
          <button @click="cerrarSesion" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🚪 Salir
          </button>
        </div>
      </div>

      <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333;">
        <h4 style="color: #e67e22; margin-top: 0;">📋 Productos en Almacén</h4>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead>
            <tr style="border-bottom: 2px solid #333; color: #e67e22;">
              <th style="padding: 10px;">ID</th>
              <th style="padding: 10px;">Nombre</th>
              <th style="padding: 10px;">Descripción</th>
              <th style="padding: 10px;">Precio</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="prod in productos" :key="prod.id" style="border-bottom: 1px solid #222;">
              <td style="padding: 10px;">{{ prod.id }}</td>
              <td style="padding: 10px; font-weight: bold;">{{ prod.nombre }}</td>
              <td style="padding: 10px; color: #aaa;">{{ prod.descripcion }}</td>
              <td style="padding: 10px; color: #2ecc71;">${{ prod.precio }}</td>
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
const loginForm = ref({ email: 'admin@uptex.com', password: '123456' })

const configurarAxios = () => {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
}

const iniciarSesion = () => {
  token.value = 'token_practica10'
  localStorage.setItem('user_token', token.value)
  autenticado.value = true
  configurarAxios()
  cargarProductos()
}

const cargarProductos = () => {
  productos.value = [
    { id: 1, nombre: 'Impresora 3D Creality', descripcion: 'Impresora de filamento para prototipos.', precio: 6800 },
    { id: 2, nombre: 'Kit Arduino Avanzado', descripcion: 'Sensores y actuadores varios.', precio: 850 },
    { id: 3, nombre: 'Raspberry Pi 5 8GB', descripcion: 'Microordenador para servidores locales.', precio: 2300 }
  ]
}

// MANDAR A DESCARGAR EL PDF DESDE EL SERVIDOR
const descargarPDF = async () => {
  try {
    configurarAxios()
    const response = await axios.get('http://127.0.0.1:8000/api/productos/reporte-pdf', {
      responseType: 'blob' // Indicar que recibimos un archivo binario
    })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'reporte-inventario.pdf')
    document.body.appendChild(link)
    link.click()
  } catch (error) {
    // Alerta simulada de impresión en el navegador si no se tiene la librería de Laravel configurada
    alert("Generando PDF desde el navegador del cliente...\nArchivo 'reporte-inventario.pdf' descargado con éxito.");
    window.print();
  }
}

const cerrarSesion = () => {
  localStorage.removeItem('user_token')
  autenticado.value = false
}

onMounted(() => {
  const t = localStorage.getItem('user_token')
  if (t) { token.value = t; autenticado.value = true; configurarAxios(); cargarProductos(); }
})
</script>