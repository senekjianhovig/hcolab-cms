<style>
.unit{
  font-size: 20px;
  font-weight: bold;
}
</style>
<div class="ui segment " style="display: flex; align-items: center ; justify-content: center">
    <div class="ui statistic">
        <div class="value">
          {!! $data['count'] !!}
        </div>
        <div class="label">
          {{$gadget->title}}
        </div>
      </div>
</div>