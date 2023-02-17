<div class="ui segment">
    <canvas id="{{$data['id']}}"></canvas>
  </div>
  @once
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @endonce
<script>
    
    var labels = "{{$data['labels']}}";
    var values = "{{$data['values']}}"; 
    var colors = "{{$data['colors']}}"; 

  
    var config = {
    type: 'line',
    data: {
    labels: labels.split(","),
    datasets: [{
        label: "{{$gadget->title}}",
        data: values.split(","),
        backgroundColor: colors.split(",") ,
        hoverOffset: 4
        }
    ]
    },
    options: {
        scales: {
        y: {
            beginAtZero: true
        }
        }
    }
};

new Chart(document.getElementById("{{$data['id']}}"), config);
  </script>