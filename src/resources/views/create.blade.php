<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Media Test Page</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg">
            <h1 class="text-center">Media Upload Test</h1>
            <form action="{{route('media.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <span class="input-group-addon">Select your file:</span>
                    <input type="file" id="media" name="photo" class="form-control" style="height:50px;padding:12px">
                    <span class="input-group-addon">
                        <button class="btn btn-default" type="submit">Upload</button>
                    </span>
                </div>
            </form>
            <hr>
            <div class="card-img">
                <img src="{{asset(session('url')) }}" alt="">
            </div>

        </div>
    </div>
</div>

</body>
</html>

