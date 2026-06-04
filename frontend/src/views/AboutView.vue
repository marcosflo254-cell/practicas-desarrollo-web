<template>
  <div class="catalogo-container" style="font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; padding: 20px;">
    <h2 style="text-align: center; color: #2c3e50; font-weight: bold; margin-bottom: 30px;">
      🛒 Catálogo Dinámico desde la API Laravel (Práctica 06)
    </h2>

    <div v-if="cargando" style="text-align: center; font-size: 18px; color: #7f8c8d; padding: 40px;">
      ⏳ Conectando con el servidor Laravel y cargando productos...
    </div>

    <div v-else>
      <div v-for="cat in categorias" :key="cat.id" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 40px; border-top: 4px solid #3498db;">
        <h3 style="color: #2c3e50; margin: 0 0 5px 0; font-size: 22px; font-weight: bold;">{{ cat.nombre }}</h3>
        <p style="color: #95a5a6; font-size: 14px; margin-bottom: 20px;">{{ cat.descripcion }}</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
          <div v-for="p in cat.productos" :key="p.id" style="background: #fdfdfd; padding: 18px; border-radius: 8px; border: 1px solid #eaeded; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <h5 style="margin: 0 0 10px 0; color: #34495e; font-size: 16px; font-weight: bold;">{{ p.nombre }}</h5>
              <p style="margin: 0 0 15px 0; color: #7f8c8d; font-size: 13px; line-height: 1.4;">{{ p.descripcion }}</p>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
              <span style="font-weight: bold; color: #27ae60; font-size: 18px;">${{ p.precio }}</span>
              <span style="background: #e8f4fd; color: #3498db; font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: bold;">
                FK Categoria: {{ cat.id }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const categorias = ref([])
const cargando = ref(true)

onMounted(async () => {
  try {
    // Petición HTTP real al endpoint local de Laravel con Eager Loading
    const respuesta = await axios.get('http://127.0.0.1:8000/api/categorias')
    categorias.value = respuesta.data
  } catch (error) {
    console.error("Error cargando datos de Laravel:", error)
  } finally {
    cargando.value = false
  }
})
</script>