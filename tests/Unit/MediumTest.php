<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Khaleghi\Media\Medium;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Throwable;

class MediumTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function attachUploadedFileToMediumInstance()
    {
        $medium = new Medium();
        $this->assertTrue($medium->attach_file(UploadedFile::fake()->create('movie.mp4', 6000)));
    }

    /**
     * @test
     */
    public function checkIfTheUploadedImageAttributesAssigned()
    {
        $medium = $this->attachAnImageToNewMedium();

        $this->assertEquals('file', $medium->file_name);
        $this->assertEquals('file.png', $medium->stored_name);
        $this->assertEquals('png', $medium->extension);
        $this->assertEquals(1127, $medium->size);
        $this->assertEquals('image/png', $medium->mime);
        $this->assertEquals(600, $medium->width);
        $this->assertEquals(600, $medium->height);
    }

    /**
     * @test
     */
    public function checkIfTheUploadedFileAttributesAssigned()
    {
        $medium = $this->attachAFileToNewMedium();

        $this->assertEquals('movie', $medium->file_name);
        $this->assertEquals('movie.mp4', $medium->stored_name);
        $this->assertEquals('mp4', $medium->extension);
        $this->assertEquals(6144000, $medium->size);
        $this->assertEquals('video/mp4', $medium->mime);
    }

    /**
     * @test
     */
    public function AttachFileInConstructor()
    {
        $medium = new Medium([
            'file' => UploadedFile::fake()->image('file.png', 600, 600),
        ]);
        $this->assertTrue($medium->is_file_attached());
        $this->assertNull($medium->created_at);
    }

    /**
     * @test
     */
    public function detachAFileFromUnsavedMedium()
    {
        $medium = new Medium(
            ['file' => UploadedFile::fake()->image('file.png', 600, 600)]
        );
        $this->assertTrue($medium->is_file_attached());
        $this->assertTrue($medium->detach());
    }

    /**
     * @test
     */
    public function storeAnImageInStorage()
    {
        $medium = new Medium(
            ['file' => UploadedFile::fake()->image('file.png', 600, 400)]
        );
        $medium->store();

        Storage::disk('public')->assertExists('upload/images/lg/file.png');
        Storage::disk('public')->assertExists('upload/images/md/file.png');
        Storage::disk('public')->assertExists('upload/images/sm/file.png');
        Storage::disk('public')->assertExists('upload/images/xs/file.png');

        Storage::disk('public')->delete('upload/images/lg/file.png');
        Storage::disk('public')->delete('upload/images/md/file.png');
        Storage::disk('public')->delete('upload/images/sm/file.png');
        Storage::disk('public')->delete('upload/images/xs/file.png');
    }

    /**
     * @test
     */
    public function imageAttributesSaveIntoDatabase()
    {
        $medium = $this->attachAnImageToNewMedium();
        $this->assertTrue( $medium->save());
        $this->assertNotNull($medium->created_at);
    }

    /**
     * @test
     */
    public function detachAFileFromSavedMedium()
    {
        $medium = new Medium(
            ['file' => UploadedFile::fake()->image('file.png', 600, 600)]
        );
        $medium->save();
        $this->assertTrue($medium->is_file_attached());
        $this->assertFalse($medium->detach());
    }

    /**
     * @test
     */
    public function makeAndSaveAMediumInstanceAtOnce()
    {
        $medium = Medium::create([
            'file' => UploadedFile::fake()->image('file.png', 600, 400),
        ]);
        $this->assertTrue($medium->is_file_attached());
        $this->assertNotNull($medium->created_at);
    }

    /**
     * @test
     */
    public function tryToAttachANewFileToSavedMedium()
    {
        $medium = Medium::create([
            'file' => UploadedFile::fake()->image('file.png', 600, 400),
        ]);
        $this->assertFalse($medium->attach_file(UploadedFile::fake()->image('new_image.png', 600, 600)));
    }

    /**
     * @test
     */
    public function deleteStoredFileFromDatabase()
    {
        $medium = $this->uploadAndSaveAFileIntoStorageAndDatabase();
        $this->assertTrue( $medium->remove());
    }

    /**
     * @test
     */
    public function getUrlOfAFile()
    {
        $medium = $this->uploadAndSaveAFileIntoStorageAndDatabase();
        $this->assertEquals('/storage/upload/videos/movie.mp4',$medium->url());
    }

    /**
     * @test
     */
    public function AssignAFileToAUser()
    {
        $this->refreshDatabase();
        $user_first_name = $this->faker->firstName;
        $user = \App\User::create(['name' => $user_first_name, 'email'=> $this->faker->email,'password'=>$this->faker->password]);
        $this->assertEquals($user_first_name,$user->name);

        $medium = Medium::create([
            'file' => UploadedFile::fake()->image('file.png', 600, 400),
            'mediumable_type' => 'App\User',
            'mediumable_id' => $user->id
        ]);
        $this->assertEquals($user_first_name,$medium->mediumable->name);
        $this->assertEquals('file',$user->media->first()->file_name);
    }

    /**
     * @test
     */
    public function CRUD()
    {
        $medium = $this->uploadAndSaveAFileIntoStorageAndDatabase();
        // Create
        $this->assertNotNull($medium->created_at);
        // Read
        $this->assertEquals('/storage/upload/videos/movie.mp4', $medium->url());
        // Update
        $medium->description = "Occupational and educational information";
        $medium->save();
        $this->assertEquals("Occupational and educational information",$medium->description);
        // Delete
        Storage::disk('public')->assertExists('upload/videos/movie.mp4');
        $this->assertTrue( $medium->remove());
    }

    /**
     * @test
     */
    public function countMediaOfAUser()
    {
        $this->refreshDatabase();
        $user = \App\User::create(['name' => $this->faker->firstName, 'email'=> $this->faker->email,'password'=>$this->faker->password]);
        $medium1 = Medium::create([
            'file' => UploadedFile::fake()->image('file1.png', 800, 400),
            'mediumable_type' => 'App\User',
            'mediumable_id' => $user->id
        ]);
        $medium2 = Medium::create([
            'file' => UploadedFile::fake()->image('file1.png', 800, 400),
            'mediumable_type' => 'App\User',
            'mediumable_id' => $user->id
        ]);
        $this->assertEquals(2, $user->media->count());
    }

    /*--------------------------- HELPER FUNCTIONS -----------------------------*/
    public function attachAnImageToNewMedium()
    {
        $medium = new Medium();
        $medium->attach_file(UploadedFile::fake()->image('file.png', 600, 600));
        return $medium;
    }

    public function attachAFileToNewMedium()
    {
        $medium = new Medium();
        $medium->attach_file(UploadedFile::fake()->create('movie.mp4', 6000));
        return $medium;
    }

    public function uploadAndSaveAFileIntoStorageAndDatabase(){
        return Medium::create([
            'file' => UploadedFile::fake()->create('movie.mp4', 6000)
        ]);
    }
}
