<?php

/**
 * Plugin Name: LPCode Flutuante BTN
 * Plugin URI: https://github.com/lpcodedev/lpcode-flutuante-btn
 * Description: This is a plugin to facilitate the installation of a floating button that will direct users to WhatsApp.
 * Version: 1.0
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

 define('LPW_PATH', plugin_dir_path(__FILE__));
 define('LPW_URL', plugin_dir_url(__FILE__));

 class LPWhatsButton{

    function __construct(){
        add_action('admin_menu', array($this,'lpwMenu'));
        add_action('admin_init', array($this,'settings'));
        add_action('wp_enqueue_scripts', array($this,'lpwMainLink'));
        add_action('wp_footer', array($this,'ifWrap'));
    }

    function lpwMainLink(){
            wp_enqueue_style('LPCode-flutuante-btn-style', LPW_URL.'assets/css/btn-style.css',false,'1.0','all');
    }

    // Chama a função para imprimir o botão na tela
    function ifWrap(){
        if(
            get_option('lpw_numero') != '' &&
            get_option('lpw_numero') != '0' ){
                return $this->lpwButtonHtml();
            }
    }

    // O botão
    function lpwButtonHtml(){
        echo '
        <div class="whats-btn '.esc_attr($this->setPosition()).' '. esc_attr($this->setAnimate()).' " style="'. esc_attr($this->setSize()).'">
            <a href="'.esc_url($this->makeLink()).'" target="_blank"><img src="'.esc_url($this->setIcon()).'" alt="Link para o WhatsApp"></a>
        </div> 
        ';
    }

    // Montar o link
    function makeLink() {
        $num = trim(get_option('lpw_numero','0'));

        $msg = str_replace(' ', '%20', get_option('lpw_mensagem', ' '));

        $link = 'https://wa.me/'.esc_html($num);

        if($msg != '' && $msg != ' '){
            $link .= '?text=' . $msg;
        }

        return $link;
    }

    // Definir o ícone
    function setIcon(){
        $icon = '';

        if(get_option('lpw_icon', '0') == '0'){
            $icon = 'Whatsapp_37229.png';
        }elseif(get_option('lpw_icon', '1') == '1'){
            $icon = 'whatsapp_op1.svg';
        }elseif(get_option('lpw_icon', '2') == '2'){
            $icon = 'whatsapp_op2.svg';
        }elseif(get_option('lpw_icon', '3') == '3'){
            $icon = 'whatsapp_op3.svg';
        }elseif(get_option('lpw_icon', '4') == '4'){
            $icon = 'whatsapp_op4.svg';
        }elseif(get_option('lpw_icon', '5') == '5'){
            $icon = 'whatsapp_op5.svg';
        }

        $imgURL = LPW_URL.'assets/images/'.$icon;

        return $imgURL;
    }

    // Definir Animação
    function setAnimate(){
        $animation = '';

        if(get_option('lpw_animate','1') == '1' ){
            $animation = 'animate-jump';
        }elseif(get_option('lpw_animate','2') == '2'){
            $animation = 'animate-zoom';
        }

        return $animation;
    }

    // Definir o lado
    function setPosition(){
        $position = '';

        if(get_option('lpw_local','0') == '0'){
            $position = 'right';
        }elseif(get_option('lpw_local','1') == '1'){
            $position = 'left';
        }

        return $position;
    }

    // Definir o tamanho
    function setSize(){
        if(get_option('lpw_size', '45')){
            $size = '--button-size: '.esc_attr(get_option('lpw_size')).'px';
        }else{
            $size = '--button-size: 45px;';
        }

        return $size;
    }

    // Criando o Banco de Dados
    function settings(){
        add_settings_section('lpw_first_section', null, null, 'lp-wts-link-app');

        // Numero
        add_settings_field('lpw_numero', 'Número do telefone: ', array($this, 'numeroHTML'), 'lp-wts-link-app', 'lpw_first_section');
        register_setting('lpWhatsappButton', 'lpw_numero', array('sanitize_callback' => array($this, 'sanitizeNumero'), 'default' => '0'));

        // Mensagem
        add_settings_field('lpw_mensagem','Mensagem Padrão: ', array($this, 'mensagemHTML'), 'lp-wts-link-app', 'lpw_first_section');
        register_setting('lpWhatsappButton', 'lpw_mensagem', array('sanitize_callback' => array($this, 'sanitizeMensagem'), 'default' => ' '));

        // Ícone
        add_settings_field('lpw_icon', 'Estilo do ícone: ', array($this, 'iconHTML'), 'lp-wts-link-app', 'lpw_first_section');
        register_setting('lpWhatsappButton', 'lpw_icon', array('sanitize_callback' => array($this,'sanitizeIcons'), 'default' => '0'));

        // Tamanho
        add_settings_field('lpw_size', 'Tamanho do botão (px): ', array($this, 'sizeHTML'), 'lp-wts-link-app', 'lpw_first_section');
        register_setting('lpWhatsappButton', 'lpw_size', array('sanitize_callback' => array($this, 'sanitizeNumero'), 'default' => '45'));

        // Posição
        add_settings_field('lpw_local', 'Escolha a posição do botão: ', array($this, 'localHTML'), 'lp-wts-link-app', 'lpw_first_section');
        register_setting('lpWhatsappButton','lpw_local', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));

        // Animação
        add_settings_field('lpw_animate', 'Escolha a animação do botão:', array($this, 'animacaoHTML'), 'lp-wts-link-app', 'lpw_first_section');
        register_setting('lpWhatsappButton', 'lpw_animate', array('sanitize_callback' => array($this, 'sanitizeAnimation'), 'default' =>'0'));
    }

    // Sanitizers

    function sanitizeNumero($input){
        $number = sanitize_text_field($input);
        $safe_number = intval($number);
        if(! $safe_number){
            add_settings_error('lpw_numero', 'lpw_numero_error', 'O Telefone precisa conter apenas número!');
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
            add_settings_error('lpw_local','lpw_local_error', 'Opção inválida, escolha uma opção válida.');
            $input = get_option('lpw_local');
            return $input;
        }
        return $input;
    }

    function sanitizeAnimation($input){
        if($input != '0' && $input != '1' && $input != '2'){
            add_settings_error('lpw_animate','lpw_animate_error', 'Opção inválida, escolha uma opção válida.');
            $input = get_option('lpw_animate');
            return $input;
        }
        return $input;
    }

    function sanitizeIcons($input){
        if($input != '0' && $input != '1' && $input != '2' && $input != '3' && $input != '4' && $input != '5'){
            add_settings_error('lpw_icon','lpw_icon_error', 'Opção inválida, escolha uma opção válida.');
            $input = get_option('lpw_icon');
            return $input;
        }
        return $input;
    }

    // HTMLs

    function numeroHTML(){ ?>
        <input type="text" name="lpw_numero" id="lpw_numero" value="<?php echo esc_attr(get_option('lpw_numero')); ?>" placeholder="55 19 980808080" pattern="[0-9]{0~20}">
        <label for="lpw_numero">O telefone precisa conter o <b>código do país e o DDD</b>.</label>
    <?php
    }

    function mensagemHTML(){ ?>
        <input type="text" name="lpw_mensagem" value="<?php echo esc_attr(get_option('lpw_mensagem')); ?>" placeholder="Digite uma mensagem padrão. (opcional)">
    <?php
    }

    function localHTML(){ ?>
        <select name="lpw_local">
            <option value="0" <?php selected(get_option('lpw_local'), '0'); ?> >Direita</option>
            <option value="1" <?php selected(get_option('lpw_local'), '1'); ?> >Esquerda</option>
        </select>
    <?php
    }

    function animacaoHTML(){ ?>
        <select name="lpw_animate">
            <option value="0" <?php selected(get_option('lpw_animate'), '0'); ?> >Nenhuma</option>
            <option value="1" <?php selected(get_option('lpw_animate'), '1'); ?> >Jump</option>
            <option value="2" <?php selected(get_option('lpw_animate'), '2'); ?> >Zoom</option>
        </select>
    <?php
    }

    function iconHTML(){ ?>
        <select name="lpw_icon">
            <option value="0" <?php selected(get_option('lpw_icon'), '0'); ?> >Style 1</option>
            <option value="1" <?php selected(get_option('lpw_icon'), '1'); ?> >Style 2</option>
            <option value="2" <?php selected(get_option('lpw_icon'), '2'); ?> >Style 3</option>
            <option value="3" <?php selected(get_option('lpw_icon'), '3'); ?> >Style 4</option>
            <option value="4" <?php selected(get_option('lpw_icon'), '4'); ?> >Style 5</option>
            <option value="5" <?php selected(get_option('lpw_icon'), '5'); ?> >Style 6</option>
        </select>
    <?php
    }

    function sizeHTML(){ ?>
        <input type="number" name="lpw_size" value="<?php echo esc_attr(get_option('lpw_size')); ?>" min='10' max='150'>
    <?php
    }

    function lpwMenu(){
        add_menu_page(
            'WtsLink',
            'WtsLink',
            'manage_options',
            'lp-wts-link-app',
            array($this, 'lpwMenuHtml'),
            'dashicons-whatsapp',
            3
        );
    }

    function lpwMenuHtml(){
        ?>
        <div class="wrap">
            <h1>WhatsApp Button</h1>
            <form action="options.php" method="post">
                <?php
                    settings_fields('lpWhatsappButton');
                    do_settings_sections('lp-wts-link-app');
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

$lpWhatsappButton = new LPwhatsButton();

?>