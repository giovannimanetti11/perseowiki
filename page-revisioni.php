<?php
/*
 * Template Name: Reviews page
 */

function create_voti_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'voti';

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            username varchar(60) NOT NULL,
            voto varchar(10) NOT NULL,
            commento text NOT NULL,
            data_ora datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
            update_option('voti_table_created', true);
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>Errore nella creazione della tabella delle revisioni.</p></div>';
            });
        }
    }
}

function check_and_create_voti_table() {
    if (!get_option('voti_table_created')) {
        create_voti_table();
    }
}

function save_votes_and_comments() {
    if (!isset($_POST['submit'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['_wpnonce'], 'save_vote')) {
        return;
    }

    global $wpdb;
    $post_id = intval($_POST['post_id']);
    $voto = isset($_POST['voto']) ? sanitize_text_field($_POST['voto']) : '';
    $commento = isset($_POST['commento']) ? sanitize_text_field($_POST['commento']) : '';
    $username = wp_get_current_user()->user_login;
    $data_ora = current_time('mysql');

    $existingVote = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}voti WHERE post_id = %d AND username = %s", $post_id, $username));

    if ($existingVote > 0) {
        $wpdb->update(
            "{$wpdb->prefix}voti",
            array('voto' => $voto, 'commento' => $commento, 'data_ora' => $data_ora),
            array('post_id' => $post_id, 'username' => $username),
            array('%s', '%s', '%s'),
            array('%d', '%s')
        );
    } else {
        $wpdb->insert(
            "{$wpdb->prefix}voti",
            array(
                'post_id' => $post_id,
                'username' => $username,
                'voto' => $voto,
                'commento' => $commento,
                'data_ora' => $data_ora
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;


}

$submission_success = save_votes_and_comments();

 get_header();

 check_and_create_voti_table();


 if ($submission_success) {
    echo '<div class="notice notice-success is-dismissible"><p>Opinione inviata</p></div>';
 }
 
 if (is_user_logged_in() && (current_user_can('author') || current_user_can('administrator'))) {
     global $wpdb;
     $current_user = wp_get_current_user();
     $posts = get_posts(array('post_type' => 'post', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ));
 
     echo '<table class="reviews-table">';
     echo '<thead>';
     echo '<tr>';
     echo '<th>SCHEDA</th>';
     echo '<th>OPINIONE</th>';
     echo '<th>COMMENTO</th>';
     echo '<th>LE TUE RECENSIONI</th>';
     echo '</tr>';
     echo '</thead>';
     echo '<tbody>';
 
     foreach ($posts as $post) {
         $user_reviews = $wpdb->get_results($wpdb->prepare("SELECT voto, commento, data_ora FROM {$wpdb->prefix}voti WHERE post_id = %d AND username = %s ORDER BY data_ora DESC", $post->ID, $current_user->user_login));
         
         echo '<tr>';
         echo '<td><a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></td>';
         echo '<td>';
         echo '<form method="post" class="vote-form">';
         echo '<input type="radio" name="voto" value="si" required> Sì ';
         echo '<input type="radio" name="voto" value="no" required> No';
         echo '<input type="hidden" name="post_id" value="' . $post->ID . '">';
         echo '</td>';
         echo '<td class="comment-form">';
         echo '<textarea class="commento" name="commento" placeholder="Inserisci un commento..."></textarea>';
         echo '<input type="hidden" name="post_id" value="' . $post->ID . '">';
         wp_nonce_field('save_vote');
         echo '<input class="button" type="submit" name="submit" value="Invia">';
         echo '</form>';
         echo '</td>';
         echo '<td class="user-reviews">';
         if ($user_reviews) {
             echo '<ul>';
             foreach ($user_reviews as $review) {
                 echo '<li>Opinione: ' . esc_html($review->voto) . ' - Commento: ' . esc_html($review->commento) . ' - Data: ' . esc_html($review->data_ora) . '</li>';
             }
             echo '</ul>';
         } else {
             echo 'Nessuna recensione';
         }
         echo '</td>';
         echo '</tr>';
     }
 
     echo '</tbody>';
     echo '</table>';
 } else {
     echo 'Non sei autorizzato a vedere questa pagina.';
 }
 

get_footer();
?>
