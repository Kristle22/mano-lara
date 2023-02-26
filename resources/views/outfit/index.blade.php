@foreach ($outfits as $outfit)
  <a href="{{route('outfit.edit',$outfit)}}">
    {{$outfit->type}} {{$outfit->color}} {{$outfit->size}}
    {{$outfit->getMaster->name}} {{$outfit->getMaster->surname}}</a>
    <form method="POST" action="{{route('outfit.destroy', $outfit)}}">
      @csrf
      <button type="submit">DELETE</button>
     </form>   
@endforeach

