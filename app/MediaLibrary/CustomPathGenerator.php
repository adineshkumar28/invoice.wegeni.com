<?php

namespace App\MediaLibrary;

use App\Models\AdminTestimonial;
use App\Models\Payment;
use App\Models\PaymentQrCode;
use App\Models\Product;
use App\Models\SectionOne;
use App\Models\SectionThree;
use App\Models\Setting;
use App\Models\SuperAdminSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSetting;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Class CustomPathGenerator
 */
class CustomPathGenerator implements PathGenerator
{
public function getPath(Media $media): string
{
    $model = $media->model;

    // Define a base path format (with placeholder)
    $path = '{PARENT_DIR}/' . $media->id . '/';

    switch (get_class($model)) {
        case SectionOne::class:
            return str_replace('{PARENT_DIR}', SectionOne::SECTION_ONE_PATH, $path);

        case SectionThree::class:
            return str_replace('{PARENT_DIR}', SectionThree::SECTION_THREE_PATH, $path);

        case AdminTestimonial::class:
            return str_replace('{PARENT_DIR}', AdminTestimonial::PATH, $path);

        case Payment::class:
            return str_replace('{PARENT_DIR}', Payment::PAYMENT_ATTACHMENT, $path);

        case Transaction::class:
            return str_replace('{PARENT_DIR}', Transaction::PAYMENT_ATTACHMENTS, $path);

        case PaymentQrCode::class:
            return str_replace('{PARENT_DIR}', PaymentQrCode::PAYMENT_QR_CODE, $path);

        case UserSetting::class:
            return str_replace('{PARENT_DIR}', UserSetting::USER_SETTING_IMAGE, $path);

        default:
            return 'default-path/';
    }
}



    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'thumbnails/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'rs-images/';
    }
}
