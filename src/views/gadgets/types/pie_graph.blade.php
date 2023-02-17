<div class="ui segment">

    <h3 class="mt-1 mb-4 t-center">{{$gadget->title}}</h3>

    <canvas id="{{$data['id']}}"></canvas>
  </div>
  @once
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @endonce
  <script>
    
  
    var labels = "{{$data['labels']}}";
    var values = "{{$data['values']}}"; 
    var colors = "{{$data['colors']}}"; 

new Chart(document.getElementById("{{$data['id']}}"), {
  type: 'pie',
  data: {
  labels: labels.split(","),
  datasets: [{
    label: "{{$gadget->title}}",
    data: values.split(","),
    backgroundColor: colors.split(",") ,
    hoverOffset: 4
  }]
},
});
  </script>