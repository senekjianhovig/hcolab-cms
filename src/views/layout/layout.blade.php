<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('head')

    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/loader-start.css">
    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/app.css">
    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/grid-system.min.css">


    {{-- <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/fontawesome.min.css"> --}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.0/semantic.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css" integrity="sha512-n//BDM4vMPvyca4bJjZPDh7hlqsQ7hqbP9RH18GF2hTXBY5amBwM2501M0GPiwCU/v9Tor2m13GOTFjk00tkQA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

  

</head>


{{-- <script>


  Pusher.logToConsole = true;

  var pusher = new Pusher('bbfe3dc7676be3fc4445', {
    cluster: 'ap2'
  });

  var channel = pusher.subscribe('my-channel');
  channel.bind('my-event', function(data) {
    alert(JSON.stringify(data));
  });
</script> --}}





<body style="background-color: #f8f7f7">
    <div class="screen-loader-init screen-loader-hide"></div>

    @include('CMSViews::partials.sidebar' , ['entity' => isset($entity) ? $entity : null])
    <div class="main-content">
        @include('CMSViews::partials.header', ["title" => $title ])
        @yield('content')
    </div>





    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="https://kit.fontawesome.com/2d0d0c6705.js" crossorigin="anonymous"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.0/semantic.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.js" integrity="sha512-5cguXwRllb+6bcc2pogwIeQmQPXEzn2ddsqAexIBhh7FO1z5Hkek1J9mrK2+rmZCTU6b6pERxI7acnp1MpAg4Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    
    <script src="{{env('APP_URL')}}/hcolab/cms/js/script.js"></script>


    @yield ('scripts')

    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/loader-end.css">

    {{-- <script type="text/javascript">
      Pusher.logToConsole = true;
        var notificationsWrapper   = $('.dropdown-notifications');
        var notificationsToggle    = notificationsWrapper.find('a[data-toggle]');
        var notificationsCountElem = notificationsToggle.find('i[data-count]');
        var notificationsCount     = parseInt(notificationsCountElem.data('count'));
        var notifications          = notificationsWrapper.find('ul.dropdown-menu');
    
        if (notificationsCount <= 0) {
          notificationsWrapper.hide();
        }
    
    
        var pusher = new Pusher('bbfe3dc7676be3fc4445', {
        cluster: 'ap2'
        });
    
        // Subscribe to the channel we specified in our Laravel Event
        var channel = pusher.subscribe('my-channel');
    
        
        // Bind a function to a Event (the full Laravel class)
        channel.bind('my-event', function(data) {

          alert("new message");

          var existingNotifications = notifications.html();
      
          var newNotificationHtml = `
            <a  class="item active">
                ${data.message}
            </a>
          `;
          notifications.html(newNotificationHtml + existingNotifications);
    
          notificationsCount += 1;
          notificationsCountElem.attr('data-count', notificationsCount);
          notificationsWrapper.find('.notif-count').text(notificationsCount);
          notificationsWrapper.show();
        });
      </script> --}}

      @if(request()->has('notification_type') && request()->notification_message)
      <script>
        $.toast({ class: "{{request()->input('notification_type')}}", message: `{{request()->input('notification_message')}}` });
      </script>
      @endif

</body>

</html>