<?php
/*
    Plugin Name: Censor Swear Words
    Description: Allows you to censor swear words (or any other words from that matter) from your site and decide how exactly to censor each.
    Version: 1.0
    Author: Simeon Davenport
*/

if (!defined('ABSPATH')) exit;

class CensorSwearWords {
    function __construct() {
        add_action('admin_menu', array($this, 'csw_menu'));
        if (get_option('censored_words')):
            add_filter('the_content', array($this, 'csw_censor_words'));
            add_action('admin_init', array($this, 'csw_options'));
        endif;
    }

    function csw_menu() {
        $cswMenuPageHook = add_menu_page('Censor Swear Words', 'Censor Swear Words', 'manage_options', 'censor-swear-words', array($this, 'csw_page'), 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKIHdpZHRoPSIzMDAuMDAwMDAwcHQiIGhlaWdodD0iMzAwLjAwMDAwMHB0IiB2aWV3Qm94PSIwIDAgMzAwLjAwMDAwMCAzMDAuMDAwMDAwIgogcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQgbWVldCI+CjxtZXRhZGF0YT4KQ3JlYXRlZCBieSBwb3RyYWNlIDEuMTAsIHdyaXR0ZW4gYnkgUGV0ZXIgU2VsaW5nZXIgMjAwMS0yMDExCjwvbWV0YWRhdGE+CjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLDMwMC4wMDAwMDApIHNjYWxlKDAuMTAwMDAwLC0wLjEwMDAwMCkiCmZpbGw9IiM5Q0EyQTciIHN0cm9rZT0ibm9uZSI+CjxwYXRoIGQ9Ik0yMzU3IDIzNTMgYy00IC0zIC03IC00OCAtNyAtOTkgbDAgLTkyIC02NSAtMTIgYy0xODQgLTM1IC0yOTkgLTE1MQotMzEzIC0zMTUgLTE0IC0xODQgMTE1IC0zMzkgMzIzIC0zODYgbDU1IC0xMiAwIC0yNTkgYzAgLTI5MSA3IC0yNzEgLTg0IC0yMzkKLTc0IDI2IC0xMzkgNzIgLTE4NyAxMzQgLTIzIDI5IC00NSA1MyAtNDkgNTUgLTQgMSAtMjggLTIzIC01MyAtNTQgbC00NyAtNTYKMzAgLTQzIGM2MCAtODcgMTg2IC0xNjQgMzA2IC0xODYgMzIgLTcgNjUgLTEzIDcyIC0xNSA4IC0yIDEyIC0zMSAxMiAtOTkgbDAKLTk1IDcwIDAgNzAgMCAwIDk0IDAgOTQgNjkgMTIgYzg5IDE1IDE3OSA1OCAyMzEgMTEwIDgxIDgxIDEyMSAyMTggMTAwIDM0MAotMjcgMTU1IC0xMjQgMjQ4IC0zMjAgMzA2IGwtNzUgMjIgLTMgMjMxIC0yIDIzMSAzMiAtNiBjNTQgLTEwIDEzMyAtNDkgMTc1Ci04OCAyMiAtMjAgNDcgLTM2IDU1IC0zNiA3IDAgMzEgMjMgNTIgNTEgbDM4IDUxIC0zOSA0MCBjLTUxIDUyIC0xNTUgMTAzCi0yNDIgMTE4IGwtNzEgMTIgMCA5OSAwIDk5IC02MyAwIGMtMzUgMCAtNjcgLTMgLTcwIC03eiBtLTcgLTU0OCBjMCAtMTE4IC0yCi0yMTUgLTUgLTIxNSAtMjAgMCAtMTIyIDQ3IC0xNDcgNjggLTU4IDQ5IC04NSAxNTUgLTU3IDIyOSAyNSA2NiAxMTIgMTI1IDE5NwoxMzIgOSAxIDEyIC00OCAxMiAtMjE0eiBtMjQzIC00NDMgYzEwNCAtNDUgMTQzIC0xMTQgMTM1IC0yMzQgLTQgLTU2IC0xMSAtNzgKLTM0IC0xMTEgLTMzIC00NiAtMTAxIC04NiAtMTYzIC05NCBsLTQxIC02IDAgMjQyIDAgMjQyIDIzIC02IGMxMiAtNCA0OCAtMTkKODAgLTMzeiIvPgo8cGF0aCBkPSJNNDQ1IDIxMTggYy0zIC0xMyAtMTYgLTkzIC0zMCAtMTc4IC0xNCAtODUgLTI3IC0xNjUgLTMwIC0xNzcgLTUKLTIyIC05IC0yMyAtMTI1IC0yMyBsLTEyMCAwIDAgLTYwIDAgLTYwIDExMCAwIGM2MSAwIDExMCAtMiAxMTAgLTUgMCAtMiAtMTEKLTY5IC0yNSAtMTQ3IC0xNCAtNzggLTI1IC0xNDYgLTI1IC0xNTAgMCAtNSAtNDkgLTggLTExMCAtOCBsLTExMCAwIDAgLTYwIDAKLTYwIDEwMCAwIGM5OSAwIDEwMCAwIDk1IC0yMiAtMyAtMTMgLTE2IC05MyAtMzAgLTE3OCAtMTUgLTg1IC0yOSAtMTY1IC0zMgotMTc3IC01IC0yMiAtMyAtMjMgNjUgLTIzIGw3MCAwIDEwIDU4IGM1IDMxIDIwIDEyMSAzMyAyMDAgbDIzIDE0MiAxNTMgMApjMTM5IDAgMTUzIC0yIDE1MyAtMTcgMCAtMTAgLTE0IC05NyAtMzAgLTE5MyAtMTYgLTk2IC0zMCAtMTc4IC0zMCAtMTgyIDAgLTUKMzEgLTggNjggLTggbDY4IDAgMjMgMTQzIGMxMyA3OCAyOCAxNjggMzMgMjAwIGwxMCA1NyAxMjQgMCAxMjQgMCAwIDYwIDAgNjAKLTExMCAwIC0xMTAgMCAxIDI4IGMwIDE1IDEwIDgzIDIyIDE1MiBsMjIgMTI1IDExMyAzIDExMiAzIDAgNTkgMCA2MCAtMTAwIDAKYy03MyAwIC0xMDAgMyAtMTAwIDEzIDAgNiAxNCA5MSAzMCAxODcgMTYgOTYgMzAgMTgxIDMwIDE4OCAwIDggLTIwIDEyIC02OAoxMiBsLTY4IDAgLTIzIC0xNDIgYy0xMyAtNzkgLTI4IC0xNjkgLTMzIC0yMDAgbC0xMCAtNTggLTE1NCAwIGMtMTE3IDAgLTE1NAozIC0xNTQgMTMgMSA2IDE2IDk2IDM0IDIwMCBsMzMgMTg3IC02OSAwIGMtNjIgMCAtNjggLTIgLTczIC0yMnogbTM1NyAtNTAzCmM1IC01IC0zNiAtMjY2IC00NiAtMjkyIC03IC0xNyAtMzA2IC0xOSAtMzA2IC0yIDAgMTMgMzggMjQyIDQ1IDI3NyA1IDIyIDgKMjIgMTU0IDIyIDgxIDAgMTUwIC0yIDE1MyAtNXoiLz4KPHBhdGggZD0iTTE0NzAgMTY5MCBsMCAtNDUwIDc1IDAgNzUgMCAwIDQ1MCAwIDQ1MCAtNzUgMCAtNzUgMCAwIC00NTB6Ii8+CjxwYXRoIGQ9Ik0xNDkyIDk4NSBjLTQwIC0xOCAtNjYgLTczIC01OCAtMTIwIDkgLTQ4IDQ3IC03NyAxMDUgLTgzIDQ3IC00IDUzCi0yIDg2IDMxIDI5IDI5IDM1IDQyIDM1IDc2IDAgODMgLTg3IDEzMyAtMTY4IDk2eiIvPgo8L2c+Cjwvc3ZnPg==', 70);
        add_submenu_page('censor-swear-words', 'Censored Words', 'Censored Words', 'manage_options', 'censor-swear-words', array($this, 'csw_page'));
        add_submenu_page('censor-swear-words', 'Censor Swear Words Settings', 'Settings', 'manage_options', 'censor-swear-words-settings', array($this, 'csw_settings'));
        add_action("load-{$cswMenuPageHook}", array($this, 'csw_page_css'));
    }

    function csw_page() { ?>
        <div class="wrap">
            <h1>Censor Swear Words</h1>
            <?php if (isset($_POST['submitted']) == "true") $this->handleForm() ?>
            <form method="POST">
                <input type="hidden" name="submitted" value="true">
                <?php wp_nonce_field('save_censored_words', 'censored_words_nonce'); ?>
                <label for="wordsToCensor"><p>Enter a comma-separated list of words to censor from your site.</p></label>
                <div class="csw__flex-container">
                    <textarea name="wordsToCensor" id="wordsToCensor" placeholder="bad, stupid, horrible"><?php echo esc_textarea(get_option('censored_words')); ?></textarea>
                </div>
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </form>
        </div>
    <?php }

    function csw_settings() { ?>
        <div class="wrap">
            <h1>Censor Swear Words Settings</h1>
            <form action="options.php" method="POST">
                <?php settings_fields('csw_group');
                do_settings_sections('censor-swear-words-settings');
                submit_button(); ?>
            </form>
        </div>
    <?php }

    function csw_page_css() {
        wp_enqueue_style('csw-page-css', plugin_dir_url(__FILE__) . 'styles.css');
    }

    function handleForm() {
        if (isset($_POST['censored_words_nonce']) && wp_verify_nonce($_POST['censored_words_nonce'], 'save_censored_words') && current_user_can('manage_options')):
            update_option('censored_words', sanitize_text_field($_POST['wordsToCensor'])); ?>
            <div class="updated">
                <p>Your censored words have been saved.</p>
            </div>
        <?php else: ?>
            <div class="error">
                <p>Sorry, you do not have permission to do this.</p>
            </div>
        <?php endif;
    }

    function csw_censor_words($content) {
        //$wordsToCensor = $this->getWordsToCensor();
        //str_ireplace('bad', $content);
    }

    function csw_options() {
        add_settings_section('censor_words_section', 'Set how each censored word should be censored.', null, 'censor-swear-words-settings');
        $wordsToCensor = $this->getWordsToCensor();
        foreach ($wordsToCensor as $word):
            $newOptionName = 'csw_' . $word . '_replace';
            add_settings_field($newOptionName, $word, array($this, 'csw_word_replace'), 'censor-swear-words-settings', 'censor_words_section', array('optionName' => $newOptionName));
            register_setting('csw_group', $newOptionName, array(
                'sanitize_callback' => 'sanitize_text_field',
                'default' => str_repeat('*', strlen($word))
            ));
        endforeach;
    }

    function getWordsToCensor() {
        return $wordsToCensor = array_map('trim', explode(',', get_option('censored_words')));
    }

    function csw_word_replace($param) { ?>
        <input type="text" name="<?php echo $param['optionName']; ?>"></input>
    <?php }
}

$censorSwearWords = new CensorSwearWords();