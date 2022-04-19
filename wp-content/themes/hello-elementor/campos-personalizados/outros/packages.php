<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

function packages() {
    $list_page = str_contains($_SERVER['REQUEST_URI'], 'edit.php');
    $new_page = str_contains($_SERVER['REQUEST_URI'], 'post-new.php');
    if (
        (isset($_SERVER['REQUEST_URI']) AND $new_page AND $_REQUEST['post_type'] == 'packages') OR
        (isset($_REQUEST['action']) AND $_REQUEST['action'] != 'editpost') OR
        (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'editpost' AND $_REQUEST['post_type'] == 'packages') OR
        $list_page
    ) {
        $current_user = wp_get_current_user();
        $role = $current_user->roles[0] ?? '';
    
        $clients_by_id = array_column(get_clients(), 'display_name', 'ID');
        $default_fields = [
            Field::make('text', 'name', 'Nome do pacote'),
    
            Field::make('rich_text', 'description', 'Descrição do pacote'),
    
            Field::make('multiselect', 'clients', 'Clientes')
                ->add_options($clients_by_id ?? []),
        
            Field::make('complex', 'sections_packages', 'Sessões')
                ->set_layout('tabbed-vertical')
                ->add_fields([
                    Field::make('radio', 'status', 'Status')
                        ->add_options([
                            'Não finalizada' => 'Não finalizada',
                            'Finalizada' => 'Finalizada',
                        ]),
    
                    Field::make('date_time', 'expected_date', 'Data prevista'),
                    
                    Field::make('textarea', 'description_section', 'Descrição da sessão'),
    
                    Field::make('checkbox', 'confirm_autonomous_termination', 'Confirme o encerramento sessão (Profissional)')
                        ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                        ->set_default_value(false),

                    Field::make('hidden', 'confirm_client_termination', 'Confirme o encerramento sessão (Cliente)')
                        ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                        ->set_default_value(false),
    
                    Field::make('date_time', 'closing_date_autonomous', 'Data do encerramento (Profissional)')
                        ->set_conditional_logic([ ['field' => 'confirm_autonomous_termination', 'value' => true] ])
                        ->set_attribute('placeholder', 'Ainda não confirmado')
                        ->set_help_text('Este campo é obrigatório para que o cliente seja notificado sobre o termino da sessão via e-mail'),
    
                    Field::make('text', 'closing_date_client', 'Data do encerramento (Cliente)')
                        ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                        ->set_attribute('readOnly', true)
                        ->set_attribute('placeholder', 'Ainda não confirmado'),
                ])
                ->set_header_template('
                    <div style="color: <%- status == "Finalizada" ? "green" : "red" %>;">
                        Sessão: <%- ++ $_index %>
                        (<%- status == "Finalizada" ? status : (
                            expected_date ? expected_date : "Não finalizada"
                        ) %>)
                    </div>
                '),
    
            Field::make('checkbox', 'close_package', 'Encerrar pacote')
                ->set_default_value(false),
        ];
    
        if ((isset($_REQUEST['post_type']) AND $_REQUEST['post_type'] == 'packages') AND ($list_page OR $new_page)) {
            $fields = $default_fields;
        } else {
    
            if ($role == 'clients') {
                $post = get_post($_REQUEST['post'] ?? $_REQUEST['post_ID']);

                global $wpdb;
                $query_post = "
                    SELECT meta_value as client_id FROM wp_postmeta
                    WHERE post_id = $post->ID
                    AND meta_key LIKE '_clients%'
                ";
                $clients = $wpdb->get_results($query_post);
                $clients = array_column($clients, 'client_id');
                $clients_names = get_users(['include'=>$clients]);
                $clients_names = implode(', ', array_column($clients_names, 'display_name'));
                
                $fields_clients = [
                    Field::make('text', 'name', 'Nome do pacote')
                        ->set_attribute('readOnly', true),
    
                    Field::make('textarea', 'description', 'Descrição do pacote')
                        ->set_attribute('readOnly', true),
    
                    Field::make('html', 'crb_information_text')
                        ->set_html('
                            <b>Clientes</b>
                            <div style="margin-top: 6px; padding: 0 8px; line-height: 2; min-height: 30px; width: initial !important; vertical-align: middle; background-color: #f0f0f1; box-shadow: 0 0 0 transparent; border-radius: 4px; border: 1px solid #8c8f94; color: #2c3338;">
                                '.$clients_names.'
                            </div>
                        '),
                
                    Field::make('complex', 'sections_packages', 'Sessões')
                        ->set_layout('tabbed-vertical')
                        ->set_classes('fields_clients')
                        ->add_fields([
                            Field::make('text', 'status', 'Status')->set_attribute('readOnly', true),
    
                            Field::make('text', 'expected_date', 'Data prevista')->set_attribute('readOnly', true),
    
                            Field::make('textarea', 'description_section', 'Descrição da sessão')->set_attribute('readOnly', true),
    
                            Field::make('text', 'closing_date_autonomous', 'Data do encerramento (Profissional)')
                                ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                                ->set_attribute('placeholder', 'Ainda não confirmado')
                                ->set_attribute('readOnly', true),
    
                            Field::make('checkbox', 'confirm_client_termination', 'Confirme o encerramento sessão (Cliente)')
                                ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                                ->set_default_value(false),
    
                            Field::make('text', 'closing_date_client', 'Data do encerramento (Cliente)')
                                ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                                ->set_attribute('readOnly', true)
                                ->set_attribute('placeholder', 'Ainda não confirmado'),
    
                        ])
                        ->set_header_template('
                            <div style="color: <%- status == "Finalizada" ? "green" : "red" %>;">
                                Sessão: <%- ++ $_index %>
                                (<%- status == "Finalizada" ? status : (
                                    expected_date ? expected_date : "Não finalizada"
                                ) %>)
                            </div>
                        ')
                        ->set_duplicate_groups_allowed(false),
                ];

                $fields = $fields_clients;
            } else {
                $fields = $default_fields;
            }
        }

        Container::make('post_meta', 'Campos personalizados')
            ->where('post_type', '=', 'packages')
            ->where('current_user_role', 'IN', array('administrator', 'autonomous', 'clients'))
            ->add_fields($fields);
    }
}