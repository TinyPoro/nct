<input type="hidden" id="playlistId" value="{{$playlistId}}">
<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Key</th>
        <th scope="col">Loại</th>
        <th scope="col">Tên</th>
        <th scope="col">Nghệ sĩ</th>
        <th scope="col">Link ảnh</th>
    </tr>
    </thead>
    <tbody>
    @foreach($medias as $media)
        <tr>
            <th scope="row">{{$media->id}}</th>
            <td>{{$media->key}}</td>
            <td>{{$media->type_text}}</td>
            <td><a href="{{route('media.download', ['media_id' => $media->id])}}" target="_blank">{{$media->title}}</a></td>
            <td>{{$media->artists}}</td>
            <td><a href="{{$media->image}}" target="_blank">{{$media->image}}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $medias->links() }}