<?php
// index.php
require_once 'conexao.php';

// ----------------------
// Funções auxiliares
// ----------------------
function get_count($mysqli, $where = '') {
    $sql = "SELECT COUNT(*) as cnt FROM chamados " . ($where ? " WHERE $where" : "");
    $res = $mysqli->query($sql);
    return $res ? (int)$res->fetch_assoc()['cnt'] : 0;
}

// Total
$total_chamados = get_count($mysqli);

// Contagens por status (usadas nos cards)
$pendentes    = get_count($mysqli, "status = 'Pendente'");
$em_andamento = get_count($mysqli, "status = 'Em Andamento' OR status = 'Em andamento' OR status = 'EmAndamento'");
$concluidos   = get_count($mysqli, "status = 'Concluído' OR status = 'Concluídos' OR status = 'Concluido'");
$cancelados   = get_count($mysqli, "status = 'Cancelado' OR status = 'Cancelados'");

// ----------------------
// Dados para gráficos
// ----------------------
// 1) Distribuição por status (geral) - pega qualquer status existente no DB
$status_data = [];
$status_q = $mysqli->query("SELECT status, COUNT(*) as total FROM chamados GROUP BY status");
if ($status_q) {
    while ($row = $status_q->fetch_assoc()) {
        $status_data[] = $row;
    }
}

