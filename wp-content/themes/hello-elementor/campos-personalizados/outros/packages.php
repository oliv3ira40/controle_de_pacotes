<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

function packages() {
    Container::make('post_meta', 'Campos personalizados')
        ->where('post_type', '=', 'packages')
        ->add_fields([
            Field::make('multiselect', 'clients', 'Clientes')
                ->add_options([
                    '1' => 'Cliente 1',
                    '2' => 'Cliente 2',
                    '3' => 'Cliente 3',
                ]),
        
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

                    Field::make('checkbox', 'confirm_autonomous_termination', 'Confirme o encerramento sessão (Autônomo)')
                        ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                        ->set_default_value(false),

                    Field::make('text', 'closing_date_autonomous', 'Data do encerramento (Autônomo)')
                        ->set_conditional_logic([ ['field' => 'status', 'value' => 'Finalizada'] ])
                        ->set_attribute('readOnly', true)
                        ->set_attribute('placeholder', 'Ainda não confirmado'),

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
                '),

            Field::make('checkbox', 'close_package', 'Encerrar pacote')
                ->set_default_value(false),
        ]
    );
}   