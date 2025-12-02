DROP DATABASE IF EXISTS helpdesk;
CREATE DATABASE helpdesk;
USE helpdesk;

-- ===========================
--  TABELA DE TÉCNICOS
-- ===========================
CREATE TABLE tecnicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

INSERT INTO tecnicos (nome) VALUES
('Marcos Silva'),
('Patrícia Mendes'),
('José Oliveira'),
('Carla Santos'),
('Rafael Almeida'),
('Thiago Moreira');

-- ===========================
--  TABELA DE CATEGORIAS
-- ===========================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

INSERT INTO categorias (nome) VALUES
('Hardware'),
('Software'),
('Rede'),
('Impressoras'),
('Sistemas Internos'),
('Backup'),
('Acessos e Permissões');

-- ===========================
--  TABELA DE PRIORIDADES
-- ===========================
CREATE TABLE prioridades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

INSERT INTO prioridades (nome) VALUES
('Baixa'),
('Média'),
('Alta'),
('Crítica');

-- ===========================
--  TABELA DE CHAMADOS
-- ===========================
CREATE TABLE chamados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    tecnico_id INT,
    categoria_id INT,
    prioridade_id INT,
    status ENUM('Pendente','Em andamento','Concluído','Cancelado') DEFAULT 'Pendente',
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_conclusao DATETIME NULL,
    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (prioridade_id) REFERENCES prioridades(id)
);

-- ===========================
--  INSERÇÃO ROBUSTA EM CHAMADOS
-- ===========================

INSERT INTO chamados (titulo, descricao, tecnico_id, categoria_id, prioridade_id, status, data_abertura, data_conclusao) VALUES
('Computador não liga', 'Usuário relata que o computador não liga após queda de energia.', 1, 1, 3, 'Pendente', '2025-01-12 08:20', NULL),
('Erro ao abrir sistema financeiro', 'Tela trava ao tentar acessar módulo financeiro.', 2, 2, 2, 'Em andamento', '2025-02-04 14:13', NULL),
('Rede instável no setor administrativo', 'Queda intermitente de conexão.', 3, 3, 4, 'Pendente', '2025-03-18 10:05', NULL),
('Impressora não imprime', 'A impressora do RH está puxando várias folhas juntas.', 4, 4, 1, 'Concluído', '2025-04-10 09:44', '2025-04-10 11:22'),
('Usuário sem acesso ao e-mail', 'Senha redefinida mas acesso continua negado.', 5, 7, 2, 'Cancelado', '2025-05-01 13:22', NULL),
('Falha no backup diário', 'Falha detectada nas rotinas automáticas.', 6, 6, 4, 'Em andamento', '2025-05-12 06:58', NULL),
('Lentidão geral nos computadores', 'Diversos usuários relatam lentidão extrema.', 1, 2, 3, 'Pendente', '2025-05-15 11:45', NULL),
('Erro de permissão em pasta compartilhada', 'Usuário perdeu acesso após troca de setor.', 5, 7, 2, 'Concluído', '2025-06-01 09:10', '2025-06-01 10:00'),
('Sistema interno travando', 'Sistema trava ao salvar informações.', 2, 5, 3, 'Em andamento', '2025-06-20 15:30', NULL),
('Monitor apresentando linhas horizontais', 'Possível defeito físico.', 4, 1, 2, 'Pendente', '2025-07-02 08:50', NULL),
('Queda total na rede', 'Sem conexão no prédio inteiro.', 3, 3, 4, 'Concluído', '2025-07-10 07:40', '2025-07-10 09:10'),
('Erro em atualizações do Windows', 'Máquina reinicia sozinha.', 6, 2, 1, 'Cancelado', '2025-07-15 16:25', NULL),
('Problema no servidor interno', 'Servidor não responde a comandos.', 1, 5, 4, 'Em andamento', '2025-08-01 03:15', NULL),
('Impressão desalinhada', 'Impressão saindo torta em relatórios.', 4, 4, 1, 'Concluído', '2025-08-10 12:50', '2025-08-10 14:05'),
('Usuário sem permissão para software X', 'Erro de privilégios.', 5, 7, 2, 'Pendente', '2025-08-15 09:33', NULL),
('Computador aquecendo muito', 'Máquina desligando por superaquecimento.', 1, 1, 3, 'Em andamento', '2025-09-02 17:20', NULL),
('VPN desconectando', 'VPN cai a cada 5 minutos.', 3, 3, 2, 'Pendente', '2025-09-12 10:12', NULL),
('Sistema interno não salva registros', 'Erro 500 ao salvar.', 2, 5, 3, 'Concluído', '2025-09-18 11:55', '2025-09-18 13:30'),
('Fonte do PC queimada', 'PC não liga após estalo.', 4, 1, 4, 'Cancelado', '2025-09-22 07:40', NULL),
('Lentidão no servidor de arquivos', 'Demora ao acessar arquivos.', 6, 6, 3, 'Em andamento', '2025-10-02 11:10', NULL);