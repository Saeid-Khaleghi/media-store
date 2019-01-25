<?php

namespace Khaleghi\Media;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Response;

class Medium extends Model
{
    //use Favorite;
    protected $guarded = ['id','file'];
    public $file_object;

    public function mediumable(){
        return $this->morphTo();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(isset($attributes['file'])){
            $this->attach_file($attributes['file']);
        }
    }

    protected function set_up_upload_folders(){

        if($this->is('image')){
            $large_image_folder = storage_path('app/'.$this->full_path_lg);
            $medium_image_folder = storage_path('app/'.$this->full_path_md);
            $small_image_folder = storage_path('app/'.$this->full_path_sm);
            $extra_small_image_folder = storage_path('app/'.$this->full_path_xs);

            File::isDirectory($large_image_folder) or File::makeDirectory($large_image_folder, 0777, true, true);
            File::isDirectory($medium_image_folder) or File::makeDirectory($medium_image_folder, 0777, true, true);
            File::isDirectory($small_image_folder) or File::makeDirectory($small_image_folder, 0777, true, true);
            File::isDirectory($extra_small_image_folder) or File::makeDirectory($extra_small_image_folder, 0777, true, true);
        }else{
            $path = storage_path('app/'.$this->main_upload_folder."/{$this->type}s/");
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
        }
    }

    public function attach_file($file)
    {
        if($this->id){ // if the file already stored
            if($this->is('image')){
                Storage::delete($this->full_path_lg.'/'.$this->stored_name);
                Storage::delete($this->full_path_md.'/'.$this->stored_name);
                Storage::delete($this->full_path_sm.'/'.$this->stored_name);
                Storage::delete($this->full_path_xs.'/'.$this->stored_name);
            }else{
                Storage::delete($this->full_path.'/'.$this->stored_name);
            }
        }

        $this->file_object = $file;
        $full_name = $file->getClientOriginalName();

        $this->stored_name = $this->store_file_name_hashed() ? $file->hashName() : $full_name;
        $this->file_name = pathinfo($full_name, PATHINFO_FILENAME);
        $this->extension = strtolower($file->getClientOriginalExtension());
        $this->size = $file->getSize();
        $this->mime = $file->getMimeType();
        $size = @is_array(getimagesize($file)) ? getimagesize($file) : [null, null];
        $this->width = $size[0];
        $this->height = $size[1];
    }

    public function save(array $options = [])
    {
        $this->store();
        return parent::save($options);
    }

    public function store() {
        if(!$this->file_object){
            throw new Exception('No File Attached');
        }
        // Create upload folder if not exists
        $this->set_up_upload_folders();
        $stored_name = $this->store_file_name_hashed() ? $this->file_object->hashName() : $this->full_name;

        if($this->is('image')){
            if(config('media.LARGE_IMAGE_SIZE')){
                Image::make($this->file_object)
                    ->widen(config('media.LARGE_IMAGE_SIZE'))
                    ->save(storage_path('app/'.$this->full_path_lg.'/'.$stored_name));
            }else{
                $this->file_object->storeAs($this->full_path_lg, $stored_name);
            }

            if(config('media.CREATE_RESPONSIVE_SIZES')){

                Image::make($this->file_object)
                    ->widen(config('media.MEDIUM_IMAGE_SIZE'))
                    ->save(storage_path('app/'.$this->full_path_md.'/'.$this->stored_name));

                Image::make($this->file_object)
                    ->widen(config('media.SMALL_IMAGE_SIZE'))
                    ->save(storage_path('app/'.$this->full_path_sm.'/'.$this->stored_name));

                Image::make($this->file_object)
                    ->widen(config('media.EXTRA_SMALL_IMAGE_SIZE'))
                    ->save(storage_path('app/'.$this->full_path_xs.'/'.$this->stored_name));
            }
        }elseif($this->is($this->type)){
            $this->file_object->storeAs(config('media.MAIN_UPLOAD_FOLDER').'/'.$this->type.'s',$stored_name);
        }
    }

    public function url($image_size = 'lg'){
        if($this->is('image')){
            return Storage::url($this->main_upload_folder."/images/{$image_size}/".$this->stored_name);
        }else{
            return Storage::url($this->main_upload_folder."/{$this->type}s/".$this->stored_name);
        }
    }

    /********************** ACCESSORS *********************/

    public function getFullNameAttribute() {
        return $this->file_name.'.'.$this->extension;
    }

    public function getMainUploadFolderAttribute(){
        return config('media.MAIN_UPLOAD_FOLDER');
    }

    public function getFullPathLgAttribute(){
        return $this->main_upload_folder.'/images/'.config('media.LARGE_IMAGE_FOLDER_PATH');
    }

    public function getFullPathMdAttribute(){
        return $this->main_upload_folder.'/images/'.config('media.MEDIUM_IMAGE_FOLDER_PATH');
    }

    public function getFullPathSmAttribute(){
        return $this->main_upload_folder.'/images/'.config('media.SMALL_IMAGE_FOLDER_PATH');
    }

    public function getFullPathXsAttribute(){
        return $this->main_upload_folder.'/images/'.config('media.EXTRA_SMALL_IMAGE_FOLDER_PATH');
    }

    public function getFullPathAttribute()
    {
        return $this->main_upload_folder."/{$this->type}s/";
    }

    public function getTypeAttribute(){
        return explode('/',$this->mime)[0];
    }

    /********************** DETERMINERS *********************/

    public function is($type){
        return preg_match("/{$type}\/*/",$this->mime);
    }

    public function store_file_name_hashed(){
        return config('media.STORE_FILE_NAME_HASHED');
    }

    /********************** SCOPES *********************/

    public function scopeByType($query, $type)
    {
        return $query->where('mime', 'LIKE', $type.'/%' );
    }

    public function scopeImages($query)
    {
        return $query->where('mime', 'LIKE', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime', 'LIKE', 'video/%');
    }

    public function scopeOf($query, $value){
        return $query->where('mediumable_type', $value);
    }

}
