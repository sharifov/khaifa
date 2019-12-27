<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wc_table
{

    protected $CI;

    public $rows;

    public $columns;

    public $custom_rows;

    public $action;

    public $module = false;

    public $url;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Custom_model');
    }

    public function set_columns($columns)
    {
        $this->columns = $columns;
    }

    public function set_custom_rows($custom_rows)
    {
        $this->custom_rows = $custom_rows;
    }

    public function set_rows($rows)
    {
        $this->rows = $rows;
    }

    public function set_action($action)
    {
        $this->action = $action;
    }

    public function set_module($module)
    {
        $this->module = $module;
        if ($this->module) {
            $this->url = $this->CI->directory . $this->CI->module_name;
        } else {
            $this->url = $this->CI->directory . $this->CI->controller;
        }
    }

    public function format_rows()
    {
        if ($this->rows) {
            foreach ($this->rows as $key => $row) {

                $formatted_rows[$key]['checkbox'] = '<input type="checkbox" name="selected[]" value="' . $row->id . '" class="styled">';
                foreach ($row as $column => $data) {
                    $formatted_rows[$key][$column] = $data;

                    if ($this->custom_rows) {
                        foreach ($this->custom_rows as $custom_row) {
                            if ($column == $custom_row['column']) {
                                if (isset($custom_row['callback'])) {
                                    $formatted_rows[$key][$custom_row['column']] = $this->CI->Custom_model->{'callback_' . $custom_row['callback']}($data, $custom_row['params'], $row->id);
                                }
                            }
                        }
                    }
                }

                if ($this->action) {
                    $formatted_rows[$key]['action'] = '<ul class="icons-list">';

                    if (isset($this->action['custom']) && !empty($this->action['custom'])) {
                       
                        foreach ($this->action['custom'] as $custom) {
                           
                            if(isset($custom['href_value2']) && !empty($custom['href_value2']))
                            {
                                if($row->{$custom['href_value2']} == 0)
                                {
                                    $formatted_rows[$key]['action'] .= '<li><a href="' . $custom['href'];
                                    if (is_array($custom['href_value'])) {
                                        $values = [];
                                        foreach ($custom['href_value'] as $href_value) {
                                            $values[] = $row->$href_value;
                                        }
        
                                        $formatted_rows[$key]['action'] .= implode('/', $values);;
                                    } else {
                                        $formatted_rows[$key]['action'] .= $row->{$custom['href_value']};
                                    }

                                    $formatted_rows[$key]['action'] .= '" data-popup="tooltip" title="' . $custom['text'] . '"><i class="' . $custom['icon'] . '"></i></a></li>';
                                }
                            }
                            else
                            {
                                $formatted_rows[$key]['action'] .= '<li><a href="' . $custom['href'];
                                if (is_array($custom['href_value'])) {
                                    $values = [];
                                    foreach ($custom['href_value'] as $href_value) {
                                        $values[] = $row->$href_value;
                                    }
    
                                    $formatted_rows[$key]['action'] .= implode('/', $values);;
                                } else {
                                    $formatted_rows[$key]['action'] .= $row->{$custom['href_value']};
                                }

                                $formatted_rows[$key]['action'] .= '" data-popup="tooltip" title="' . $custom['text'] . '"><i class="' . $custom['icon'] . '"></i></a></li>';
                            }
                            

                            
                        }
                    }
                    if (isset($this->action['show']) && $this->action['show'] === true) {
                        $formatted_rows[$key]['action'] .= '<li><a href="' . site_url_multi($this->url . '/show/') . $row->id . '" data-popup="tooltip" title="' . translate('show',
                                true) . '"><i class="icon-eye"></i></a></li>';
                    }
                    if (isset($this->action['edit']) && $this->action['edit'] === true) {
                        $formatted_rows[$key]['action'] .= '<li><a href="' . site_url_multi($this->url . '/edit/') . $row->id . '" data-popup="tooltip" title="' . translate('edit',
                                true) . '"><i class="icon-pencil7"></i></a></li>';
                    }
                    if (isset($this->action['delete']) && $this->action['delete'] === true) {
                        $formatted_rows[$key]['action'] .= '<li><a href="' . site_url_multi($this->url . '/delete/') . $row->id . '" class="delete" data-popup="tooltip" title="' . translate('delete',
                                true) . '"><i class="icon-trash"></i></a></li>';
                    }
                    if (isset($this->action['restore']) && $this->action['restore'] === true) {
                        $formatted_rows[$key]['action'] .= '<li><a href="' . site_url_multi($this->url . '/restore/') . $row->id . '" class="restore" data-popup="tooltip" title="' . translate('restore',
                                true) . '"><i class="icon-reset"></i></a></li>';
                    }
                    if (isset($this->action['remove']) && $this->action['remove'] === true) {
                        $formatted_rows[$key]['action'] .= '<li><a href="' . site_url_multi($this->url . '/remove/') . $row->id . '" class="remove" data-popup="tooltip" title="' . translate('remove',
                                true) . '"><i class="icon-cancel-circle2"></i></a></li>';
                    }
                    $formatted_rows[$key]['action'] .= '</ul>';
                }
            }

            return $formatted_rows;
        }
    }

    public function format_heading()
    {
        $table_heads[] = "<th style=\"width:1px;\"><input type=\"checkbox\" class=\"styled\" onclick=\"$('input[name*=\'selected\']').prop('checked', this.checked); $.uniform.update();\"></th>";

        if (in_array($this->CI->input->get('order'), ['ASC', 'DESC'])) {
            $next_order = ($this->CI->input->get('order') == 'ASC') ? 'DESC' : 'ASC';
        } else {
            $next_order = 'ASC';
        }

        if ($this->columns) {
            foreach ($this->columns as $column => $column_data) {

                
                $column_title = (isset($column_data['table'][$this->CI->data['current_lang']])) ? $column_data['table'][$this->CI->data['current_lang']] : $column_data['table'][$this->CI->data['default_language']];

                $table_heads[] = '<th class="column_' . $column . '"><a href="' . current_url() . '?per_page=' . $this->CI->data['per_page'] . '&column=' . $column . '&order=' . $next_order . '">' . $column_title;
                if ($this->CI->input->get('column') == $column) {
                    $table_heads[] .= '<i class="icon-order-' . strtolower($next_order) . ' pull-right"></i>';
                } else {
                    $table_heads[] .= '<i class="icon-menu-open pull-right"></i>';
                }

                $table_heads[] .= '</a></th>';
            }
        }
        if ($this->action) {
            $table_heads[] = "<th style='width:auto;'><i class='icon-menu7'></i></th>";
        }

        return $table_heads;
    }

    public function generate()
    {
        $format_heading = $this->format_heading();
        $this->CI->table->set_heading($format_heading);
        $formatted_rows = $this->format_rows();
        if (!empty($formatted_rows)) {
            return $this->CI->table->generate($formatted_rows);
        } else {
            $cell = [
                'data' => translate('empty_data', true),
                'class' => 'text-center',
                'colspan' => count($this->columns) + 2
            ];
            $this->CI->table->add_row($cell);
            return $this->CI->table->generate();
        }
    }
}