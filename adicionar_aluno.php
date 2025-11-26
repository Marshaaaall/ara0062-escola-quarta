<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'escola_taekwondo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

$erro = '';

// Criar pasta de uploads se não existir
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $idade = $_POST['idade'] ?? '';
    $faixa = $_POST['faixa'] ?? '';
    $tempo = $_POST['tempo'] ?? '';
    
    // Processar a foto (URL ou upload)
    $foto = 'placeholder.jpg'; // padrão
    
    // Se foi enviada uma URL
    if (!empty($_POST['foto_url'])) {
        $foto = $_POST['foto_url'];
    }
    
    // Se foi feito upload de arquivo
    if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === UPLOAD_ERR_OK) {
        $arquivo = $_FILES['foto_upload'];
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($extensao), $extensoes_permitidas)) {
            // Gerar nome único para o arquivo
            $nome_arquivo = uniqid() . '_' . time() . '.' . $extensao;
            $caminho_arquivo = 'uploads/' . $nome_arquivo;
            
            if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
                $foto = $nome_arquivo;
            } else {
                $erro = "Erro ao fazer upload da imagem.";
            }
        } else {
            $erro = "Formato de arquivo não permitido. Use JPG, PNG, GIF ou WebP.";
        }
    }

    // Validação básica
    if (empty($erro)) {
        if (!empty($nome) && !empty($idade) && !empty($faixa) && !empty($tempo)) {
            try {
                $stmt = $pdo->prepare('INSERT INTO alunos (foto, nome, idade, faixa, tempo) VALUES (?, ?, ?, ?, ?)');
                if ($stmt->execute([$foto, $nome, $idade, $faixa, $tempo])) {
                    header('Location: alunos.php?sucesso=1');
                    exit;
                } else {
                    $erro = "Erro ao adicionar aluno.";
                }
            } catch (PDOException $e) {
                $erro = "Erro ao adicionar aluno: " . $e->getMessage();
            }
        } else {
            $erro = "Por favor, preencha todos os campos obrigatórios.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Aluno - Escola de Taekwondo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/alunos.css">
    <link rel="stylesheet" href="css/form-alunos.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="alunos.php">Alunos</a></li>
                    <li><a href="tabela.html">Faixas</a></li>
                    <li><a href="sac.html">SAC</a></li>
                    <li><a href="integrantes.html">Integrantes</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="content-section">
            <h1>Adicionar Aluno</h1>

            <?php if (!empty($erro)): ?>
                <div class="erro"><?= $erro ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="form-container">
                <div class="form-group">
                    <h3>Escolha como adicionar a foto:</h3>
                    <div class="upload-options">
                        <div class="upload-option">
                            <h3> Usar URL do Google</h3>
                            <p>Cole a URL de uma imagem da web</p>
                            <input type="url" id="foto_url" name="foto_url" 
                                   class="url-input" 
                                   placeholder="https://exemplo.com/imagem.jpg">
                            <small>Formatos: JPG, PNG, GIF, WebP</small>
                        </div>
                        
                        <div class="upload-option">
                            <h3> Fazer Upload</h3>
                            <p>Envie uma imagem do seu computador</p>
                            <input type="file" id="foto_upload" name="foto_upload" 
                                   class="file-input" 
                                   accept="image/*">
                            <small>Formatos: JPG, PNG, GIF, WebP (máx. 5MB)</small>
                        </div>
                    </div>
                    
                    <div class="preview-container">
                        <img id="preview" class="preview-image" alt="Pré-visualização">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nome">Nome completo:</label>
                    <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="idade">Idade:</label>
                    <input type="number" id="idade" name="idade" min="5" max="100" required value="<?= htmlspecialchars($_POST['idade'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="faixa">Faixa:</label>
                    <select id="faixa" name="faixa" required>
                        <option value="">Selecione a faixa</option>
                        <option value="Branca" <?= ($_POST['faixa'] ?? '') == 'Branca' ? 'selected' : '' ?>>Branca</option>
                        <option value="Amarela" <?= ($_POST['faixa'] ?? '') == 'Amarela' ? 'selected' : '' ?>>Amarela</option>
                        <option value="Verde" <?= ($_POST['faixa'] ?? '') == 'Verde' ? 'selected' : '' ?>>Verde</option>
                        <option value="Azul" <?= ($_POST['faixa'] ?? '') == 'Azul' ? 'selected' : '' ?>>Azul</option>
                        <option value="Vermelha" <?= ($_POST['faixa'] ?? '') == 'Vermelha' ? 'selected' : '' ?>>Vermelha</option>
                        <option value="Preta" <?= ($_POST['faixa'] ?? '') == 'Preta' ? 'selected' : '' ?>>Preta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tempo">Tempo de Prática:</label>
                    <input type="text" id="tempo" name="tempo" placeholder="ex: 2 anos" required value="<?= htmlspecialchars($_POST['tempo'] ?? '') ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-form btn-adicionar-form"> Adicionar Aluno</button>
                    <a href="alunos.php" class="btn-form btn-cancelar-form"> Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Escola de Taekwondo - Todos os direitos reservados</p>
        </div>
    </footer>

    <script src="js/form-alunos.js"></script>
</body>
</html>