<?php

/**
 * Plugin Name: LPCode Flutuante BTN
 * Plugin URI: https://github.com/lpcodedev/lpcode-flutuante-btn
 * Description: This is a plugin to facilitate the installation of a floating button that will direct users to WhatsApp.
 * Version: 1.2
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * Author: lpcode
 * Author URI: https://lpcode.com.br
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: lpcode-flutuante-btn 
 * Domain Path: /language
 */

 defined('ABSPATH') or die('Olá, esta perdido?');

 define('LPCODE_BUTTON_PATH', plugin_dir_path(__FILE__));
 define('LPCODE_BUTTON_URL', plugin_dir_url(__FILE__));

 class lpcode_button_whatsButton{

    function __construct(){
        add_action('admin_menu', array($this,'lpcode_buttonMenu'));
        add_action('admin_init', array($this,'settings'));
        add_action('wp_enqueue_scripts', array($this,'lpcode_buttonMainLink'));
        add_action('wp_footer', array($this,'ifWrap'));
    }

    function lpcode_buttonMainLink(){
            wp_enqueue_style('LPCode-flutuante-btn-style', LPCODE_BUTTON_URL.'assets/css/btn-style.css',false,'1.0','all');
    }

    // Chama a função para imprimir o botão na tela
    function ifWrap(){
        if(
            get_option('lpcode_button_numero') != '' &&
            get_option('lpcode_button_numero') != '0' ){
                return $this->lpcode_buttonButtonHtml();
            }
    }

    // O botão
    function lpcode_buttonButtonHtml(){
        echo '
        <div class="whats-btn '.esc_attr($this->setPosition()).' '. esc_attr($this->setAnimate()).' " style="'. esc_attr($this->setSize()).' '. esc_attr($this->setBottom()).' '. esc_attr($this->setDistance()).'">
            <a href="'.esc_url($this->makeLink()).'" target="_blank"><img src="'.esc_url($this->setIcon()).'" alt="Link para o WhatsApp"></a>
        </div> 
        ';
    }

    // Montar o link
    function makeLink() {
        $num = trim(get_option('lpcode_button_numero','0'));

        $msg = str_replace(' ', '%20', get_option('lpcode_button_mensagem', ' '));

        $link = 'https://wa.me/'.esc_html($num);

        if($msg != '' && $msg != ' '){
            $link .= '?text=' . $msg;
        }

        return $link;
    }

    // Definir o ícone
    function setIcon(){
        $icon = '';

        if(get_option('lpcode_button_icon', '0') == '0'){
            $icon = 'Whatsapp_37229.png';
        }elseif(get_option('lpcode_button_icon', '1') == '1'){
            $icon = 'whatsapp_op1.svg';
        }elseif(get_option('lpcode_button_icon', '2') == '2'){
            $icon = 'whatsapp_op2.svg';
        }elseif(get_option('lpcode_button_icon', '3') == '3'){
            $icon = 'whatsapp_op3.svg';
        }elseif(get_option('lpcode_button_icon', '4') == '4'){
            $icon = 'whatsapp_op4.svg';
        }elseif(get_option('lpcode_button_icon', '5') == '5'){
            $icon = 'whatsapp_op5.svg';
        }

        $imgURL = LPCODE_BUTTON_URL.'assets/images/'.$icon;

        return $imgURL;
    }

    // Definir Animação
    function setAnimate(){
        $animation = '';

        if(get_option('lpcode_button_animate','1') == '1' ){
            $animation = 'animate-jump';
        }elseif(get_option('lpcode_button_animate','2') == '2'){
            $animation = 'animate-zoom';
        }elseif(get_option('lpcode_button_animate','0') == '0'){
            $animation = ' ';
        }

        return $animation;
    }

    // Definir o lado
    function setPosition(){
        $position = '';

        if(get_option('lpcode_button_local','0') == '0'){
            $position = 'right';
        }elseif(get_option('lpcode_button_local','1') == '1'){
            $position = 'left';
        }

        return $position;
    }

    // Definir o tamanho
    function setSize(){
        if(get_option('lpcode_button_size', '45')){
            $size = '--button-size: '.esc_attr(get_option('lpcode_button_size')).'px;';
        }else{
            $size = '--button-size: 45px;';
        }

        return $size;
    }

    // Definir o a distancia do bottom
    function setBottom(){
        if(get_option('lpcode_button_bottom', '10')){
            $bottom = '--button-bottom: '.esc_attr(get_option('lpcode_button_bottom')).'px;';
        }else{
            $bottom = '--button-bottom: 10px;';
        }

        return $bottom;
    }

    // Definir o a distancia das laterais
    function setDistance(){
        if(get_option('lpcode_button_distance', '10')){
            $distance = '--button-distance: '.esc_attr(get_option('lpcode_button_distance')).'px;';
        }else{
            $distance = '--button-distance: 20px;';
        }

        return $distance;
    }

    // Criando o Banco de Dados
    function settings(){
        add_settings_section('lpcode_button_first_section', null, null, 'lpcode-button-wts-link-app');

        // Numero
        add_settings_field('lpcode_button_numero', 'Número do telefone: ', array($this, 'numeroHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton', 'lpcode_button_numero', array('sanitize_callback' => array($this, 'sanitizeNumero'), 'default' => '0'));

        // Mensagem
        add_settings_field('lpcode_button_mensagem','Mensagem Padrão: ', array($this, 'mensagemHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton', 'lpcode_button_mensagem', array('sanitize_callback' => array($this, 'sanitizeMensagem'), 'default' => ' '));

        // Ícone
        add_settings_field('lpcode_button_icon', 'Estilo do ícone: ', array($this, 'iconHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton', 'lpcode_button_icon', array('sanitize_callback' => array($this,'sanitizeIcons'), 'default' => '0'));

        // Tamanho
        add_settings_field('lpcode_button_size', 'Tamanho do botão (px): ', array($this, 'sizeHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton', 'lpcode_button_size', array('sanitize_callback' => array($this, 'sanitizeNumero'), 'default' => '45'));

        // Distancia Bottom
        add_settings_field('lpcode_button_bottom', 'Distancia inferior (px): ', array($this, 'bottomHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton', 'lpcode_button_bottom', array('sanitize_callback' => array($this, 'sanitizeNumero'), 'default' => '10'));

        // Distancia laterais
        add_settings_field('lpcode_button_distance', 'Distancia da lateral (px): ', array($this, 'distanceHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton', 'lpcode_button_distance', array('sanitize_callback' => array($this, 'sanitizeNumero'), 'default' => '20'));

        // Posição
        add_settings_field('lpcode_button_local', 'Escolha a posição do botão: ', array($this, 'localHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton','lpcode_button_local', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));

        // Animação
        add_settings_field('lpcode_button_animate', 'Escolha a animação do botão:', array($this, 'animacaoHTML'), 'lpcode-button-wts-link-app', 'lpcode_button_first_section');
        register_setting('lpcode_whatsappButton', 'lpcode_button_animate', array('sanitize_callback' => array($this, 'sanitizeAnimation'), 'default' =>'1'));
    }

    // Sanitizers

    function sanitizeNumero($input){
        $number = sanitize_text_field($input);
        $safe_number = intval($number);
        if(! $safe_number){
            add_settings_error('lpcode_button_numero', 'lpcode_button_numero_error', 'O Telefone precisa conter apenas número!');
            return $safe_number = '0';
        }

        return $safe_number;
    }

    function sanitizeMensagem($input){
        $msg = sanitize_text_field($input);
        return $msg;
    }

    function sanitizeLocation($input){
        if($input != '0' && $input != '1'){
            add_settings_error('lpcode_button_local','lpcode_button_local_error', 'Opção inválida, escolha uma opção válida.');
            $input = get_option('lpcode_button_local');
            return $input;
        }
        return $input;
    }

    function sanitizeAnimation($input){
        if($input != '0' && $input != '1' && $input != '2'){
            add_settings_error('lpcode_button_animate','lpcode_button_animate_error', 'Opção inválida, escolha uma opção válida.');
            $input = get_option('lpcode_button_animate');
            return $input;
        }
        return $input;
    }

    function sanitizeIcons($input){
        if($input != '0' && $input != '1' && $input != '2' && $input != '3' && $input != '4' && $input != '5'){
            add_settings_error('lpcode_button_icon','lpcode_button_icon_error', 'Opção inválida, escolha uma opção válida.');
            $input = get_option('lpcode_button_icon');
            return $input;
        }
        return $input;
    }

    // HTMLs

    function numeroHTML(){ ?>
        <input type="text" name="lpcode_button_numero" id="lpcode_button_numero" value="<?php echo esc_attr(get_option('lpcode_button_numero')); ?>" placeholder="55 19 980808080" pattern="[0-9]{0~20}">
        <label for="lpcode_button_numero">O telefone precisa conter o <b>código do país e o DDD</b>.</label>
    <?php
    }

    function mensagemHTML(){ ?>
        <input type="text" name="lpcode_button_mensagem" value="<?php echo esc_attr(get_option('lpcode_button_mensagem')); ?>" placeholder="Digite uma mensagem padrão. (opcional)">
    <?php
    }

    function localHTML(){ ?>
        <select name="lpcode_button_local">
            <option value="0" <?php selected(get_option('lpcode_button_local'), '0'); ?> >Direita</option>
            <option value="1" <?php selected(get_option('lpcode_button_local'), '1'); ?> >Esquerda</option>
        </select>
    <?php
    }

    function animacaoHTML(){ ?>
        <select name="lpcode_button_animate">
            <option value="0" <?php selected(get_option('lpcode_button_animate'), '0'); ?> >Nenhuma</option>
            <option value="1" <?php selected(get_option('lpcode_button_animate'), '1'); ?> >Jump</option>
            <option value="2" <?php selected(get_option('lpcode_button_animate'), '2'); ?> >Zoom</option>
        </select>
    <?php
    }

    function iconHTML(){ ?>
        <select name="lpcode_button_icon">
            <option value="0" <?php selected(get_option('lpcode_button_icon'), '0'); ?> >Style 1</option>
            <option value="1" <?php selected(get_option('lpcode_button_icon'), '1'); ?> >Style 2</option>
            <option value="2" <?php selected(get_option('lpcode_button_icon'), '2'); ?> >Style 3</option>
            <option value="3" <?php selected(get_option('lpcode_button_icon'), '3'); ?> >Style 4</option>
            <option value="4" <?php selected(get_option('lpcode_button_icon'), '4'); ?> >Style 5</option>
            <option value="5" <?php selected(get_option('lpcode_button_icon'), '5'); ?> >Style 6</option>
        </select>
    <?php
    }

    function sizeHTML(){ ?>
        <input type="number" name="lpcode_button_size" value="<?php echo esc_attr(get_option('lpcode_button_size')); ?>" min='10' max='150'>
    <?php
    }

    function bottomHTML(){ ?>
        <input type="number" name="lpcode_button_bottom" value="<?php echo esc_attr(get_option('lpcode_button_bottom')); ?>" min='-150' max='2000'>
    <?php
    }

    function distanceHTML(){ ?>
        <input type="number" name="lpcode_button_distance" value="<?php echo esc_attr(get_option('lpcode_button_distance')); ?>" min='-50' max='2000'>
    <?php
    }

    function lpcode_buttonMenu(){
        add_menu_page(
            'WtsLink',
            'WtsLink',
            'manage_options',
            'lpcode-button-wts-link-app',
            array($this, 'lpcode_buttonMenuHtml'),
            'dashicons-whatsapp',
            3
        );
    }

    function lpcode_buttonMenuHtml(){
        ?>
        <div class="wrap">
            <h1>WhatsApp Button</h1>
            <form action="options.php" method="post">
                <?php
                    settings_fields('lpcode_whatsappButton');
                    do_settings_sections('lpcode-button-wts-link-app');
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

$lpcode_whatsappButton = new lpcode_button_whatsButton();

?>