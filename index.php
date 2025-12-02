<?php
// Inclui o arquivo de conexão
include 'conexao.php';

// Inicializa a variável de dados do PHP
$chamadosData = [];
$totalChamados = 0;

// =========================================================================
// 1. BUSCA DOS DADOS PRINCIPAIS (Simulação de JOINs usando apenas a tabela de chamados)
// Na prática, você faria um JOIN com tecnicos, categorias e prioridades
// para obter os nomes diretamente no SQL, mas aqui simulamos o resultado.
// =========================================================================

// Query para buscar todos os chamados
$sql = "SELECT id, titulo, tecnico_id, categoria_id, prioridade_id, status, data_abertura FROM chamados ORDER BY data_abertura DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $chamadosData[] = $row;
        $totalChamados++;
    }
}

// =========================================================================
// 2. BUSCA DAS TABELAS DE LOOKUP (Técnicos, Categorias, Prioridades)
// Na aplicação real, estes dados seriam necessários para mapear os IDs aos nomes.
// Aqui, eles estão hardcoded para fins de demonstração, mas o código a seguir
// mostra como você faria a busca.
// =========================================================================
$lookup = [
    'tecnicos' => [1 => 'Marcos Silva', 2 => 'Patrícia Mendes', 3 => 'José Oliveira', 4 => 'Carla Santos', 5 => 'Rafael Almeida', 6 => 'Thiago Moreira'],
    'categorias' => [1 => 'Hardware', 2 => 'Software', 3 => 'Rede', 4 => 'Impressoras', 5 => 'Sistemas Internos', 6 => 'Backup', 7 => 'Acessos e Permissões'],
    'prioridades' => [1 => 'Baixa', 2 => 'Média', 3 => 'Alta', 4 => 'Crítica']
];

// O dado final para o JS é o array dos chamados e as tabelas de lookup
$jsData = [
    'chamados' => $chamadosData,
    'lookup' => $lookup
];

// Transforma os dados em JSON para serem injetados no JavaScript
$jsonChamadosData = json_encode($jsData);

// Fecha a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Helpdesk MySQL</title>
    <!-- Inclui o arquivo CSS externo -->
    <link rel="stylesheet" href="style.css">
    <!-- Carrega a biblioteca Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>
<body>

    <header class="header">
        <h1>Dashboard de Chamados (Helpdesk)</h1>
        <p>Visualização estatística dos tickets do sistema.</p>
    </header>

    <!-- Indicador de Carregamento (Agora usando o estilo do CSS) -->
    <div id="loading-indicator" class="loading-indicator"></div>

    <!-- Contêiner principal para centralizar e limitar a largura na tela -->
    <div id="charts-container" class="charts-container" style="display: none;">

        <!-- GRÁFICO 1: Chamados por Status (Bar Chart) -->
        <div class="chart-card">
            <h2 style="color: #4f46e5;">Chamados por Status Atual</h2>
            <div class="canvas-wrapper">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <!-- GRÁFICO 2: Chamados por Categoria (Doughnut Chart) -->
        <div class="chart-card">
            <h2 style="color: #10b981;">Distribuição por Categoria</h2>
            <div class="canvas-wrapper">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Tabela de Chamados Recentes para visualização -->
    <div class="max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold mt-12 mb-4 text-center">Chamados Recentes</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Técnico</th>
                    <th>Categoria</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($chamadosData)): ?>
                <?php foreach ($chamadosData as $chamado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($chamado['id']); ?></td>
                        <td><?php echo htmlspecialchars($chamado['titulo']); ?></td>
                        <!-- Mapeando ID para Nome (Simulação) -->
                        <td><?php echo htmlspecialchars($lookup['tecnicos'][$chamado['tecnico_id']] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($lookup['categorias'][$chamado['categoria_id']] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($lookup['prioridades'][$chamado['prioridade_id']] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($chamado['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center;">Nenhum chamado encontrado no banco de dados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        // Injeta os dados do PHP no JavaScript
        const initialData = <?php echo $jsonChamadosData; ?>;
        const chamadosData = initialData.chamados;
        const categorias = initialData.lookup.categorias;

        // --- FUNÇÕES DE CONTROLE DE UI ---
        function showLoading() {
            document.getElementById('loading-indicator').style.display = 'block';
            document.getElementById('charts-container').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('loading-indicator').style.display = 'none';
            document.getElementById('charts-container').style.display = 'grid';
        }

        // --- FUNÇÃO PARA PROCESSAR OS DADOS E AGREGAR CONTAGENS ---
        function processDataForCharts(data) {
            const statusCounts = {};
            const categoryCounts = {};

            data.forEach(chamado => {
                // Contagem por Status
                statusCounts[chamado.status] = (statusCounts[chamado.status] || 0) + 1;

                // Contagem por Categoria
                const categoryName = categorias[chamado.categoria_id];
                categoryCounts[categoryName] = (categoryCounts[categoryName] || 0) + 1;
            });

            return { statusCounts, categoryCounts };
        }

        // --- FUNÇÃO PRINCIPAL DE INICIALIZAÇÃO ---
        function initializeCharts() {
            // No ambiente PHP, os dados já foram carregados antes da renderização do HTML,
            // mas simularemos um atraso de renderização para dar a sensação de carregamento.
            showLoading();

            setTimeout(() => {
                // O processo de busca do banco de dados já ocorreu no lado PHP
                const fetchedData = chamadosData;
                
                hideLoading();

                // Processa os dados recebidos
                const { statusCounts, categoryCounts } = processDataForCharts(fetchedData);

                // ------------------------------------
                // 3. Gráfico de Barras - Chamados por Status
                // ------------------------------------
                const statusLabels = Object.keys(statusCounts);
                const statusData = Object.values(statusCounts);

                const statusColors = {
                    'Pendente': 'rgba(234, 179, 8, 0.9)', 
                    'Em andamento': 'rgba(79, 70, 229, 0.9)', 
                    'Concluído': 'rgba(16, 185, 129, 0.9)', 
                    'Cancelado': 'rgba(239, 68, 68, 0.9)' 
                };
                
                const barChartColors = statusLabels.map(label => statusColors[label]);

                const barCtx = document.getElementById('barChart').getContext('2d');
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            label: 'Número de Chamados',
                            data: statusData,
                            backgroundColor: barChartColors,
                            borderColor: barChartColors.map(color => color.replace('0.9', '1')),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Contagem de Chamados' }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: `Total de Chamados: ${fetchedData.length}`
                            }
                        }
                    }
                });

                // ------------------------------------
                // 4. Gráfico de Rosca (Doughnut) - Chamados por Categoria
                // ------------------------------------
                const categoryLabels = Object.keys(categoryCounts);
                const categoryData = Object.values(categoryCounts);

                const categoryPalette = [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                    '#8b5cf6', '#6366f1', '#06b6d4', 
                ];

                const lineCtx = document.getElementById('lineChart').getContext('2d');
                new Chart(lineCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Tickets por Categoria',
                            data: categoryData,
                            backgroundColor: categoryPalette,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { padding: 20 }
                            },
                            title: {
                                display: true,
                                text: 'Distribuição Percentual de Tickets'
                            }
                        }
                    }
                });
            }, 500); // Pequeno atraso para mostrar o spinner
        }

        // Inicia a renderização
        window.onload = initializeCharts;
    </script>
</body>
</html>