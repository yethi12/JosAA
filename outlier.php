<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Description of the page">
  <meta name="keywords" content="Keyword1, Keyword2, Keyword3">
  <meta name="author" content="Author Name">
  <meta name="robots" content="index, follow">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1/chart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
  <title>Outlier Analysis</title>
</head>

<body>
  <form id="#outlier" method="post" action="outlier.php">
    <label for="min_rank">Minimum Opening Rank:</label>
    <input type="number" name="min_rank" id="min_rank"><br><br>

    <label for="max_rank">Maximum Opening Rank:</label>
    <input type="number" name="max_rank" id="max_rank"><br><br>

    <input type="submit" value="Submit">
    <canvas id="myChart" style="background-color : white;"></canvas>
  </form>



  <?php


  // Connect to the database using PDO
  $servername = "localhost";
  $port_no = 3306;
  $username = "Thanish";
  $password = "Thanish@123";
  $myDB = "josaa";

  try {
    $conn = new PDO("mysql:host=$servername;port=$port_no;dbname = $myDB", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (isset($_POST['min_rank'])) {
      $min_rank = $_POST['min_rank'];
    } else {
      $min_rank = 1000;
    }
    if (isset($_POST['max_rank'])) {
      $max_rank = $_POST['max_rank'];
    } else {
      $max_rank = 10000;
    }

    $gender = "Gender-Neutral";

    $stmt = $conn->query("SELECT * FROM josaa.`josaa`  WHERE  Gender='$gender' AND Round='6' AND `Seat Type`='OPEN' AND 1*`OR`>='$min_rank' AND 1*`OR`<='$max_rank'");
    // $stmt->bindParam(':min_rank', $min_rank);
    // $stmt->bindParam(':max_rank', $max_rank);

    // echo "connected successfully";
    $opening_ranks = array();
    $closing_ranks = array();
    $years = array();
    $institutes = array();
    $genders = array();
    $seats = array();
    $rounds = array();
    $departments = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $institutes[] = $row['Institute'];
      $departments[] = $row['Academic Program Name'];
      $opening_ranks[] = $row['OR'];
      $closing_ranks[] = $row['Closing Rank'];
    }


    $data = array();
    for ($i = 0; $i < count($opening_ranks); $i++) {
      $data[] = array('x' => $opening_ranks[$i], 'y' => $closing_ranks[$i], 'label1' => $institutes[$i], 'label2' => $departments[$i]);
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  ?>

  <div style="background-color: black; padding: 2%; margin-top: 1%; margin-left:5%; margin-right:5%">
    <div class="title">
      <h4 style="color:aquamarine; text-align:center; font-size:150%">Closing Rank vs Opening Rank</h4>
    </div>
    <canvas id="scatterChart"></canvas>
  </div>
  <script>
    var ctx = document.getElementById("scatterChart").getContext("2d");
    var scatterChart = new Chart(ctx, {
      type: 'scatter',
      data: {
        datasets: [{
          label: 'Scatter Plot',
          data: <?php echo json_encode($data); ?>,
          pointBackgroundColor: 'black',
          pointBorderColor: 'red',
          pointHoverBackgroundColor: 'black',
          pointHoverBorderColor: 'white',
          pointHoverBorderWidth: 2,
          pointHoverRadius: 7,
          pointStyle: 'circle',
        }]
      },
      options: {
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              var label1 = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].label1;
              var label2 = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].label2;
              return ': (' + tooltipItem.xLabel + ', ' + tooltipItem.yLabel + '), ' + label1 + ', ' + label2;
            }
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Closing Rank vs Opening Rank',
            font: {
              size: 36,
              weight: 'bold',
            },
            padding: {
              top: 1,
              bottom: 15
            },
            color: 'red' // set the font color of title
          }
        },
        scales: {
          xAxes: [{
            ticks: {
              fontColor: 'white'
            },
            scaleLabel: {
              display: true,
              labelString: 'Opening Rank',
              fontSize: 30,
              fontColor: 'white',
            }
          }],
          yAxes: [{
            ticks: {
              fontColor: 'white'
            },
            scaleLabel: {
              display: true,
              labelString: 'Closing Rank',
              fontSize: 30,
              fontColor: 'white',
            },
          }]
        },
      }
    });
  </script>
</body>

</html>