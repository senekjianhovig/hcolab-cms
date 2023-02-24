<?php


$email=$template->email;



//$EmailText = $EmailTemplate['template_en'];
$Search = [];
$Replace = [];
foreach ($dictionary AS $key=>$value){
    $Search[]   =   "*".$key."*";
    $Replace[]  =   $value;
}

$email = str_replace( $Search, $Replace,$email );



?>

<style>
    .divider{
        margin: 5px 0;
        border-bottom: 1px solid #eee;
       
    }
    table{
        width:100%;

    }

    td{
        padding:10px;
    }

    a.icon {
        text-decoration: none;

    }

    a{
        color: initial
    }

    .button{
       display: inline-block;
       border-radius: 5px; 
       border : 1px solid #000;
        color: #fff;
        background-color: #000;
        padding: 15px 25px;
        font-weight: bold;
        font-size: 14px;
        text-decoration: none
    }

    .button:hover{
        color: #000;
        background-color: #fff;
    }

    </style>

<div style="font-family:Arial, Helvetica, sans-serif; padding: 30px 0; font-size: 14px;   ">
    <div style="margin:auto; max-width: 500px; padding: 30px 30px 10px 30px; overflow: hidden ; border: 1px solid #eeeeee;" >
    <img src="{{env('APP_URL')}}/assets/mail/logo.png" />
   
    <div style="margin-top:40px">
    {!! $email !!}
    </div>

    <div  class="divider"> </div>
<br> 
    <p style="color: #000029 ; font-size: 12px"> 
        Please do not reply directly to this email. This was sent from an address that cannot accept responses.</a>.
    </p>

</div>
</div>