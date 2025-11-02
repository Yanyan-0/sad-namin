<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

$transactions_query = "SELECT * FROM transactions ORDER BY transaction_date DESC";
$transactions_result = mysqli_query($conn, $transactions_query);

$monthly_sales_query = "
  SELECT 
    DATE_FORMAT(transaction_date, '%Y-%m') AS month,
    SUM(total_amount) AS total_sales
  FROM transactions
  GROUP BY month
  ORDER BY month ASC
";
$monthly_sales_result = mysqli_query($conn, $monthly_sales_query);

$months = [];
$sales = [];
$total_revenue = 0;

while ($row = mysqli_fetch_assoc($monthly_sales_result)) {
  $months[] = $row['month'];
  $sales[] = $row['total_sales'];
  $total_revenue += $row['total_sales'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transactions - Admin Panel</title>
  <link rel="stylesheet" href="admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 90%;
      margin: 40px auto;
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      position: relative;
    }

    h1 {
      color: #004080;
      text-align: center;
      margin-bottom: 15px;
    }

    .top-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .back-btn, .download-btn {
      background-color: #004080;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      transition: background 0.3s ease;
    }

    .back-btn:hover, .download-btn:hover {
      background-color: #0059b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 10px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #004080;
      color: white;
    }

    .sales-summary {
      margin-top: 50px;
      padding: 20px;
      background: #e6f0ff;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .sales-summary h2 {
      color: #004080;
      margin-bottom: 10px;
      text-align: center;
    }

    .total-revenue {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 20px;
      text-align: center;
    }

    canvas {
      width: 90%;
      max-width: 700px;
      height: 180px;
      display: block;
      margin: 0 auto;
    }

    @media (max-width: 768px) {
      .container {
        width: 95%;
        margin: 20px auto;
        padding: 15px;
      }

      .top-buttons {
        flex-direction: column;
        gap: 10px;
      }

      table, th, td {
        font-size: 12px;
      }

      canvas {
        height: 150px;
      }
    }

    @media (max-width: 480px) {
      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="top-buttons">
      <a href="admin.php" class="back-btn">← Back to Dashboard</a>
      <button class="download-btn" id="downloadPDF">⬇ Download Report</button>
    </div>

    <!-- PAGE 1: TRANSACTIONS -->
    <div id="transactionsPage">
      <h1>Transactions</h1>
      <table>
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>User ID</th>
            <th>Total Amount (₱)</th>
            <th>Transaction Date</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($transaction = mysqli_fetch_assoc($transactions_result)) { ?>
            <tr>
              <td><?= $transaction['transaction_id'] ?></td>
              <td><?= $transaction['user_id'] ?></td>
              <td>₱<?= number_format($transaction['total_amount'], 2) ?></td>
              <td><?= $transaction['transaction_date'] ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- PAGE 2: SALES SUMMARY -->
    <div id="salesPage" class="sales-summary">
      <h2>📊 Sales Summary</h2>
      <div class="total-revenue">
        Total Revenue: <strong>₱<?= number_format($total_revenue, 2) ?></strong>
      </div>
      <canvas id="salesChart"></canvas>
    </div>
  </div>

  <script>
    // Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
          label: 'Monthly Sales (₱)',
          data: <?= json_encode($sales) ?>,
          backgroundColor: 'rgba(0, 64, 128, 0.7)',
          borderColor: '#004080',
          borderWidth: 1,
          borderRadius: 8,
          barThickness: 40
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) { return '₱' + value.toLocaleString(); }
            }
          }
        }
      }
    });

    // PDF Download (2 pages)
    document.getElementById('downloadPDF').addEventListener('click', async () => {
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF('p', 'mm', 'a4');

      // Page 1 - Transactions
      const page1 = await html2canvas(document.getElementById('transactionsPage'), { scale: 2 });
      const img1 = page1.toDataURL('image/png');
      const imgWidth = 190;
      const imgHeight1 = page1.height * imgWidth / page1.width;
      pdf.addImage(img1, 'PNG', 10, 10, imgWidth, imgHeight1);

      // Page 2 - Sales Summary
      pdf.addPage();
      const page2 = await html2canvas(document.getElementById('salesPage'), { scale: 2 });
      const img2 = page2.toDataURL('image/png');
      const imgHeight2 = page2.height * imgWidth / page2.width;
      pdf.addImage(img2, 'PNG', 10, 10, imgWidth, imgHeight2);

      pdf.save('Transaction_Report.pdf');
    });
  </script>
</body>
</html>
