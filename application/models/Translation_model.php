<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Translation_model
{

    public function get_directories($directory)
    {
        $dir = APPPATH . "language/" . $directory . "/";
        $dh = opendir($dir);

        $i = 0;
        while (false !== ($filename = readdir($dh))) {
            if ($filename !== '.' && $filename !== '..' && is_dir($dir . $filename)) {
                $files[$i]['dir'] = $filename;
                $files[$i]['count'] = $this->get_count_files($dir . $filename);
                $i++;
            }
        }
        return (!empty($files)) ? $files : false;
    }

    /**
     * Get list of files from language directory
     *
     * @param string
     * @return    array
     */

    public function get_files($dir)
    {
        if (!is_dir(APPPATH . "language/$dir/")) {
            return false;
        }
        $dir = APPPATH . "language/$dir/";
        $dh = opendir($dir);
        while (false !== ($filename = readdir($dh))) {
            if ($filename !== '.' && $filename !== '..' && !is_dir($dir . $filename) && pathinfo($filename,
                    PATHINFO_EXTENSION) == 'php' && substr($filename, 0, 7) != 'backup_') {
                $files[] = $filename;
            }
        }
        return (!empty($files)) ? $files : false;
    }

    /**
     * Get number of files from language directory
     *
     * @param string
     * @return    int
     */

    public function get_count_files($dir)
    {
        if (!is_dir(APPPATH . "language/$dir/")) {
            return false;
        }
        $dir = APPPATH . "language/$dir/";
        $dh = opendir($dir);
        $i = 0;
        while (false !== ($filename = readdir($dh))) {
            if ($filename !== '.' && $filename !== '..' && !is_dir($dir . $filename) && pathinfo($filename,
                    PATHINFO_EXTENSION) == 'php' && substr($filename, 0, 7) != 'backup_') {
                $i++;
            }
        }
        return (int)$i;
    }

    /**
     * Get list of languages where file exist
     *
     * @param string
     * @return    array
     */

    public function file_in_language($file)
    {
        $lang = $this->get_languages();
        if ($lang !== false) {
            foreach ($lang as $l) {
                $names = get_filenames(APPPATH . "language/{$l['dir']}/");
                if (in_array($file, $names)) {
                    $in_lang[] = $l['dir'];
                }
            }
            return $in_lang;
        }
        return false;
    }

    /**
     * Delete keys from database if file does not exists in any language
     *
     * @param string
     * @return    bool
     */

    public function delete_keys($file)
    {
        $lang = $this->get_languages();
        if ($lang !== false) {
            foreach ($lang as $l) {
                $names = get_filenames(APPPATH . "language/{$l['dir']}/");
                if (in_array($file, $names)) {
                    return false;
                }
            }

            return true;
        }
    }

}