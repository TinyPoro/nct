@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="form-group">
            <label for="search">Tìm kiếm:</label>
            <input type="text" class="form-control" id="search">
        </div>

        <div id="media_table">
            @include('playlist.media_table')
        </div>
    </div>
@endsection

@section('after_scripts')
    <script>
        $('#search').on('input', () => {
            let search = $('#search').val();
            let playlistId = $('#playlistId').val();

            let url = '{{route('playlist.medias.json')}}'
            let data = {
                'playlistId': playlistId,
                'search': search
            }

            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                success: function(response)
                {
                    $('#media_table').html(response)
                },
                error: function(xhr)
                {
                    console.log(xhr.responseText)
                }
            });
        })
    </script>
@endsection
