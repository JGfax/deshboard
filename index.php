<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Helpdesk - Dark Premium (Simulado)</title>
    <!-- Tailwind CSS CDN para estilização rápida e responsiva -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuração do Tailwind para o tema Dark Premium
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-bg': '#1e293b', // Slate-800
                        'dark-card': '#334155', // Slate-700
                        'dark-text': '#f1f5f9', // Slate-100
                        'accent-blue': '#3b82f6', // Blue-500
                        'accent-green': '#10b981', // Emerald-500
                        'accent-yellow': '#f59e0b', // Amber-500
                        'accent-red': '#ef4444', // Red-500
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Estilo customizado para a tabela e cores dos cards/gráficos -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Slate-900 */
            color: #f1f5f9; /* Slate-100 */
        }
        /* Estilo para a barra de rolagem da tabela */
        .table-wrapper::-webkit-scrollbar {
            height: 8px;
        }
        .table-wrapper::-webkit-scrollbar-thumb {
            background: #475569; /* Slate-600 */
            border-radius: 10px;
        }
        .table-wrapper::-webkit-scrollbar-track {
            background: #1e293b; /* Slate-800 */
        }
        /* Cor de fundo para o Chart.js */
        canvas {
            background-color: #334155; /* Slate-700 */
            padding: 1rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-dark-card shadow-lg p-4 mb-6 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="brand">
                <h1 class="text-2xl font-bold text-accent-blue">HelpDesk - Dashboard</h1>
                <p class="text-sm text-gray-400">Visão geral dos chamados (Dados Simulados)</p>
            </div>
            <div class="text-sm px-4 py-2 rounded-full bg-accent-blue/20 text-accent-blue font-semibold">
                Admin
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 pb-12">
        <!-- CARDS SUPERIORES -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total -->
            <div id="card-total" class="bg-dark-card p-6 rounded-xl shadow-xl border-b-4 border-accent-blue transition duration-300 hover:shadow-2xl">
                <div class="text-sm font-medium text-gray-400">Total de Chamados</div>
                <div class="text-4xl font-extrabold mt-1"></div>
            </div>

            <!-- Pendentes -->
            <div id="card-pendentes" class="bg-dark-card p-6 rounded-xl shadow-xl border-b-4 border-accent-yellow transition duration-300 hover:shadow-2xl">
                <div class="text-sm font-medium text-gray-400">Pendentes</div>
                <div class="text-4xl font-extrabold mt-1 text-accent-yellow"></div>
            </div>

            <!-- Em Andamento -->
            <div id="card-andamento" class="bg-dark-card p-6 rounded-xl shadow-xl border-b-4 border-accent-blue transition duration-300 hover:shadow-2xl">
                <div class="text-sm font-medium text-gray-400">Em Andamento</div>
                <div class="text-4xl font-extrabold mt-1 text-accent-blue"></div>
            </div>
            
            <!-- Concluídos -->
            <div id="card-concluidos" class="bg-dark-card p-6 rounded-xl shadow-xl border-b-4 border-accent-green transition duration-300 hover:shadow-2xl">
                <div class="text-sm font-medium text-gray-400">Concluídos</div>
                <div class="text-4xl font-extrabold mt-1 text-accent-green"></div>
            </div>
        </section>

        <!-- GRÁFICOS -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Status -->
            <div class="bg-dark-card p-6 rounded-xl shadow-xl">
                <h3 class="text-xl font-semibold mb-4 border-b border-gray-700 pb-2">Distribuição por Status</h3>
                <canvas id="pieStatus" class="h-80"></canvas>
            </div>

            <!-- Gráfico de Categorias -->
            <div class="bg-dark-card p-6 rounded-xl shadow-xl">
                <h3 class="text-xl font-semibold mb-4 border-b border-gray-700 pb-2">Chamados por Categoria</h3>
                <canvas id="barCategoria" class="h-80"></canvas>
            </div>
        </section>

        <!-- TABELA DE CHAMADOS RECENTES -->
        <section class="bg-dark-card p-6 rounded-xl shadow-xl">
            <h3 class="text-xl font-semibold mb-4 border-b border-gray-700 pb-2">Chamados mais Recentes (Top 10)</h3>
            <div class="table-wrapper overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Título</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Técnico</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Categoria</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Prioridade</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Abertura</th>
                        </tr>
                    </thead>
                    <tbody id="recent-calls-body" class="divide-y divide-gray-800 bg-dark-bg">
                        <!-- Linhas serão preenchidas por JS -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- SCRIPTS: Dados simulados e inicialização dos gráficos -->
    <script>
        // Função para formatar data (simulando a função PHP date)
        const formatDate = (isoString) => {
            try {
                const date = new Date(isoString);
                return date.toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }).replace(',', '');
            } catch (e) {
                return isoString;
            }
        };

        // ----------------------------------------------------
        // DADOS SIMULADOS (Substituem as consultas MySQL/PHP)
        // ----------------------------------------------------
        const simulatedData = {
            // Contagens dos Cards
            totalChamados: 125,
            pendentes: 30,
            emAndamento: 55,
            concluidos: 35,
            cancelados: 5,
            
            // Dados para Gráfico 1: Status (Pie Chart)
            statusData: [
                { status: 'Pendente', total: 30 },
                { status: 'Em Andamento', total: 55 },
                { status: 'Concluído', total: 35 },
                { status: 'Cancelado', total: 5 }
            ],

            // Dados para Gráfico 2: Categoria (Bar Chart)
            categoryData: [
                { category: 'Hardware', total: 40 },
                { category: 'Software', total: 35 },
                { category: 'Rede', total: 25 },
                { category: 'Acesso/Permissão', total: 20 },
                { category: 'Outros', total: 5 },
            ],

            // Dados para a Tabela: Chamados Recentes
            recentCalls: [
                { id: 101, titulo: 'Falha de login no sistema X', tecnico: 'João S.', categoria: 'Acesso/Permissão', status: 'Pendente', prioridade: 'Alta', data_abertura: '2025-12-02T10:00:00Z' },
                { id: 100, titulo: 'Troca de monitor do setor financeiro', tecnico: 'Maria T.', categoria: 'Hardware', status: 'Em Andamento', prioridade: 'Média', data_abertura: '2025-12-02T09:30:00Z' },
                { id: 99, titulo: 'Instalação de Office 365', tecnico: 'João S.', categoria: 'Software', status: 'Concluído', prioridade: 'Baixa', data_abertura: '2025-12-01T17:45:00Z' },
                { id: 98, titulo: 'Rede lenta no 3º andar', tecnico: 'Pedro H.', categoria: 'Rede', status: 'Em Andamento', prioridade: 'Alta', data_abertura: '2025-12-01T15:20:00Z' },
                { id: 97, titulo: 'Bug no relatório de vendas', tecnico: 'Maria T.', categoria: 'Software', status: 'Pendente', prioridade: 'Média', data_abertura: '2025-12-01T11:00:00Z' },
                { id: 96, titulo: 'Configuração de VPN', tecnico: 'Pedro H.', categoria: 'Rede', status: 'Concluído', prioridade: 'Baixa', data_abertura: '2025-11-30T10:00:00Z' },
                { id: 95, titulo: 'Teclado com defeito', tecnico: 'João S.', categoria: 'Hardware', status: 'Em Andamento', prioridade: 'Média', data_abertura: '2025-11-29T14:00:00Z' },
                { id: 94, titulo: 'Atualização do sistema operacional', tecnico: 'Maria T.', categoria: 'Software', status: 'Pendente', prioridade: 'Baixa', data_abertura: '2025-11-29T09:00:00Z' },
                { id: 93, titulo: 'Impressora offline', tecnico: 'Pedro H.', categoria: 'Hardware', status: 'Concluído', prioridade: 'Alta', data_abertura: '2025-11-28T16:30:00Z' },
                { id: 92, titulo: 'Acesso negado à pasta compartilhada', tecnico: 'João S.', categoria: 'Acesso/Permissão', status: 'Em Andamento', prioridade: 'Média', data_abertura: '2025-11-28T14:00:00Z' },
            ]
        };

        // Função para aplicar os dados aos cards
        function renderCards(data) {
            document.querySelector('#card-total .text-4xl').textContent = data.totalChamados;
            document.querySelector('#card-pendentes .text-4xl').textContent = data.pendentes;
            document.querySelector('#card-andamento .text-4xl').textContent = data.emAndamento;
            document.querySelector('#card-concluidos .text-4xl').textContent = data.concluidos;
            // Se quisesse incluir cancelados, teria que adicionar outro card
        }

        // Função para preencher a tabela
        function renderTable(data) {
            const tbody = document.getElementById('recent-calls-body');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-gray-400">Nenhum chamado encontrado.</td></tr>';
                return;
            }

            const rows = data.map(r => {
                const statusClass = r.status === 'Concluído' ? 'text-accent-green' :
                                    r.status === 'Pendente' ? 'text-accent-yellow' :
                                    'text-accent-blue';
                
                return `
                    <tr class="hover:bg-gray-800 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300">${r.id}</td>
                        <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-400">${r.titulo}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">${r.tecnico}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">${r.categoria}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold ${statusClass}">${r.status}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">${r.prioridade}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">${formatDate(r.data_abertura)}</td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rows;
        }


        // ----------------------------------------------------
        // INICIALIZAÇÃO DOS GRÁFICOS
        // ----------------------------------------------------
        function initializeCharts(data) {
            // Transformar dados para o formato Chart.js
            const statusLabels = data.statusData.map(r => r.status);
            const statusValues = data.statusData.map(r => r.total);
            const catLabels = data.categoryData.map(r => r.category);
            const catValues = data.categoryData.map(r => r.total);

            // Cores base para os gráficos (ajustadas para o tema dark)
            const backgroundColors = [
                '#f59e0b', // Pendente (Amarelo)
                '#3b82f6', // Em Andamento (Azul)
                '#10b981', // Concluído (Verde)
                '#ef4444', // Cancelado (Vermelho)
                '#6b7280', // Outros (Cinza)
                '#a855f7', // Roxo
                '#ec4899', // Rosa
            ];

            // PIE: Status
            const ctxPie = document.getElementById('pieStatus').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        label: 'Status',
                        data: statusValues,
                        backgroundColor: backgroundColors.slice(0, statusLabels.length),
                        borderColor: '#0f172a', // Cor de fundo do body
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { color: '#d6d6d6', padding: 20 } 
                        },
                        tooltip: { 
                            enabled: true,
                            backgroundColor: '#1f2937',
                            titleColor: '#f1f5f9',
                            bodyColor: '#e5e7eb',
                        }
                    }
                }
            });

            // BAR: Categoria
            const ctxBar = document.getElementById('barCategoria').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: catLabels,
                    datasets: [{
                        label: 'Chamados',
                        data: catValues,
                        backgroundColor: '#10b981', // Cor principal das barras
                        hoverBackgroundColor: '#059669',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y', // Barras horizontais
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { 
                            enabled: true,
                            backgroundColor: '#1f2937',
                            titleColor: '#f1f5f9',
                            bodyColor: '#e5e7eb',
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#cfcfcf' },
                            grid: { color: 'rgba(255,255,255,0.05)' }
                        },
                        y: {
                            ticks: { color: '#cfcfcf' },
                            grid: { color: 'rgba(255,255,255,0.03)' }
                        }
                    }
                }
            });
        }

        // ----------------------------------------------------
        // PONTO DE ENTRADA: Iniciar o dashboard após o carregamento
        // ----------------------------------------------------
        document.addEventListener('DOMContentLoaded', () => {
            renderCards(simulatedData);
            renderTable(simulatedData.recentCalls);
            initializeCharts(simulatedData);
        });

    </script>
</body>
</html>