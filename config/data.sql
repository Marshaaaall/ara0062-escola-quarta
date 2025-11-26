CREATE DATABASE IF NOT EXISTS escola_taekwondo;
USE escola_taekwondo;

CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    foto VARCHAR(500) NOT NULL DEFAULT 'placeholder.jpg',
    nome VARCHAR(100) NOT NULL,
    idade INT NOT NULL,
    faixa VARCHAR(50) NOT NULL,
    tempo VARCHAR(50) NOT NULL
);

INSERT INTO alunos (foto, nome, idade, faixa, tempo) VALUES
('https://images.unsplash.com/photo-1560272564-c83b66b1ad12?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', 'Carlos Silva', 15, 'Verde', '2 anos'),
('https://images.unsplash.com/photo-1599058917765-a780eda07a3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', 'Ana Costa', 12, 'Amarela', '1 ano'),
('https://images.unsplash.com/photo-1594381898411-846e7d193883?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', 'Mariana Oliveira', 17, 'Azul', '3 anos'),
('https://images.unsplash.com/photo-1599058917212-d750089bc87e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', 'João Santos', 14, 'Verde', '1.5 anos'),
('https://images.unsplash.com/photo-1599058917765-a780eda07a3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', 'Pedro Alves', 16, 'Vermelha', '4 anos');