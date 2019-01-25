<?php

return [
    /* The file will be saved in storage\app\MAIN_UPLOAD_FOLDER you specified */
    'MAIN_UPLOAD_FOLDER' => 'public/upload',

    'CREATE_RESPONSIVE_SIZES'       => true,
    /* ------------------------------------------------------------------
     *  All images created in responsive size stores in below folders inside "images" folder.
     *  If CREATE_RESPONSIVE_SIZES flag goes false, the image just will save into LARGE_IMAGE_FOLDER_PATH
     */
    'LARGE_IMAGE_FOLDER_PATH'       => 'lg',
    'MEDIUM_IMAGE_FOLDER_PATH'      => 'md',
    'SMALL_IMAGE_FOLDER_PATH'       => 'sm',
    'EXTRA_SMALL_IMAGE_FOLDER_PATH' => 'xs',

    /* ------------------------------------------------------------------
     *  Responsive image sizes
     */

    'LARGE_IMAGE_SIZE'      => 0,   //determine that the image store in original size
    'MEDIUM_IMAGE_SIZE'     => 540, // It will save the image in 540x540 pixels
    'SMALL_IMAGE_SIZE'      => 270, // It will save the image in 270x270 pixels
    'EXTRA_SMALL_IMAGE_SIZE'=> 135, // It will save the image in 135x135 pixels

    'STORE_FILE_NAME_HASHED' => false
];
