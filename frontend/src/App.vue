<script setup>
import ResumeInput from './components/ResumeInput.vue';
import { ref, onMounted } from 'vue'
import axios from 'axios'

const response = ref('Carregando...')

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/ping')
    console.log(res.data.message)
  } catch (err) {
    response.value = 'Erro: ' + err.message
  }
})
</script>


<template>
  <div class="relative h-screen w-screen overflow-auto bg-gradient-to-tr from-purple-900 to-blue-950">

    <ResumeInput />

  </div>
</template>

