<?php

// ACCESS_TOKEN_SECRET
putenv('ACCESS_TOKEN_SECRET=adadeca5c7e7e270b986f0fae5e32f7468ec9c00876d6bbb73f2eccbfc6affa612735a9d65dea4245038e137d09e5b310578f528c637b25de7a11758f5cde322');

// REFRESH_TOKEN_SECRET
putenv('REFRESH_TOKEN_SECRET=dcaa7165024d146253704fc6635d7b6f973827ff8daa8a51806e6c705a9a0dab958924d5076e446d9e269ae689372a6b1b522d2b076d9ae02e1c279a16480b6c');


// PERSONAS_DATA_DIR
putenv('PERSONAS_FILENAME=' . __DIR__ . '\..\data\personas.json');

// PRODUCTS_DATA_DIR
putenv('PRODUCTS_FILENAME=' . __DIR__ . '\..\data\products.json');

// ORDERS_DATA_DIR
putenv('ORDERS_FILENAME=' . __DIR__ . '\..\data\orders.txt');


// DEFAULT_IMAGE_DIR
putenv('DEFAULT_IMAGE_DIR=' . __DIR__ . '\..\data\img');

// PHOTO_WATERMARK_DIR
putenv('PHOTO_WATERMARK_DIR=' . __DIR__ . '\..\data\img\watermark.png');

// BUCKUP_IMAGE_DIR
putenv('BUCKUP_IMAGE_DIR=' . __DIR__ . '\..\data\bak');