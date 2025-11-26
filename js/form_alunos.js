
document.addEventListener('DOMContentLoaded', function() {
    initializeFormHandlers();
    setupImageUploadSystem();
});

function initializeFormHandlers() {
    // Configurar validação de formulário
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showMessage('Por favor, preencha todos os campos obrigatórios corretamente.', 'error');
            }
        });
    });
}

function setupImageUploadSystem() {
    // Configurar sistema de upload de imagens
    const urlInput = document.getElementById('foto_url');
    const fileInput = document.getElementById('foto_upload');
    
    if (urlInput) {
        urlInput.addEventListener('input', function() {
            previewUrlImage(this.value);
            highlightActiveOption('url');
        });
        
        // Mostrar instruções ao focar no campo de URL
        urlInput.addEventListener('focus', showUrlInstructions);
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            previewUploadImage(this);
            highlightActiveOption('upload');
        });
    }
    
    // Configurar drag and drop para upload de arquivos
    setupDragAndDrop();
}

function previewUrlImage(url) {
    const preview = document.getElementById('preview');
    if (url && isValidImageUrl(url)) {
        preview.src = url;
        preview.style.display = 'block';
        
        // Limpar o input de upload se URL for preenchida
        const fileInput = document.getElementById('foto_upload');
        if (fileInput) fileInput.value = '';
        
        showMessage('URL da imagem carregada com sucesso!', 'success');
    } else if (url) {
        preview.style.display = 'none';
        showMessage('URL de imagem inválida. Verifique o link.', 'error');
    } else {
        preview.style.display = 'none';
    }
}

function previewUploadImage(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validar tipo de arquivo
        if (!isValidImageFile(file)) {
            showMessage('Formato de arquivo não permitido. Use JPG, PNG, GIF ou WebP.', 'error');
            input.value = '';
            return;
        }
        
        // Validar tamanho do arquivo (máx 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showMessage('Arquivo muito grande. O tamanho máximo é 5MB.', 'error');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            showMessage('Imagem carregada com sucesso!', 'success');
        }
        
        reader.onerror = function() {
            showMessage('Erro ao carregar a imagem. Tente novamente.', 'error');
        }
        
        reader.readAsDataURL(file);
        
        // Limpar o input de URL se upload for feito
        const urlInput = document.getElementById('foto_url');
        if (urlInput) urlInput.value = '';
    } else {
        preview.style.display = 'none';
    }
}

function highlightActiveOption(activeType) {
    const urlOption = document.querySelector('.upload-option:first-child');
    const uploadOption = document.querySelector('.upload-option:last-child');
    
    if (activeType === 'url') {
        urlOption.classList.add('active');
        uploadOption.classList.remove('active');
    } else if (activeType === 'upload') {
        uploadOption.classList.add('active');
        urlOption.classList.remove('active');
    } else {
        urlOption.classList.remove('active');
        uploadOption.classList.remove('active');
    }
}

function showUrlInstructions() {
    const instructions = `
        <div class="upload-instructions">
            <h4>📸 Como obter URL de imagem do Google:</h4>
            <ol>
                <li>Pesquise uma imagem no Google Images</li>
                <li>Clique na imagem desejada para ampliá-la</li>
                <li>Clique com o botão direito na imagem</li>
                <li>Selecione <strong>"Copiar endereço da imagem"</strong></li>
                <li>Cole o endereço no campo acima</li>
            </ol>
            <p><small><strong>Dica:</strong> Procure por imagens com boa qualidade e que representem bem o aluno.</small></p>
        </div>
    `;
    
    // Remover instruções anteriores
    const existingInstructions = document.querySelector('.upload-instructions');
    if (existingInstructions) {
        existingInstructions.remove();
    }
    
    // Inserir novas instruções
    const urlInput = document.getElementById('foto_url');
    if (urlInput) {
        urlInput.insertAdjacentHTML('afterend', instructions);
    }
}

function setupDragAndDrop() {
    const fileInput = document.getElementById('foto_upload');
    const uploadOption = document.querySelector('.upload-option:last-child');
    
    if (!uploadOption || !fileInput) return;
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadOption.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadOption.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadOption.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        uploadOption.classList.add('active');
        uploadOption.style.borderColor = '#2ecc71';
        uploadOption.style.backgroundColor = '#e8f5e8';
    }
    
    function unhighlight() {
        uploadOption.classList.remove('active');
        uploadOption.style.borderColor = '';
        uploadOption.style.backgroundColor = '';
    }
    
    uploadOption.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files;
            previewUploadImage(fileInput);
        }
    }
}

function isValidImageUrl(url) {
    // Verificar se é uma URL válida e se parece com uma imagem
    try {
        const urlObj = new URL(url);
        const path = urlObj.pathname.toLowerCase();
        return /\.(jpg|jpeg|png|gif|webp|bmp)$/i.test(path);
    } catch {
        return false;
    }
}

function isValidImageFile(file) {
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    return allowedTypes.includes(file.type);
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#e74c3c';
            isValid = false;
        } else {
            field.style.borderColor = '#27ae60';
        }
    });
    
    // Validar idade
    const idadeField = form.querySelector('input[type="number"]');
    if (idadeField && idadeField.value) {
        const idade = parseInt(idadeField.value);
        if (idade < 5 || idade > 100) {
            idadeField.style.borderColor = '#e74c3c';
            showMessage('Idade deve estar entre 5 e 100 anos.', 'error');
            isValid = false;
        }
    }
    
    return isValid;
}

function showMessage(message, type) {
    // Remover mensagens anteriores
    const existingMessages = document.querySelectorAll('.form-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Criar nova mensagem
    const messageDiv = document.createElement('div');
    messageDiv.className = `form-message ${type}`;
    messageDiv.style.cssText = `
        padding: 12px 15px;
        margin: 10px 0;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        ${type === 'success' ? 'background-color: #d5f4e6; color: #27ae60; border: 1px solid #27ae60;' : ''}
        ${type === 'error' ? 'background-color: #fadbd8; color: #e74c3c; border: 1px solid #e74c3c;' : ''}
    `;
    messageDiv.textContent = message;
    
    // Inserir mensagem no formulário
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(messageDiv, form.firstChild);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// Função para limpar o formulário
function clearForm() {
    const form = document.querySelector('form');
    if (form) {
        form.reset();
        const preview = document.getElementById('preview');
        if (preview) preview.style.display = 'none';
        
        const urlOption = document.querySelector('.upload-option:first-child');
        const uploadOption = document.querySelector('.upload-option:last-child');
        if (urlOption) urlOption.classList.remove('active');
        if (uploadOption) uploadOption.classList.remove('active');
        
        showMessage('Formulário limpo!', 'success');
    }
}

// Adicionar botão de limpar formulário se não existir
document.addEventListener('DOMContentLoaded', function() {
    const formActions = document.querySelector('.form-actions');
    if (formActions && !document.querySelector('.btn-limpar')) {
        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'btn-form btn-cancelar-form btn-limpar';
        clearButton.textContent = '🗑️ Limpar';
        clearButton.onclick = clearForm;
        formActions.appendChild(clearButton);
    }
});