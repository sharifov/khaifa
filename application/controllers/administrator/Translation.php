<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Translation extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('translation');
    }

    public function directory($directory = [])
    {
        $this->data['title'] = translate('index_title');
        $this->data['subtitle'] = translate('index_description');
        $server = site_url_multi();

        $filter_name = $this->input->get('filter_name');
        if (isset($filter_name)) {
            $filter_name = rtrim(str_replace('*', '', $filter_name), '/');
        } else {
            $filter_name = null;
        }
        // Make sure we have the correct directory
        $directory = implode('/', func_get_args());

        if (isset($directory)) {
            $directory = rtrim(APPPATH . "language/" . str_replace('*', '', $directory), '/');
        } else {
            $directory = APPPATH . "language/" . $directory . "/";
        }

        $directories = [];
        $files = [];

        $directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);
        $files = glob($directory . '/' . $filter_name . '*{_lang.php}', GLOB_BRACE);


        foreach ($directories as $directory) {
            $name = basename($directory);
            $folder_href = site_url_multi('administrator/translation/directory/' . substr($directory,
                    strlen(APPPATH . "language/")));

            $this->data['directories'][] = [
                'name' => $name,
                'path' => substr($directory, strlen(APPPATH . "language/")),
                'href' => $folder_href,
            ];
        }

        foreach ($files as $file) {
            $name = basename($file);

            $this->data['files'][] = [
                'name' => $name,
                'path' => substr($file, strlen(APPPATH . "language/")),
                'href' => site_url_multi('administrator/translation/file/' . str_replace('_lang.php', '', substr($file, strlen(APPPATH . "language/"))))
            ];
        }

        $this->template->render($this->controller . '/index');
    }

    public function file($files = [])
    {
        $this->data['title'] = translate('index_title');
        $this->data['subtitle'] = translate('index_description');

        $this->data['buttons'][] = [
            'type' => 'button',
            'text' => translate('form_button_save', true),
            'class' => 'btn btn-primary btn-labeled heading-btn',
            'id' => 'save',
            'icon' => 'icon-floppy-disk',
            'additional' => [
                'onclick' => "confirm('Are you sure?') ? $('#form-save').submit() : false;",
                'form' => 'form-save'
            ]
        ];

        $files = func_get_args();

        $file = implode('/', $files).'_lang.php';
        $pattern_files = array_shift($files);
        $pattern_file = implode('/', $files);

        if (is_array($files)) {
            if ($file !== false && file_exists(APPPATH . "language/$file")) {
                require(APPPATH . "language/$file");
                $lang = (isset($lang)) ? $lang : [];
                $this->data['lang_array'] = $lang;

                $this->data['file'] = $file;
                require(APPPATH . "language/{$this->config->item('language_pattern_lang')}/{$pattern_file}_lang.php");
                $this->data['pattern'] = $lang;
                $this->template->render($this->controller . '/form');
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function save()
    {
        if ($this->input->post('key')) {
            $file = $this->input->post('filename');
            if (!empty($file) && file_exists(APPPATH . "language/$file")) {
                $f = '<?php  if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n"; /// begin file with standard line
                //$f .= "\$lang = array("."\n"; /// our language array

                foreach ($this->input->post('key') as $key => $value) {
                    $f .= '$lang[\'' . $key . '\'] = \'';
                    $f .= addslashes($value) . '\';' . "\n";        ///for language array		, add escaping "
                }

                $f .= '/* End of file ' . $file . ' */'; ///closing tags
                ///Before we go on, copy files just in case.
                ///

                $r = file_put_contents(APPPATH . "language/$file", $f);    ///save language file
                if ($r) {
                    $this->session->set_flashdata('msg', translate('language_file_saved'));
                } else {
                    $this->session->set_flashdata('error', translate('language_file_not_saved'));
                }
                redirect($_SERVER['HTTP_REFERER']);
                redirect(site_url('administrator/translation/file/' . $file));
            } else {
                $this->session->set_flashdata('error', translate('language_error_dir_not_exist'));
                redirect('/language');
            }
        } else {
            $this->session->set_flashdata('error', translate('language_error_no_direct_access'));
            redirect('language');
        }
    }
}
