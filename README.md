# Media store
[![Issues](https://img.shields.io/github/issues/Saeid-Khaleghi/media-store.svg?style=flat-square)](https://github.com/Saeid-Khaleghi/media-store/issues)
[![Forks](	https://img.shields.io/github/forks/Saeid-Khaleghi/media-store.svg?style=flat-square)](https://github.com/Saeid-Khaleghi/media-store/network/members)
[![Stars](	https://img.shields.io/github/stars/Saeid-Khaleghi/media-store.svg?style=flat-square)](https://github.com/Saeid-Khaleghi/media-store/stargazers)

`khaleghi/media-store` is a Laravel package which created to provide an easy way 
to store any kind of files on server storage and save their attributes in the database.
The other purpose of this package is to store images in a way that to be used in various responsive dimensions. 
This package is supported and tested in Laravel 5.4 and above.

### Installation
Run the following command: 
```bash
composer require khaleghi/media
```

Then run the following:
```bash
php artisan vendor:publish"

php artisan migrate
```
## Configuration
Set the property values in the config/media.php. These values will be used by media-store to make proper image sizes and customized upload folder.

## Usage

Let's make a form in a html file.
For example to upload an image you can do this:
```blade
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
```

Then make a route like this:
```blade
Route::post('media', 'YourController@YourMethod')->name('media.store');
```

Now, All ever you have to do in your controller is:
```php
use Khaleghi\Media\Medium;

public function YourMethod(Request $request){
    if($request->has('photo')){
        $medium = Medium::create([
            'file' => $request->file('photo'),
        ]);
        
        return back()->with('photo_url', $medium->url());
    }
}
```
