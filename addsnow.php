<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Addsnow extends Module
{
    public function __construct()
    {
        $this->name = 'addsnow';
        $this->tab = 'frontend';
        $this->version = '1.0.0';
        $this->author = 'Łukasz Ryszkiewicz';
        $this->author_uri = 'https://ryszkiewicz.cloud';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.7',
            'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Add falling snowflakes');
        $this->description = $this->l('This module adds falling snowflakes to your store');
        $this->confirmUninstall = $this->l('Uninstall module?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install() &&
            Configuration::updateValue('ADDSNOW_ENABLED', true) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_NUMBER_DENSITY_VALUE_AREA', 1200) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_SIZE_VALUE', true) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_COLOR_VALUE', true) &&
            Configuration::updateValue('ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER', true) &&
            $this->registerHook('header') &&
            $this->registerHook('displayBeforeBodyClosingTag');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            Configuration::deleteByName('ADDSNOW_ENABLED');
    }

    public function getContent()
    {
        $output = '';
        
        if (Tools::isSubmit('submit_'.$this->name)) {
            if ($this->postProcess()) {
                $output .= $this->displayConfirmation($this->l('Settings saved'));
            } else {
                $output .= $this->displayWarning($this->l('Something went wrong! Check form values.'));
            }
        }
            
        $vars = array(
            $this->name . '_name' => $this->displayName,
        );
        
        $this->context->smarty->assign($vars);

        $output .= '<h1>'.$this->displayName.'</h1>';
        $output .= $this->displayForm();

        return $output;
    }

    public function displayForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Snow?'),
                        'name' => 'ADDSNOW_ENABLED',
                        'is_bool' => true,
                        'desc' => $this->l('Set "Yes" to enable animated snow on your store.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('On')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Off')
                            )
                        ),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'color',
                        'desc' => $this->l('Color'),
                        'name' => 'ADDSNOW_PARTICLES_COLOR_VALUE',
                        'label' => $this->l('Container ID:'),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('Opacity [0.1 to 1]. "1" means no opacity, 0.7 recommended'),
                        'name' => 'ADDSNOW_PARTICLES_OPACITY_VALUE',
                        'label' => $this->l('Opacity:'),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('Density of snow flakes.'),
                        'name' => 'ADDSNOW_PARTICLES_NUMBER_DENSITY_VALUE_AREA',
                        'label' => $this->l('Density:'),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('Snow flakes size:'),
                        'name' => 'ADDSNOW_PARTICLES_SIZE_VALUE',
                        'label' => $this->l('Size in px of snow flakes.'),
                        'class' => '.fixed-width-xs'
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('Move speed:'),
                        'name' => 'ADDSNOW_PARTICLES_MOVE_SPEED',
                        'label' => $this->l('Move speed of snow flakes.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Snow?'),
                        'name' => 'ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER',
                        'is_bool' => true,
                        'desc' => $this->l('ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('On')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Off')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->fields_value['ADDSNOW_ENABLED'] = Configuration::get('ADDSNOW_ENABLED');
        $helper->fields_value['ADDSNOW_PARTICLES_NUMBER_DENSITY_VALUE_AREA'] = Configuration::get('ADDSNOW_PARTICLES_NUMBER_DENSITY_VALUE_AREA');
        $helper->fields_value['ADDSNOW_PARTICLES_SIZE_VALUE'] = Configuration::get('ADDSNOW_PARTICLES_SIZE_VALUE');
        $helper->fields_value['ADDSNOW_PARTICLES_MOVE_SPEED'] = Configuration::get('ADDSNOW_PARTICLES_MOVE_SPEED');
        $helper->fields_value['ADDSNOW_PARTICLES_OPACITY_VALUE'] = Configuration::get('ADDSNOW_PARTICLES_OPACITY_VALUE');
        $helper->fields_value['ADDSNOW_PARTICLES_COLOR_VALUE'] = Configuration::get('ADDSNOW_PARTICLES_COLOR_VALUE');
//        $helper->fields_value['ADDSNOW_PARTICLES_MOVE_SPEED'] = Configuration::get('ADDSNOW_PARTICLES_MOVE_SPEED');
        $helper->fields_value['ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER'] = Configuration::get('ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER');

        

        return $helper->generateForm(array($fields_form));
    }

    // Save config form
    protected function postProcess()
    {
        if (
            Configuration::updateValue('ADDSNOW_ENABLED', (bool)Tools::getValue('ADDSNOW_ENABLED')) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_NUMBER_DENSITY_VALUE_AREA', Tools::getValue('ADDSNOW_PARTICLES_NUMBER_DENSITY_VALUE_AREA')) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_SIZE_VALUE', Tools::getValue('ADDSNOW_PARTICLES_SIZE_VALUE')) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_MOVE_SPEED', Tools::getValue('ADDSNOW_PARTICLES_MOVE_SPEED')) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_OPACITY_VALUE', Tools::getValue('ADDSNOW_PARTICLES_OPACITY_VALUE')) &&
            Configuration::updateValue('ADDSNOW_PARTICLES_COLOR_VALUE', Tools::getValue('ADDSNOW_PARTICLES_COLOR_VALUE')) &&
            Configuration::updateValue('ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER', Tools::getValue('ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER'))
            ) {
            return true;
        } else {
            return false;
        }
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js', 'all');
        $this->context->controller->addCSS($this->_path.'views/css/front.css', 'all');
    }

    public function hookDisplayBeforeBodyClosingTag()
    {
        $vars = [
            'addsnow_enabled'        => (bool)Configuration::get('ADDSNOW_ENABLED'),
            'addsnow_particles_number_density_value_area'   => Configuration::get('ADDSNOW_PARTICLES_NUMBER_DENSITY_VALUE_AREA'),
            'addsnow_particles_size_value'   => Configuration::get('ADDSNOW_PARTICLES_SIZE_VALUE'),
            'addsnow_particles_move_speed'   => Configuration::get('ADDSNOW_PARTICLES_MOVE_SPEED'),
            'addsnow_particles_opacity_value'   => Configuration::get('ADDSNOW_PARTICLES_OPACITY_VALUE'),
            'addsnow_particles_color_value'   => Configuration::get('ADDSNOW_PARTICLES_COLOR_VALUE'),
            //'addsnow_particles_opacity_value'   => Configuration::get('ADDSNOW_PARTICLES_MOVE_SPEED'),
            
            'addsnow_interactivity_events_onhoover'   => Configuration::get('ADDSNOW_INTERACTIVITY_EVENTS_ONHOOVER')
        ];

        $this->context->smarty->assign($vars);

        return $this->fetch('module:'.$this->name.'/views/templates/hook/beforebodyclosingtag.tpl');
    }
}
