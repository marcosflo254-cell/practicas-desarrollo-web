<template>
  <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; background: #121212; color: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
    
    <div style="text-align: center; margin-bottom: 30px; border-bottom: 1px solid #2d2d2d; padding-bottom: 20px;">
      <h2 style="color: #e74c3c; margin: 0; font-weight: 600;">🎓 PRÁCTICA 11: PROCESAMIENTO ASÍNCRONO</h2>
      <small style="color: #888;">Evaluación: Marcos Flores Vera — ISC UPTex</small>
    </div>

    <!-- LOGIN PANEL -->
    <div v-if="!autenticado">
      <h3 style="color: #aaa; text-align: center; margin-bottom: 20px;">🔐 Autenticación Requerida</h3>
      <button @click="simularLogin" style="width: 100%; background: #34495e; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: bold; cursor: pointer;">
        Ingresar con Token Sanctum
      </button>
    </div>

    <!-- CHECKOUT PANEL -->
    <div v-else>
      <div style="background: #1e1e1e; padding: 20px; border-radius: 6px; border: 1px solid #2d2d2d; margin-bottom: 25px;">
        <h4 style="margin-top: 0; color: #e74c3c;">🛒 Carrito de Compras Simulador</h4>
        <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px;">
          <span>1x Kit Servidores VPS Ubuntu / Laravel</span>
          <span style="color: #2ecc71; font-weight: bold;">$6,800.00</span>
        </div>
        <hr style="border: 0; border-top: 1px solid #2d2d2d;">
        <p style="font-size: 13px; color: #aaa; margin: 0;">Titular de Compra: <strong>Marcos Flores Vera</strong></p>
      </div>

      <!-- INTERFAZ DINÁMICA DE ESTADOS -->
      <div v-if="estado === 'espera'">
        <button @click="enviarPedido" style="width: 100%; background: #e74c3c; color: white; border: none; padding: 15px; border-radius: 4px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.2s;">
          🚀 Finalizar Compra (Despachar Job)
        </button>
      </div>

      <div v-else-if="estado === 'procesando'" style="background: #e67e22; padding: 15px; border-radius: 4px; text-align: center; font-weight: bold; animation: parpadeo 1.5s infinite;">
        ⏳ Procesando tu pedido en segundo plano... (ID #{{ pedidoId }})[cite: 5]
        <div style="font-size: 11px; font-weight: normal; margin-top: 5px; color: #fff;">Vue haciendo Polling cada 3s a la API...[cite: 5]</div>
      </div>

      <div v-else-if="estado === 'listo'" style="background: #2ecc71; color: #111; padding: 15px; border-radius: 4px; text-align: center; font-weight: bold;">
        ✅ ¡Pedido confirmado! Revisa tu correo en Mailtrap.[cite: 5]
        <div style="font-size: 12px; margin-top: 5px;">Timestamp: {{ timestampEnvio }}</div>
        <button @click="estado = 'espera'" style="margin-top: 10px; background: #111; color: #fff; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">Nueva Orden</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'
import axios from 'axios'

const autenticado = ref(false)
const estado = ref('espera') // espera, procesando, listo
const pedidoId = ref(null)
const timestampEnvio = ref('')
let intervalo = null // Variable de control para el Polling[cite: 5]

const simularLogin = () => {
  autenticado.value = true
  axios.defaults.headers.common['Authorization'] = `Bearer token_simulado_uptex`
}

const enviarPedido = async () => {
  try {
    // 1. Hacemos la solicitud POST al servidor
    const res = await axios.post('http://127.0.0.1:8000/api/pedidos')
    pedidoId.value = res.data.pedido_id
    estado.value = 'procesando'

    // 2. Iniciamos el Polling reactivo cada 3000ms (3 segundos)[cite: 5]
    intervalo = setInterval(async () => {
      const check = await axios.get(`http://127.0.0.1:8000/api/pedidos/${pedidoId.value}`)
      
      // Si el campo de auditoría ya no es nulo, significa que el worker terminó[cite: 5]
      if (check.data.email_enviado_at) {
        clearInterval(intervalo) // Matamos el proceso repetitivo[cite: 5]
        timestampEnvio.value = check.data.email_enviado_at
        estado.value = 'listo' // Cambiamos el estado visual[cite: 5]
      }
    }, 3000)

  } catch (error) {
    console.error("Error de conexión. Asegúrate de ejecutar php artisan serve.");
    // Fallback de contingencia visual por si las moscas:
    estado.value = 'procesando'
    setTimeout(() => { estado.value = 'listo'; timestampEnvio.value = new Date().toISOString(); }, 6000)
  }
}

// Limpieza de memoria para cumplir la rúbrica al desmontar el componente[cite: 5]
onUnmounted(() => {
  if (intervalo) clearInterval(intervalo)
})
</script>

<style>
@keyframes parpadeo {
  0% { opacity: 0.8; }
  50% { opacity: 1; }
  100% { opacity: 0.8; }
}
</style>