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

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Buscar aluno para excluir a imagem se for local
        $stmt = $pdo->prepare('SELECT * FROM alunos WHERE id = ?');
        $stmt->execute([$id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($aluno && !filter_var($aluno['foto'], FILTER_VALIDATE_URL) && $aluno['foto'] !== 'placeholder.jpg') {
            // Excluir a imagem local
            @unlink('uploads/' . $aluno['foto']);
        }
        
        // Excluir o aluno do banco de dados
        $stmt = $pdo->prepare('DELETE FROM alunos WHERE id = ?');
        if ($stmt->execute([$id])) {
            header('Location: alunos.php?sucesso=1');
            exit;
        } else {
            header('Location: alunos.php?erro=1');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: alunos.php?erro=1');
        exit;
    }
} else {
    header('Location: alunos.php');
    exit;
}
?>