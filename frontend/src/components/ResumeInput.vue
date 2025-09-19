<script setup>
import { ref } from 'vue'
import axios from 'axios'

// Referência para o input de arquivo
const fileInput = ref(null)

// Estado do nome do arquivo
const fileName = ref('None')

// Resultado da análise
const analysis = ref('')

// Loading (quando a API estiver processando)
const loading = ref(false)

// Abrir seletor de arquivo
const resumeUpload = () => {
  fileInput.value.click()
}

// Atualizar nome do arquivo selecionado
const updateFileName = () => {
  if (fileInput.value.files.length) {
    fileName.value = fileInput.value.files[0].name
  } else {
    fileName.value = 'None'
  }
}

// Enviar currículo para análise
const analyzeResume = async () => {
  if (!fileInput.value.files.length) {
    alert('Please select a file first.')
    return
  }

  const formData = new FormData()
  formData.append('resume', fileInput.value.files[0])

  loading.value = true
  analysis.value = '' // limpa resultado anterior

  try {
    const response = await axios.post('http://localhost:8000/analyze', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    console.log(response.data)
    analysis.value = response.data.analysis
  } catch (error) {
    console.error(error)
    alert('Error analyzing resume. Check console.')
  } finally {
    loading.value = false
  }
}
</script>



<template>
  <div class="flex min-h-screen justify-center items-center">
    <div class="flex flex-col space-y-6 rounded-lg bg-gray-900/80 p-8 text-white font-bold shadow-xl backdrop-blur-md w-full max-w-2xl">

      <!-- Logo -->
      <div class="flex justify-center">
        <img src="../assets/logo.png" alt="CS50 Logo" class="h-28" />
      </div>

      <!-- Título -->
      <h1 class="text-center text-2xl">CS50 Resume Analyzer</h1>

      <!-- Upload -->
      <div class="flex flex-col items-center space-y-2">
        <button
          @click="resumeUpload"
          class="rounded-lg bg-blue-500 px-4 py-2 hover:bg-blue-600 transition duration-200"
        >
          Select a file
        </button>
        <input
          type="file"
          class="hidden"
          ref="fileInput"
          @change="updateFileName"
        />
        <p class="text-sm">Selected File: <span class="text-green-400">{{ fileName }}</span></p>
      </div>

      <!-- Botão de Analisar -->
      <div class="flex justify-center">
        <button
          @click="analyzeResume"
          class="rounded-lg bg-green-500 px-6 py-2 hover:bg-green-600 transition duration-200"
        >
          Analyze
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-yellow-400 text-center">Analyzing...</div>

      <!-- Resultado -->
        <div 
        class="prose prose-invert max-w-none text-left"
        v-html="analysis">
        </div>

    </div>
  </div>
</template>