<template>
  <div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: sans-serif;">
    <h1>📦 Práctica 14: Notificaciones en Tiempo Real</h1>

    <div v-if="notificacion" style="background: #28a745; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; position: fixed; top: 20px; right: 20px; z-index: 1000;">
      {{ notificacion }}
    </div>

    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr style="background: #333; color: white;">
          <th style="padding: 10px;">Cliente</th>
          <th style="padding: 10px;">Total</th>
          <th style="padding: 10px;">Estado</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in pedidos" :key="p.id" style="border-bottom: 1px solid #ddd;">
          <td style="padding: 10px;">{{ p.cliente }}</td>
          <td style="padding: 10px;">${{ p.total }}</td>
          <td style="padding: 10px; color: green; font-weight: bold;">✅ Procesado</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import './echo.js';

const pedidos = ref([]);
const notificacion = ref('');

const mostrarToast = (mensaje) => {
    notificacion.value = mensaje;
    setTimeout(() => { notificacion.value = ''; }, 5000); // Se oculta a los 5 segundos
};

onMounted(() => {
    window.Echo.channel('pedidos-canal')
        .listen('.pedido.procesado', (e) => {
            pedidos.value.unshift(e.pedido);
            mostrarToast("¡Nuevo pedido de " + e.pedido.cliente + "!");
        });
});
</script>