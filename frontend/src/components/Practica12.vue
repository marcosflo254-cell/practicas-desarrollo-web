<template>
  <div style="color: white; padding: 50px;">
    <h1>¡Hola, esta es la Práctica 12!</h1>
    <p>Si ves esto, el componente ya cargó.</p>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import '../echo.js';

onMounted(() => {
    console.log("Componente cargado correctamente");
});
</script>