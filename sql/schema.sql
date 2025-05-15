CREATE DATABASE IF NOT EXISTS rifasdesenvolvimento DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE rifasdesenvolvimento;

CREATE TABLE rifas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    status ENUM('ABERTA', 'FINALIZADA') NOT NULL DEFAULT 'ABERTA',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);

CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rifa_id INT NOT NULL,
    quantidade INT NOT NULL,
    codigo_pix VARCHAR(100),
    status_pagamento ENUM('PENDENTE', 'PAGO') DEFAULT 'PENDENTE',
    data_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (rifa_id) REFERENCES rifas(id)
);

CREATE TABLE cotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    numero_cota VARCHAR(6) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id)
);

CREATE INDEX idx_cpf ON usuarios(cpf);
CREATE INDEX idx_numero_cota ON cotas(numero_cota);


ALTER TABLE rifas ADD preco DECIMAL(10,2) NOT NULL DEFAULT 10.00;


ALTER TABLE rifas ADD COLUMN preco DECIMAL(10,2) NOT NULL DEFAULT 10.00;