// 2) Chamados por categoria
$category_data = [];
$cat_q = $mysqli->query("
    SELECT c.nome AS category, COUNT(*) AS total
    FROM chamados ch
    LEFT JOIN categorias c ON ch.categoria_id = c.id
    GROUP BY c.nome
    ORDER BY total DESC
");
if ($cat_q) {
    while ($row = $cat_q->fetch_assoc()) {
        $category_data[] = $row;
    }
}

// 3) Chamados por prioridade
$priority_data = [];
$prio_q = $mysqli->query("
    SELECT p.nome AS prioridade, COUNT(*) AS total
    FROM chamados ch
    LEFT JOIN prioridades p ON ch.prioridade_id = p.id
    GROUP BY p.nome
    ORDER BY total DESC
");
if ($prio_q) {
    while ($row = $prio_q->fetch_assoc()) {
        $priority_data[] = $row;
    }
}

// ----------------------
// Tabela: chamados mais recentes
// ----------------------
$recent = [];
$recent_q = $mysqli->query("
    SELECT ch.id, ch.titulo, COALESCE(t.nome, '-') AS tecnico, COALESCE(c.nome, '-') AS categoria,
           ch.status, COALESCE(p.nome, '-') AS prioridade, ch.data_abertura
    FROM chamados ch
    LEFT JOIN tecnicos t ON ch.tecnico_id = t.id
    LEFT JOIN categorias c ON ch.categoria_id = c.id
    LEFT JOIN prioridades p ON ch.prioridade_id = p.id
    ORDER BY ch.data_abertura DESC
    LIMIT 10
");
if ($recent_q) {
    while ($row = $recent_q->fetch_assoc()) {
        $recent[] = $row;
    }
}

// Fechar conexão (opcional — ainda podemos usar $mysqli mais tarde)
$mysqli->close();

// Para o Chart.js vamos transformar os arrays em JSON
$status_labels = array_map(function($r){ return $r['status']; }, $status_data);
$status_values = array_map(function($r){ return (int)$r['total']; }, $status_data);

$cat_labels = array_map(function($r){ return $r['category'] ? $r['category'] : 'Sem categoria'; }, $category_data);
$cat_values = array_map(function($r){ return (int)$r['total']; }, $category_data);

$prio_labels = array_map(function($r){ return $r['prioridade'] ? $r['prioridade'] : 'Sem prioridade'; }, $priority_data);
$prio_values = array_map(function($r){ return (int)$r['total']; }, $priority_data);
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Helpdesk - Dark Premium</title>
  <link rel="stylesheet" href="style.css">
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <header class="topbar">
    <div class="brand">
      <h1>HelpDesk - Dashboard</h1>
      <p class="subtitle">Visão geral dos chamados</p>
    </div>
    <div class="header-right">
      <div class="user">Admin</div>
    </div>
  </header>

  <main class="container">
    <!-- CARDS SUPERIORES -->
    <section class="cards-row">
      <div class="card big">
        <div class="card-title">Total de Chamados</div>
        <div class="card-value"><?php echo $total_chamados; ?></div>
      </div>

      <div class="card big">
        <div class="card-title">Pendentes</div>
        <div class="card-value"><?php echo $pendentes; ?></div>
      </div>

      <div class="card big">
        <div class="card-title">Em Andamento</div>
        <div class="card-value"><?php echo $em_andamento; ?></div>
      </div>
    </section>

    <!-- CARDS INFERIORES -->
    <section class="cards-row">
      <div class="card large">
        <div class="card-title">Concluídos</div>
        <div class="card-value"><?php echo $concluidos; ?></div>
      </div>

      <div class="card large">
        <div class="card-title">Cancelados</div>
        <div class="card-value"><?php echo $cancelados; ?></div>
      </div>
    </section>

    <!-- GRÁFICOS -->
    <section class="charts-row">
      <div class="chart-card">
        <h3>Status dos Chamados</h3>
        <canvas id="pieStatus"></canvas>
      </div>

      <div class="chart-card">
        <h3>Chamados por Categoria</h3>
        <canvas id="barCategoria"></canvas>
      </div>
    </section>

    <!-- TABELA DE CHAMADOS -->
    <section class="table-card">
      <h3>Chamados mais recentes</h3>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Título</th>
              <th>Técnico</th>
              <th>Categoria</th>
              <th>Status</th>
              <th>Prioridade</th>
              <th>Data de abertura</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($recent) === 0): ?>
              <tr><td colspan="7" style="text-align:center">Nenhum chamado encontrado.</td></tr>
            <?php else: ?>
              <?php foreach ($recent as $r): ?>
                <tr>
                  <td><?php echo htmlspecialchars($r['id']); ?></td>
                  <td><?php echo htmlspecialchars($r['titulo']); ?></td>
                  <td><?php echo htmlspecialchars($r['tecnico']); ?></td>
                  <td><?php echo htmlspecialchars($r['categoria']); ?></td>
                  <td><?php echo htmlspecialchars($r['status']); ?></td>
                  <td><?php echo htmlspecialchars($r['prioridade']); ?></td>
                  <td><?php 
                        $d = $r['data_abertura'];
                        // tenta formatar data para dd/mm/YYYY se possível
                        $fmt = strtotime($d) ? date('d/m/Y H:i', strtotime($d)) : $d;
                        echo htmlspecialchars($fmt);
                      ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- RODAPÉ -->
    <footer class="page-footer">
      <div>
        Projeto Helpdesk • Estilo Dark Premium
      </div>
      <div class="ia-note">
        <strong>IA usada:</strong> Códigos PHP, CSS, estrutura do dashboard, consultas SQL e Chart.js gerados/ajustados com auxílio de IA.
      </div>
    </footer>
  </main>

  <!-- SCRIPTS: dados do PHP para JS -->
  <script>
    // Dados vindos do PHP
    const statusLabels = <?php echo json_encode($status_labels, JSON_UNESCAPED_UNICODE); ?>;
    const statusValues = <?php echo json_encode($status_values); ?>;

    const catLabels = <?php echo json_encode($cat_labels, JSON_UNESCAPED_UNICODE); ?>;
    const catValues = <?php echo json_encode($cat_values); ?>;

    // PIE: Status
    const ctxPie = document.getElementById('pieStatus').getContext('2d');
    const pieStatus = new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: statusLabels,
        datasets: [{
          label: 'Status',
          data: statusValues,
          // Let Chart.js pick colors automatically; do not set explicit colors per style rules.
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom', labels: { color: '#d6d6d6' } },
          tooltip: { enabled: true }
        }
      }
    });

    // BAR: Categoria
    const ctxBar = document.getElementById('barCategoria').getContext('2d');
    const barCategoria = new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: catLabels,
        datasets: [{
          label: 'Chamados',
          data: catValues,
          // Chart.js will apply default colors
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
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
  </script>
</body>
</html>
