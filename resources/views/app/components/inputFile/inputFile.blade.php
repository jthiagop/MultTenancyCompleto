<style>
    /* Estilos básicos */
    .file-upload-container {
      border: 2px dashed #ccc;
      border-radius: 5px;
      padding: 16px;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.3s;
    }
    .file-upload-container:hover {
      border-color: #777;
    }

    .file-details {
      display: none; /* Oculto até que selecione o arquivo */
      background-color: #f1f3f5; /* Cor de fundo clara */
      border-radius: 5px;
      padding: 16px;
      align-items: center;
      justify-content: space-between;
    }
    .file-details.active {
      display: flex; /* Ao ativar, vira um flex container */
    }

    .file-icon {
      font-size: 1.2rem;
      margin-right: 8px;
    }

    .file-name-size {
      flex-grow: 1; /* Ocupa espaço do flex */
      font-weight: 600;
      color: #333;
      display: flex;
      align-items: center;
      overflow: hidden; /* Se o nome for muito grande */
    }
    .file-name-size span {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      max-width: 250px;
      margin-right: 8px;
    }

    .file-delete-btn {
      background: none;
      border: none;
      color: #888;
      font-size: 1.2rem;
      cursor: pointer;
      margin-right: 16px;
    }
    .file-delete-btn:hover {
      color: #dc3545;
    }
  </style>


<!-- 1. Estado inicial (borda tracejada) -->
  <div id="fileUploadArea" class="file-upload-container mb-3">
    <label for="fileInput" class="btn btn-outline-primary me-2">
      <i class="fas fa-paperclip"></i> Escolha um arquivo
    </label>
    Ou arraste-o para este espaço
    <input
      id="fileInput"
      type="file"
      accept=".xlsx,.xls,.csv"
      style="display:none"
    >
    
  </div>

  <!-- 2. Estado depois de selecionar (detalhes do arquivo) -->
  <div id="fileDetails" class="file-details mb-3">
    <div class="file-name-size">
      <i class="fas fa-paperclip file-icon"></i>
      <span id="fileName">Nome do arquivo</span>
      <small id="fileSize" class="text-muted">(35Kb)</small>
    </div>
    <!-- Botão para remover arquivo -->
    <button id="removeFileBtn" class="file-delete-btn" title="Excluir arquivo">
      <i class="fas fa-trash"></i>
    </button>
  </div>
