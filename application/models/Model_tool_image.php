<?php

class Model_tool_image
{
    public function resize($filename, $width, $height)
    {
        if (!is_file(DIR_IMAGE . $filename)) {
            return false;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $image_old = $filename;
        $image_new = 'cache/' . substr($filename, 0,
                strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

        if (!is_file(DIR_IMAGE . $image_new) || (filectime(DIR_IMAGE . $image_old) > filectime(DIR_IMAGE . $image_new))) {
            list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);

            if (!in_array($image_type, [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF])) {
                return DIR_IMAGE . $image_old;
            }

            $path = '';

            $directories = explode('/', dirname($image_new));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!is_dir(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            if ($width_orig != $width || $height_orig != $height) {
                $image = new Image(DIR_IMAGE . $image_old);
                $image->resize($width, $height);
                $image->save(DIR_IMAGE . $image_new);
            } else {
                copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
            }
        }
        return site_url() . 'uploads/' . $image_new;

    }
}
