@extends('layouts.app')

@section('content')
    <div class="container">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Tên</th>
                <th scope="col">Nghệ sĩ</th>
                <th scope="col">Link ảnh</th>
            </tr>
            </thead>
            <tbody>
                @foreach($playlists as $playlist)
                    <tr>
                        <th scope="row">{{$playlist->id}}</th>
                        <td><a href="{{route('playlist.medias', ['playlist_id' => $playlist->id])}}" target="_blank">{{$playlist->name}}</a></td>
                        <td>{{$playlist->artist}}</td>
                        <td><a href="{{$playlist->image}}" target="_blank">{{$playlist->image}}</a></td>
                    </tr>
                @endforeach
            </tbody>

            {{ $playlists->links() }}
        </table>
    </div>
@endsection

@section('after_scripts')

@endsection
