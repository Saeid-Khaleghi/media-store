# Media store
[![Issues](https://img.shields.io/github/issues/Saeid-Khaleghi/media-store.svg?style=flat-square)](https://github.com/Saeid-Khaleghi/media-store/issues)
[![Forks](	https://img.shields.io/github/forks/Saeid-Khaleghi/media-store.svg?style=flat-square)](https://github.com/Saeid-Khaleghi/media-store/network/members)
[![Stars](	https://img.shields.io/github/stars/Saeid-Khaleghi/media-store.svg?style=flat-square)](https://github.com/Saeid-Khaleghi/media-store/stargazers)

`khaleghi/media-store` is a Laravel package which created to provide an easy way 
to store any kind of files on server storage and save their attributes in the database.
The other purpose of this package is to store images in a way that to be used in various responsive dimensions. 
This package is supported and tested in Laravel 5.4 and above.

## Requirements
PHP >= 5.4


### Installation
Run the following command: 
```bash
composer require khaleghi/media
```

Then run the following:
```bash
php artisan vendor:publish --provider="Khaleghi\MediaServiceProvider"

php artisan migrate
```

You have to make a link between `public` folder and `storage` by running this command:
```bash
php artisan storage:link
``` 

## Configuration
Set the property values in the config/media.php. These values will be used by media-store to make proper image sizes and customized upload folder.

## Usage

### Saving Images

Let's make a form in a html file:
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

Now, All ever we have to do in our controller is:
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
We can display the image like this:
```html
<div>
    <img src="{{asset(session('photo_url')) }}" alt="">
</div>
``` 

#### Responsive images
By default, The image we store in a way like above will be saved in 4 paths:
```
storage\app\public\images\lg    (Original Image size)
storage\app\public\images\md    (540 x 540 px)
storage\app\public\images\sm    (270 x 270 px)
storage\app\public\images\xs    (135 x 135 px)
```
You can access url of each path by sending these parameters:
```
$medium->url('lg') //default
$medium->url('md') // medium size
$medium->url('sm') // small size
$medium->url('xs') // extra small size
```

**Fill free to change default image sizes at config\media.php**

> **Note:** `khaleghi/media-store` uses [Intervension Image](http://http://image.intervention.io//) package to change dimension of images. It will be installed on your project as a dependency. 


### Saving other file types

You can store all other file types in a way that mentioned before. They will save in these paths related to their mime types:
```
storage\app\public\applications
storage\app\public\auduis
storage\app\public\videos
storage\app\public\texts
...
``` 

### Polymorphism
The `Medium` model is a polymorphic class. So you can make a relation between your other models to this model.
```php
$medium = Medium::create([
    'file' => $request->file('name'),
    'mediumable_type' => 'App\User',
    'mediumable_id' => 10 
]);
```
Now you can access to user's file:
```php
$user = App\User::find(10);
$file = $user->media->first();
echo $file->url();
```
And reverse access:
```php
echo $medium->mediumable->email;
```
> **Note:** Don't forget to use morphMany in medium's related model (here: App\User).
```php
public function media(){
    return $this->morphMany(Medium::class, 'mediumable');
}
```

### Determine before saving
You can make an instance of `medium` model and change it's attribute before save it:
```php
$medium = new Medium();
$medium->attach_file($request->file('name'));
$medium->description = "Occupational and educational information";
if($medium->size < 102400 and $medium->extension == "jpg"){
    $medium->save();
}else{
    $medium->detach();
    return "Error";
}
```

## Delete a medium
To delete a medium from Storage and Database you need to use `remove` method:
```php
$medium->remove();
```

## Database
These fields will be stored in database and you are able to access them through instance of Medium model:

|   Field Name   |          Usage                |
|----------------|-------------------------------|
|stored_name     |The name that file will store in storage folder           |
|file_name       |Original name of the file      |
|caption         ||
|mime            ||
|size            |Size of the file in bytes      |
|width           |Just for images|
|height          |Just for images|
|mediumable_type |Name of model that this file is related to it|
|mediumable_id   |model primary key that this file is related to it|
|position        |Assigning a priority to each file with same manner of mediumable_type|
|manner          |What this file is good for. Example: avatar|
|comments_count  ||
|likes_count     ||
|description     ||

## License

Media-store is free software distributed under the terms of the MIT license.
